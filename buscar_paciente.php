<?php
session_start();
$medico_nombre = $_SESSION['medico_nombre'] ?? 'Dr. SaludProactiva';

function colorRiesgo($nivel) {
    switch(strtolower($nivel)){
        case 'alto': return '#dc3545';
        case 'medio': return '#ffc107';
        case 'bajo': return '#198754';
        default: return '#6c757d';
    }
}

// Conexión a base de datos
$conexion = new mysqli("localhost", "root", "", "saludproactiva");
if ($conexion->connect_error) die("Error de conexión: " . $conexion->connect_error);

// Traer pacientes
$result = $conexion->query("SELECT id, nombre, apellido, dni FROM pacientes");
$pacientes = [];
while($row = $result->fetch_assoc()){
    $dni = $row['dni'];
    // Controles
    $controles = [];
    $resC = $conexion->query("SELECT tipo, ultimo, proximo, riesgo_actual, riesgo_futuro, recordatorio_enviado, fecha_recordatorio FROM controles WHERE dni='$dni'");
    while($c = $resC->fetch_assoc()) $controles[] = $c;

    // Enfermedades (solo info textual, sin parámetros)
    $enfermedades = [];
    $resE = $conexion->query("SELECT nombre, riesgo_actual_nivel, riesgo_futuro_nivel, descripcion, frecuencia, referencia, prediccion_complicaciones FROM enfermedades WHERE dni='$dni'");
    while($e = $resE->fetch_assoc()){
        $enfermedades[$e['nombre']] = [
            'riesgo_actual'=>['nivel'=>$e['riesgo_actual_nivel'],'escala'=>'','justificacion'=>''],
            'riesgo_futuro'=>['nivel'=>$e['riesgo_futuro_nivel'],'escala'=>'','justificacion'=>''],
            'descripcion'=>$e['descripcion'],
            'frecuencia'=>$e['frecuencia'],
            'referencia'=>$e['referencia'],
            'prediccion_complicaciones'=>$e['prediccion_complicaciones'],
            'parametros'=>[], // vacío, no genera gráfico
            'meses'=>[],
            'objetivo'=>[]
        ];
    }

    $pacientes[] = [
        'dni'=>$dni,
        'nombre'=>$row['nombre'],
        'apellido'=>$row['apellido'],
        'habitos'=>['Ejercicio 150 min/semana','Controlar peso mensual'],
        'medicacion'=>['Enalapril 10mg','Metformina 500mg'],
        'enfermedades'=>$enfermedades,
        'controles'=>$controles,
        'notas_medico'=>[],
        'estudios_pendientes'=>[]
    ];
}
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Riesgo de Pacientes | SaludProactiva</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.navbar { background-color: #212529; padding: 0.2rem 1rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #fff; border-radius:5px; margin-right:0.5rem; padding:2px 10px; font-size:0.9rem;}
.navbar .btn-menu:hover { background-color: #6c757d; }
.navbar-text { color:#fff; background-color: rgba(255,255,255,0.1); padding:2px 6px; border-radius:5px; font-size:0.9rem;}
.btn-salir { background-color:#dc3545; color:#fff; border:none; padding:2px 10px; font-size:0.9rem;}
.btn-salir:hover { background-color:#b02a37; }
.data-section { border:1px solid #dee2e6; padding:15px; margin-top:15px; border-radius:5px; background:#f8f9fa;}
.data-section h5 { margin-top:10px; }
textarea { width:100%; min-height:60px; margin-bottom:10px; }
footer { background-color:#212529; color:#fff; text-align:center; padding:12px 0; }
footer a { color:#0d6efd; text-decoration:none; font-weight:bold; }
footer a:hover { text-decoration:underline; }
.tooltip-custom { cursor:pointer; text-decoration:underline dotted; }
.list-group-item-action { cursor:pointer; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php"><img src="icons/logo.png" alt="Logo"></a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="btn btn-menu" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="vademecum.php">Vademécum</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="buscar_paciente.php">Pacientes</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($medico_nombre) ?></span></li>
        <li class="nav-item"><a class="btn btn-salir btn-sm" href="logout.php">Salir</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-4">
    <h3 class="mb-3">Riesgo y Controles de Pacientes</h3>
    
    <!-- Buscador por nombre/apellido -->
    <div class="mb-3">
        <label for="buscarPaciente">Buscar paciente:</label>
        <input type="text" id="buscarPaciente" class="form-control" placeholder="Escriba nombre o apellido">
        <div id="listaPacientes" class="list-group mt-1" style="max-height:200px; overflow-y:auto;"></div>
    </div>

    <!-- Select desplegable -->
    <div class="mb-3">
        <label for="selectPaciente">O seleccione paciente:</label>
        <select id="selectPaciente" class="form-select"></select>
    </div>

    <div id="datosPaciente"></div>
</div>

<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script>
let pacientesData = <?php echo json_encode($pacientes); ?>;

const selectPaciente = document.getElementById('selectPaciente');
const datosPaciente = document.getElementById('datosPaciente');
const buscarInput = document.getElementById('buscarPaciente');
const listaPacientes = document.getElementById('listaPacientes');

function colorRiesgo(nivel){
    switch(nivel){
        case 'alto': return '#dc3545';
        case 'medio': return '#ffc107';
        case 'bajo': return '#198754';
        default: return '#6c757d';
    }
}

// Guardar nota
function guardarNota(pacienteIndex){
    const textarea = document.getElementById('nota-medico');
    const nota = textarea.value.trim();
    if(nota){
        const now = new Date().toLocaleString();
        pacientesData[pacienteIndex].notas_medico.push({nota, fecha: now});
        localStorage.setItem('pacientesData', JSON.stringify(pacientesData));
        textarea.value='';
        renderPaciente();
        alert(`Nota guardada el ${now}`);
    }
}

// Recordatorios y estudios
function enviarRecordatorio(pacienteIndex, controlIndex){
    const now = new Date().toLocaleString();
    pacientesData[pacienteIndex].controles[controlIndex].recordatorio_enviado = true;
    pacientesData[pacienteIndex].controles[controlIndex].fecha_recordatorio = now;
    localStorage.setItem('pacientesData', JSON.stringify(pacientesData));
    renderPaciente();
    alert(`Recordatorio enviado automáticamente al paciente el ${now}.`);
}

function solicitarEstudio(pacienteIndex, estudioIndex){
    const now = new Date().toLocaleString();
    pacientesData[pacienteIndex].estudios_pendientes[estudioIndex].fecha_solicitud = now;
    localStorage.setItem('pacientesData', JSON.stringify(pacientesData));
    renderPaciente();
    alert(`Estudio ${pacientesData[pacienteIndex].estudios_pendientes[estudioIndex].nombre} solicitado el ${now}.`);
}

function renderPaciente(){
    const pacienteIndex=selectPaciente.value;
    if(pacienteIndex==='') return;
    const paciente=pacientesData[pacienteIndex];
    datosPaciente.innerHTML='';

    paciente.enfermedades && Object.keys(paciente.enfermedades).forEach(enf=>{
        const data=paciente.enfermedades[enf];
        const div=document.createElement('div');
        div.className='data-section';

        let riesgoHTML=`<p><b>Riesgo Actual:</b> <span style="color:${colorRiesgo(data.riesgo_actual.nivel)}">${data.riesgo_actual.nivel}</span> | 
        <b>Riesgo Futuro:</b> <span style="color:${colorRiesgo(data.riesgo_futuro.nivel)}">${data.riesgo_futuro.nivel}</span></p>
        <p><b>Frecuencia sugerida:</b> ${data.frecuencia}</p>`;

        let estudiosHTML='';
        paciente.estudios_pendientes.forEach((e,i)=>{
            const fecha = e.fecha_solicitud ? ` (Solicitado: ${e.fecha_solicitud})` : '';
            estudiosHTML+=`<button class="btn btn-sm btn-outline-primary me-1 mb-1" title="Estado: ${e.estado}, Prioridad: ${e.prioridad}" onclick="solicitarEstudio(${pacienteIndex},${i})">Solicitar ${e.nombre}</button>${fecha}<br>`;
        });

        let controlesHTML='';
        const hoy=new Date();
        paciente.controles.forEach((c,i)=>{
            const proximoDate=new Date(c.proximo);
            let alerta='';
            if(proximoDate<hoy){
                const dias=Math.floor((hoy-proximoDate)/(1000*60*60*24));
                alerta=` ⚠️ Vencido hace ${dias} días`;
            }
            const recordIcon=c.recordatorio_enviado?' ✅':'';
            const fechaRec = c.fecha_recordatorio ? ` (Enviado: ${c.fecha_recordatorio})` : '';
            controlesHTML+=`<div>Último: ${c.ultimo}<br>Próximo: ${c.proximo} (${c.tipo})${alerta}<br>
            <button class="btn btn-sm btn-success mt-1" onclick="enviarRecordatorio(${pacienteIndex},${i})">Recordatorio${recordIcon}</button>${fechaRec}</div>`;
        });

        let notasHTML='';
        if(paciente.notas_medico.length>0){
            notasHTML='<ul>';
            paciente.notas_medico.forEach(n=>{ notasHTML+=`<li>${n.fecha}: ${n.nota}</li>`; });
            notasHTML+='</ul>';
        }

        div.innerHTML=`<h5>${enf}</h5>${riesgoHTML}<p>${data.descripcion}<br><b>Referencia:</b> ${data.referencia}</p>
        <div><b>Estudios Pendientes:</b><br>${estudiosHTML}</div>
        <div><b>Controles:</b><br>${controlesHTML}</div>
        <div class="mt-2"><b>Notas del médico:</b><br>${notasHTML}
            <textarea id="nota-medico" placeholder="Escribir nota..."></textarea>
            <button class="btn btn-sm btn-primary mb-2" onclick="guardarNota(${pacienteIndex})">Guardar nota</button>
        </div>
        <div><b>Predicción de complicaciones:</b> ${data.prediccion_complicaciones}</div>
        <div><b>Recomendaciones de hábitos:</b><br>${paciente.habitos.join('<br>')}</div>
        <div><b>Medicamentos:</b><br>${paciente.medicacion.join('<br>')}</div>`;

        datosPaciente.appendChild(div);
    });
}

// Cargar pacientes en select
pacientesData.forEach((p,index)=>{
    const opt=document.createElement('option');
    opt.value=index;
    opt.textContent=`${p.nombre} ${p.apellido}`;
    selectPaciente.appendChild(opt);
});

// Event listener select
selectPaciente.addEventListener('change', renderPaciente);

// Función de búsqueda
function mostrarListaPacientes(filtro){
    listaPacientes.innerHTML = '';
    pacientesData.forEach((p,index)=>{
        const nombreCompleto = `${p.nombre} ${p.apellido}`;
        if(nombreCompleto.toLowerCase().includes(filtro.toLowerCase())){
            const item = document.createElement('button');
            item.className = 'list-group-item list-group-item-action';
            item.textContent = nombreCompleto;
            item.onclick = () => {
                selectPaciente.value = index;
                renderPaciente();
                listaPacientes.innerHTML = '';
                buscarInput.value = nombreCompleto;
            };
            listaPacientes.appendChild(item);
        }
    });
}

buscarInput.addEventListener('input', (e) => {
    mostrarListaPacientes(e.target.value);
});

renderPaciente();
</script>
</body>
</html>













































