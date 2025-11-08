<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$medico_nombre = $_SESSION['medico_nombre'] ?? null;

// Traer datos de pacientes
$edades = [];
$enfermedades = [];
$obrasSociales = [];

$result = $conn->query("SELECT edad, enfermedades, obra_social FROM pacientes");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $edades[] = (int)$row['edad'];
        $obra = $row['obra_social'] ?? 'Sin OS';
        if(!isset($obrasSociales[$obra])) $obrasSociales[$obra]=0;
        $obrasSociales[$obra]++;

        if (!empty($row['enfermedades'])) {
            $lista = explode(",", $row['enfermedades']);
            foreach ($lista as $e) {
                $e = trim($e);
                if ($e) {
                    if (!isset($enfermedades[$e])) $enfermedades[$e] = 0;
                    $enfermedades[$e]++;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estadísticas | SaludProactiva</title>
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
.container-main { padding-top:20px; padding-bottom:20px; flex:1; max-width:900px; margin:auto; }
.chart-container { width:100%; max-width:350px; height:250px; margin:15px auto; }
footer { background-color:#212529; color:#fff; text-align:center; padding:12px 0; }
footer a { color:#0d6efd; text-decoration:none; font-weight:bold; }
footer a:hover { text-decoration:underline; }
body { display:flex; flex-direction:column; min-height:100vh; }
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
        <li class="nav-item"><a class="btn btn-menu" href="estadisticas.php">Estadísticas</a></li>
      </ul>
      <ul class="navbar-nav">
        <?php if ($medico_nombre): ?>
          <li class="nav-item"><span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($medico_nombre) ?></span></li>
          <li class="nav-item"><a class="btn btn-salir btn-sm" href="logout.php">Salir</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-menu btn-sm" href="login.php">Ingresar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container container-main">
    <h3 class="mb-3">Estadísticas de Pacientes</h3>

    <div class="mb-3">
        <label for="preguntaSelect" class="form-label">Seleccione una pregunta:</label>
        <select id="preguntaSelect" class="form-select">
            <option value="edad">Distribución de edades</option>
            <option value="enfermedades">Enfermedades más comunes</option>
            <option value="obraSocial">Pacientes por obra social</option>
            <option value="promEdad">Promedio de edad</option>
        </select>
    </div>

    <div id="graficos">
        <div class="chart-container"><canvas id="edadChart"></canvas></div>
        <div class="chart-container"><canvas id="enfermedadesChart"></canvas></div>
        <div class="chart-container"><canvas id="obraSocialChart"></canvas></div>
        <div class="chart-container"><canvas id="promEdadChart"></canvas></div>
    </div>
</div>

<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script>
// Datos PHP a JS
const edades = <?= json_encode($edades) ?>;
const enfermedadesData = <?= json_encode($enfermedades) ?>;
const obrasSocialesData = <?= json_encode($obrasSociales) ?>;
const totalEnfermedades = <?= array_sum(array_values($enfermedades)) ?>;

// Gráfico edades
const edadCtx = document.getElementById('edadChart').getContext('2d');
const edadChart = new Chart(edadCtx, {
    type: 'bar',
    data: {
        labels: ['<40','40-49','50-59','60+'],
        datasets: [{
            label: 'Cantidad de pacientes',
            data: [
                edades.filter(e=>e<40).length,
                edades.filter(e=>e>=40 && e<50).length,
                edades.filter(e=>e>=50 && e<60).length,
                edades.filter(e=>e>=60).length
            ],
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        scales: {
            x:{ title:{ display:true, text:'Rango de edad' } },
            y:{ title:{ display:true, text:'Cantidad de pacientes' }, beginAtZero:true }
        }
    }
});

// Gráfico enfermedades
const enfermedadesCtx = document.getElementById('enfermedadesChart').getContext('2d');
const enfermedadesChart = new Chart(enfermedadesCtx, {
    type: 'pie',
    data: {
        labels: Object.keys(enfermedadesData),
        datasets: [{
            label: 'Enfermedades',
            data: Object.values(enfermedadesData),
            backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#6c757d','#fd7e14','#0dcaf0']
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        plugins:{
            tooltip:{
                callbacks:{
                    label: function(ctx){
                        let value = ctx.raw;
                        let pct = ((value/totalEnfermedades)*100).toFixed(1);
                        return ctx.label + ': ' + value + ' (' + pct + '%)';
                    }
                }
            }
        }
    }
});

// Gráfico obras sociales
const obraCtx = document.getElementById('obraSocialChart').getContext('2d');
const obraChart = new Chart(obraCtx, {
    type: 'bar',
    data: {
        labels: Object.keys(obrasSocialesData),
        datasets:[{
            label:'Cantidad de pacientes',
            data:Object.values(obrasSocialesData),
            backgroundColor:'#198754'
        }]
    },
    options:{
        responsive:true, maintainAspectRatio:false,
        scales:{ x:{ title:{ display:true, text:'Obra social' } }, y:{ title:{ display:true, text:'Cantidad de pacientes' }, beginAtZero:true } }
    }
});

// Gráfico promedio edad
const promCtx = document.getElementById('promEdadChart').getContext('2d');
const promedioEdad = edades.length>0 ? (edades.reduce((a,b)=>a+b,0)/edades.length).toFixed(1) : 0;
const promEdadChart = new Chart(promCtx,{
    type:'bar',
    data:{
        labels:['Promedio de edad'],
        datasets:[{label:'Edad',data:[promedioEdad],backgroundColor:'#fd7e14'}]
    },
    options:{
        responsive:true, maintainAspectRatio:false,
        scales:{ y:{ title:{ display:true, text:'Edad promedio' }, beginAtZero:true } }
    }
});

// Control del dropdown
const preguntaSelect = document.getElementById('preguntaSelect');
preguntaSelect.addEventListener('change', function(){
    document.querySelectorAll('#graficos .chart-container').forEach(c=>c.style.display='none');
    switch(this.value){
        case 'edad': document.getElementById('edadChart').parentElement.style.display='block'; break;
        case 'enfermedades': document.getElementById('enfermedadesChart').parentElement.style.display='block'; break;
        case 'obraSocial': document.getElementById('obraSocialChart').parentElement.style.display='block'; break;
        case 'promEdad': document.getElementById('promEdadChart').parentElement.style.display='block'; break;
    }
});

// Mostrar inicialmente solo edades
document.getElementById('edadChart').parentElement.style.display='block';
document.getElementById('enfermedadesChart').parentElement.style.display='none';
document.getElementById('obraSocialChart').parentElement.style.display='none';
document.getElementById('promEdadChart').parentElement.style.display='none';
</script>
</body>
</html>
<?php $conn->close(); ?>







