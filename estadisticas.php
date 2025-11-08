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

$pacientes = [
    [
        'dni' => '9123456',
        'nombre' => 'Salvador',
        'apellido' => 'Dali',
        'edad' => 71,
        'medicacion' => ['Enalapril 10mg', 'Metformina 500mg'],
        'habitos' => ['Reducir sal a <2g/día', 'Ejercicio 150 min/semana', 'Controlar peso mensual'],
        'enfermedades' => [
            'Hipertensión' => [
                'riesgo_actual' => ['nivel'=>'alto','escala'=>'Framingham','justificacion'=>'Hipertensión mal controlada'],
                'riesgo_futuro' => ['nivel'=>'alto','escala'=>'Framingham','justificacion'=>'Riesgo cardiovascular alto según Framingham'],
                'descripcion' => 'Presión arterial sostenida ≥140/90 mmHg',
                'frecuencia' => 'Control mensual',
                'parametros' => ['Presión sistólica' => [130, 135, 140, 145, 150]],
                'objetivo' => ['Presión sistólica'=>120],
                'meses' => ['May','Jun','Jul','Ago','Sep'],
                'referencia' => 'AHA 2017',
                'prediccion_complicaciones' => '25% riesgo cardiovascular 1 año'
            ],
            'Diabetes' => [
                'riesgo_actual' => ['nivel'=>'medio','escala'=>'ADA','justificacion'=>'HbA1c alta, riesgo microvascular'],
                'riesgo_futuro' => ['nivel'=>'alto','escala'=>'ADA','justificacion'=>'Riesgo microvascular elevado'],
                'descripcion' => 'HbA1c 8%',
                'frecuencia' => 'Control cada 3 meses',
                'parametros' => ['HbA1c' => [7.5, 8.0, 8.2, 8.5, 9.0],'Glucemia' => [95, 110, 115, 120, 125]],
                'objetivo' => ['HbA1c'=>7, 'Glucemia'=>100],
                'meses' => ['May','Jun','Jul','Ago','Sep'],
                'referencia' => 'ADA 2025',
                'prediccion_complicaciones' => 'Riesgo neuropatía diabética: medio'
            ]
        ],
        'estudios_pendientes' => [
            ['nombre' => 'Electrocardiograma', 'estado' => 'Pendiente', 'prioridad' => 'Alta', 'fecha_solicitud' => null],
            ['nombre' => 'Perfil lipídico', 'estado' => 'Realizado', 'prioridad' => 'Media', 'fecha_solicitud' => null]
        ],
        'controles' => [
            ['ultimo' => '2025-08-20', 'proximo' => '2025-09-10', 'tipo' => 'Chequeo general', 'recordatorio_enviado' => false, 'fecha_recordatorio' => null],
            ['ultimo' => '2025-07-15', 'proximo' => '2025-08-15', 'tipo' => 'Laboratorio', 'recordatorio_enviado' => false, 'fecha_recordatorio' => null]
        ],
        'notas_medico' => [],
        'proximos_pasos' => []
    ]
];
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
.chart-container { display:flex; flex-wrap:wrap; gap:20px; align-items:flex-start; }
.chart-main { flex:1.5; min-width:250px; height:300px; } 
.chart-sidebar { flex:1; min-width:180px; }
textarea { width:100%; min-height:60px; margin-bottom:10px; }
footer { background-color:#212529; color:#fff; text-align:center; padding:12px 0; }
footer a { color:#0d6efd; text-decoration:none; font-weight:bold; }
footer a:hover { text-decoration:underline; }
.tooltip-custom { cursor:pointer; text-decoration:underline dotted; }
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
    <div class="mb-3">
        <label for="selectPaciente">Seleccione paciente:</label>
        <select id="selectPaciente" class="form-select"></select>
    </div>
    <div id="datosPaciente"></div>
</div>

<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script>
let pacientesData = <?php echo json_encode($pacientes); ?>;

// Cargar datos guardados de localStorage
const storedData = localStorage.getItem('pacientesData');
if(storedData){
    const parsed = JSON.parse(storedData);
    parsed.forEach((p, i)=>{
        pacientesData[i].notas_medico = p.notas_medico || [];
        pacientesData[i].proximos_pasos = p.proximos_pasos || [];
        pacientesData[i].controles = p.controles || pacientesData[i].controles;
        pacientesData[i].estudios_pendientes = p.estudios_pendientes || pacientesData[i].estudios_pendientes;
    });
}

const selectPaciente = document.getElementById('selectPaciente');
const datosPaciente = document.getElementById('datosPaciente');

function colorRiesgo(nivel){
    switch(nivel){
        case 'alto': return '#dc3545';
        case 'medio': return '#ffc107';
        case 'bajo': return '#198754';
        default: return '#6c757d';
    }
}

// Guardar en localStorage
function guardarLocalStorage(){
    localStorage.setItem('pacientesData', JSON.stringify(pacientesData));
}

// Recordatorios y estudios
function enviarRecordatorio(pacienteIndex, controlIndex){
    const now = new Date().toLocaleString();
    pacientesData[pacienteIndex].controles[controlIndex].recordatorio_enviado = true;
    pacientesData[pacienteIndex].controles[controlIndex].fecha_recordatorio = now;
    guardarLocalStorage();
    renderPaciente();
    alert(`Recordatorio enviado automáticamente al paciente el ${now}.`);
}

function solicitarEstudio(pacienteIndex, estudioIndex){
    const now = new Date().toLocaleString();
    pacientesData[pacienteIndex].estudios_pendientes[estudioIndex].fecha_solicitud = now;
    guardarLocalStorage();
    renderPaciente();
    alert(`Estudio ${pacientesData[pacienteIndex].estudios_pendientes[estudioIndex].nombre} solicitado el ${now}.`);
}

// Guardar nota
function guardarNota(pacienteIndex){
    const textarea = document.getElementById('nota-medico');
    const nota = textarea.value.trim();
    if(nota){
        const now = new Date().toLocaleString();
        pacientesData[pacienteIndex].notas_medico.push({nota, fecha: now});
        guardarLocalStorage();
        textarea.value='';
        renderPaciente();
        alert(`Nota guardada el ${now}`);
    }
}

// Interpolación para gráficos
function interpolarDatos(valores, total){
    const resultado=[];
    const n=valores.length;
    for(let i=0;i<total;i++){
        const pos=i*(n-1)/(total-1);
        const low=Math.floor(pos);
        const high=Math.ceil(pos);
        const peso=pos-low;
        if(high>=n) resultado.push(valores[n-1]);
        else resultado.push(valores[low]+(valores[high]-valores[low])*peso);
    }
    return resultado;
}

function renderPaciente(){
    const pacienteIndex=selectPaciente.value;
    const paciente=pacientesData[pacienteIndex];
    datosPaciente.innerHTML='';

    paciente.enfermedades && Object.keys(paciente.enfermedades).forEach(enf=>{
        const data=paciente.enfermedades[enf];
        const div=document.createElement('div');
        div.className='data-section';

        let riesgoHTML=`<p><b>Riesgo Actual:</b> <span style="color:${colorRiesgo(data.riesgo_actual.nivel)}" class="tooltip-custom" title="${data.riesgo_actual.justificacion}">${data.riesgo_actual.nivel} (${data.riesgo_actual.escala})</span> | 
        <b>Riesgo Futuro:</b> <span style="color:${colorRiesgo(data.riesgo_futuro.nivel)}" class="tooltip-custom" title="${data.riesgo_futuro.justificacion}">${data.riesgo_futuro.nivel} (${data.riesgo_futuro.escala})</span></p>
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

        // Notas
        let notasHTML='';
        if(paciente.notas_medico.length>0){
            notasHTML='<ul>';
            paciente.notas_medico.forEach(n=>{ notasHTML+=`<li>${n.fecha}: ${n.nota}</li>`; });
            notasHTML+='</ul>';
        }

        // Próximos pasos
        let pasosHTML='';
        if(paciente.proximos_pasos.length>0){
            pasosHTML='<ul>';
            paciente.proximos_pasos.forEach(p=>{ pasosHTML+=`<li>${p}</li>`; });
            pasosHTML+='</ul>';
        }

        div.innerHTML=`<h5>${enf}</h5>${riesgoHTML}<p>${data.descripcion}<br><b>Referencia:</b> ${data.referencia}</p>
        <div class="chart-container">
            <canvas id="chart-${enf}" class="chart-main"></canvas>
            <div class="chart-sidebar">
                <p><b>Predicción de complicaciones:</b> ${data.prediccion_complicaciones}</p>
                <p><b>Recomendaciones de hábitos:</b><br>${paciente.habitos.join('<br>')}</p>
                <p><b>Medicamentos:</b><br>${paciente.medicacion.join('<br>')}</p>
            </div>
        </div>
        <div><b>Estudios Pendientes:</b><br>${estudiosHTML}</div>
        <div><b>Controles:</b><br>${controlesHTML}</div>
        <div class="mt-2"><b>Notas del médico:</b><br>${notasHTML}
            <textarea id="nota-medico" placeholder="Escribir nota..."></textarea>
            <button class="btn btn-sm btn-primary mb-2" onclick="guardarNota(${pacienteIndex})">Guardar nota</button>
        </div>
        <div><b>Próximos pasos:</b><br>${pasosHTML}</div>`;

        datosPaciente.appendChild(div);

        const ctx=document.getElementById(`chart-${enf}`).getContext('2d');
        new Chart(ctx,{
            type:'line',
            data:{
                labels:data.meses,
                datasets:Object.keys(data.parametros).map((param,index)=>{
                    const colores=['rgb(75,192,192)','rgb(255,99,132)','rgb(54,162,235)','rgb(255,206,86)'];
                    const valoresInterpolados=interpolarDatos(data.parametros[param], data.meses.length);
                    const objetivo=data.objetivo[param];
                    return {
                        label:param,
                        data:valoresInterpolados,
                        borderColor:colores[index%colores.length],
                        fill:false,
                        tension:0.4,
                        pointRadius:4,
                        pointBackgroundColor:colores[index%colores.length],
                        segment:{
                            borderColor: ctx => ctx.p1.parsed.y>objetivo ? '#dc3545' : colores[index%colores.length]
                        }
                    };
                })
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                plugins:{legend:{display:true, position:'bottom'}, title:{display:true, text:'Evolución del parámetro'}},
                scales:{x:{title:{display:true,text:'Meses'}},y:{title:{display:true,text:'Valor del parámetro'}}}
            }
        });
    });
}

// Cargar pacientes en select
pacientesData.forEach((p,index)=>{
    const opt=document.createElement('option');
    opt.value=index;
    opt.textContent=`${p.nombre} ${p.apellido}`;
    selectPaciente.appendChild(opt);
});
selectPaciente.addEventListener('change', renderPaciente);
renderPaciente();
</script>
</body>
</html>






