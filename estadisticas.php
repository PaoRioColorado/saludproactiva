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

// Datos de ejemplo con más pacientes
$pacientes = [
    [
        'dni' => '9123456',
        'nombre' => 'Salvador',
        'apellido' => 'Dali',
        'edad' => 71,
        'enfermedades' => [
            'Hipertensión' => [
                'riesgo_actual' => 'alto',
                'riesgo_futuro' => 'alto',
                'descripcion' => 'Presión arterial sostenida ≥140/90 mmHg',
                'frecuencia' => 'Control mensual',
                'parametros' => ['Presión sistólica' => [130, 135, 140, 145, 150]],
                'referencia' => 'AHA 2017'
            ],
            'Diabetes' => [
                'riesgo_actual' => 'medio',
                'riesgo_futuro' => 'alto',
                'descripcion' => 'HbA1c 8%',
                'frecuencia' => 'Control cada 3 meses',
                'parametros' => ['HbA1c' => [7.5, 8.0, 8.2, 8.5, 9.0]],
                'referencia' => 'ADA 2025'
            ]
        ],
        'estudios_pendientes' => [
            ['nombre' => 'Electrocardiograma', 'estado' => 'Pendiente', 'prioridad' => 'Alta'],
            ['nombre' => 'Perfil lipídico', 'estado' => 'Pendiente', 'prioridad' => 'Media']
        ],
        'controles' => [
            ['ultimo' => '2025-08-20', 'proximo' => '2025-09-10', 'tipo' => 'Chequeo general', 'recordatorio_enviado' => '']
        ]
    ],
    [
        'dni' => '10123456',
        'nombre' => 'Pablo',
        'apellido' => 'Picasso',
        'edad' => 44,
        'enfermedades' => [
            'Hipertensión' => [
                'riesgo_actual' => 'medio',
                'riesgo_futuro' => 'medio',
                'descripcion' => 'Presión arterial 130/85 mmHg',
                'frecuencia' => 'Control cada 2 meses',
                'parametros' => ['Presión sistólica' => [120, 125, 130, 132, 135]],
                'referencia' => 'AHA 2017'
            ]
        ],
        'estudios_pendientes' => [
            ['nombre' => 'Ecografía abdominal', 'estado' => 'Pendiente', 'prioridad' => 'Media']
        ],
        'controles' => [
            ['ultimo' => '2025-09-01', 'proximo' => '2025-09-25', 'tipo' => 'Chequeo general', 'recordatorio_enviado' => '']
        ]
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
.navbar .btn-menu { background-color: #212529; color: #fff; border: 1px solid #212529; border-radius: 5px; margin-right: 0.5rem; padding: 2px 10px; font-size: 0.9rem; }
.navbar .btn-menu:hover { background-color: #6c757d; color: #fff; transform: translateY(-2px); }
.navbar-text { color: #fff; background-color: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 5px; margin-right: 0.5rem; font-weight: 500; font-size: 0.9rem;}
.btn-salir { background-color: #dc3545; color: #fff; border: none; padding: 2px 10px; font-size: 0.9rem;}
.btn-salir:hover { background-color: #b02a37; }
.table td, .table th { vertical-align: middle; text-align: center; }
.small-chart { width: 300px !important; height: 150px !important; }
footer { background-color: #212529; color: #fff; text-align: center; padding: 12px 0; }
footer a { color: #0d6efd; text-decoration: none; font-weight: bold; }
footer a:hover { text-decoration: underline; }
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
        <select id="selectPaciente" class="form-select">
            <?php foreach($pacientes as $index => $p): ?>
                <option value="<?= $index ?>"><?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="card p-3 paciente-card">
        <label>Seleccione enfermedad: 
            <select class="form-select form-select-sm select-enfermedad"></select>
        </label>

        <table class="table table-striped table-hover mt-3 align-middle">
            <thead class="table-dark text-uppercase">
                <tr>
                    <th>Riesgo Actual</th>
                    <th>Riesgo Futuro</th>
                    <th>Base clínica / Frecuencia</th>
                    <th>Evolución del parámetro</th>
                    <th>Estudios Pendientes</th>
                    <th>Controles</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="riesgo-actual" style="color:#fff;"></td>
                    <td class="riesgo-futuro" style="color:#fff;"></td>
                    <td class="base-frecuencia" style="text-align:left;"></td>
                    <td><canvas class="small-chart"></canvas></td>
                    <td class="estudios" style="text-align:left;"></td>
                    <td class="controles" style="text-align:left;"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const pacientesData = <?php echo json_encode($pacientes); ?>;
const selectPaciente = document.getElementById('selectPaciente');
const card = document.querySelector('.paciente-card');
const selectEnfermedad = card.querySelector('.select-enfermedad');
const row = card.querySelector('tbody tr');
const canvas = row.querySelector('canvas');
let chart;

function enviarRecordatorio(controlIndex){
    const now = new Date();
    const fecha = now.toLocaleString();
    const pacienteIndex = selectPaciente.value;
    pacientesData[pacienteIndex].controles[controlIndex].recordatorio_enviado = fecha;
    renderEnfermedad();
    alert(`Recordatorio enviado al paciente el ${fecha}`);
}

function renderPaciente(){
    const paciente = pacientesData[selectPaciente.value];
    selectEnfermedad.innerHTML = '';
    Object.keys(paciente.enfermedades).forEach((enf,index) => {
        const opt = document.createElement('option');
        opt.value = enf; opt.textContent = enf;
        selectEnfermedad.appendChild(opt);
    });
    renderEnfermedad();
}

function renderEnfermedad(){
    const paciente = pacientesData[selectPaciente.value];
    const enf = selectEnfermedad.value;
    const data = paciente.enfermedades[enf];

    row.querySelector('.riesgo-actual').innerHTML = `<b>${data.riesgo_actual}</b><br>${data.descripcion}<br><small>Control: ${data.frecuencia}</small>`;
    row.querySelector('.riesgo-actual').style.backgroundColor = color(data.riesgo_actual);

    row.querySelector('.riesgo-futuro').innerHTML = `<b>${data.riesgo_futuro}</b><br>${data.descripcion}<br><small>Control: ${data.frecuencia}</small>`;
    row.querySelector('.riesgo-futuro').style.backgroundColor = color(data.riesgo_futuro);

    row.querySelector('.base-frecuencia').innerHTML = `<b>${data.descripcion}</b><br>Referencia: ${data.referencia}<br>Frecuencia: ${data.frecuencia}`;

    // Evolución parámetros
    row.querySelector('.estudios').innerHTML = '';
    paciente.estudios_pendientes.forEach((e,i)=>{
        const btn = document.createElement('button');
        btn.textContent = `Solicitar ${e.nombre}`;
        btn.className='btn btn-sm btn-outline-primary me-1 mb-1';
        btn.title = `Estado: ${e.estado}, Prioridad: ${e.prioridad}`;
        btn.onclick = ()=>alert(`Solicitando estudio: ${e.nombre}`);
        row.querySelector('.estudios').appendChild(btn);
    });

    // Controles
    row.querySelector('.controles').innerHTML = '';
    const hoy = new Date();
    paciente.controles.forEach((c,i)=>{
        const proximoDate = new Date(c.proximo);
        const div = document.createElement('div');
        let alerta='';
        if(proximoDate < hoy){
            const dias = Math.floor((hoy - proximoDate)/(1000*60*60*24));
            alerta = ` ⚠️ Vencido hace ${dias} días`;
            div.style.backgroundColor='#f8d7da';
            div.style.padding='4px';
            div.style.borderRadius='4px';
        }
        div.innerHTML = `Último: ${c.ultimo}<br>Próximo: ${c.proximo} (${c.tipo})${alerta}<br>`;
        const btn = document.createElement('button');
        btn.className='btn btn-sm btn-success mt-1';
        btn.textContent='Recordatorio al paciente';
        btn.onclick = ()=>enviarRecordatorio(i);
        if(c.recordatorio_enviado) btn.textContent += ` ✅ (${c.recordatorio_enviado})`;
        div.appendChild(btn);
        row.querySelector('.controles').appendChild(div);
    });

    if(chart) chart.destroy();
    chart = new Chart(canvas.getContext('2d'), {
        type:'line',
        data: {
            labels:['-4','-3','-2','-1','Actual'],
            datasets: Object.keys(data.parametros).map((param)=>{
                return {
                    label: param,
                    data: data.parametros[param],
                    borderColor: 'rgb(75,192,192)',
                    fill:false,
                    tension:0.3,
                    pointRadius:3
                }
            })
        },
        options:{responsive:false, plugins:{legend:{display:true}}, scales:{y:{beginAtZero:false}}}
    });
}

function color(nivel){
    switch(nivel){
        case 'alto': return '#dc3545';
        case 'medio': return '#ffc107';
        case 'bajo': return '#198754';
        default: return '#6c757d';
    }
}

selectPaciente.addEventListener('change', renderPaciente);
selectEnfermedad.addEventListener('change', renderEnfermedad);

renderPaciente();
</script>
</body>
</html>



