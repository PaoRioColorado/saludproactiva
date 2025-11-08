<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$medico_nombre = $_SESSION['medico_nombre'] ?? null;

// Traer datos de pacientes
$pacientes = [];

$result = $conn->query("SELECT edad, enfermedades, sexo FROM pacientes");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pacientes[] = $row;
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
/* NAVBAR */
.navbar { background-color: #212529; padding: 0.2rem 1rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #fff; border-radius:5px; margin-right:0.5rem; padding:2px 10px; font-size:0.9rem;}
.navbar .btn-menu:hover { background-color: #6c757d; }
.navbar-text { color:#fff; background-color: rgba(255,255,255,0.1); padding:2px 6px; border-radius:5px; font-size:0.9rem;}
.btn-salir { background-color:#dc3545; color:#fff; border:none; padding:2px 10px; font-size:0.9rem;}
.btn-salir:hover { background-color:#b02a37; }

/* MAIN */
.container-main { 
    padding-top:20px; 
    padding-bottom:60px; 
    flex:1; 
    max-width:1000px; 
    margin:auto; 
}

/* GRAFICOS */
#graficos {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    grid-auto-rows: auto;
    gap: 20px;
    justify-items: center;
    margin-bottom: 40px; /* espacio antes de Noticias */
}

.chart-container {
    width: 100%;
    max-width: 350px;
    margin: 0 auto;
}

/* TABLAS */
table.table-sm { font-size:0.85rem; }

/* RESUMEN */
#resumen-container { width:100%; margin-top:20px; margin-bottom:20px; padding:15px; background:#f8f9fa; border-radius:8px; }

/* NOTICIAS */
#noticias { margin-top:40px; }
#noticias h4 { margin-bottom:15px; }
.card-noticia { margin:10px 0; padding:12px; border-radius:8px; background:#f8f9fa; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
.card-noticia h6 { margin:0 0 5px 0; font-weight:bold; font-size:0.95rem; }
.card-noticia a { text-decoration:none; color:#0d6efd; }
.card-noticia a:hover { text-decoration:underline; }

/* FILTROS */
.filters { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:15px; }
.filters select { flex:1; max-width:200px; }

/* FOOTER */
footer { background-color:#212529; color:#fff; text-align:center; padding:12px 0; }
footer a { color:#0d6efd; text-decoration:none; font-weight:bold; }
footer a:hover { text-decoration:underline; }

/* BODY FLEX */
body { display:flex; flex-direction:column; min-height:100vh; }
</style>
</head>
<body>

<!-- NAVBAR -->
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

<!-- CONTENIDO PRINCIPAL -->
<div class="container container-main">
    <h3 class="mb-3">Estadísticas de Pacientes</h3>

    <div class="filters">
        <select id="preguntaSelect" class="form-select">
            <option value="edad">Distribución de edades</option>
            <option value="enfermedades">Enfermedades más comunes</option>
            <option value="resumen">Resumen general</option>
        </select>
        <select id="sexoSelect" class="form-select">
            <option value="todos">Todos los sexos</option>
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
        </select>
    </div>

    <div id="graficos">
        <div class="chart-container" id="edad-container">
            <canvas id="edadChart"></canvas>
            <table class="table table-sm mt-2">
                <thead>
                    <tr><th>Rango de edad</th><th>Cantidad</th><th>Porcentaje</th></tr>
                </thead>
                <tbody id="edadTablaBody"></tbody>
            </table>
        </div>

        <div class="chart-container" id="enfermedades-container">
            <canvas id="enfermedadesChart"></canvas>
            <table class="table table-sm mt-2">
                <thead>
                    <tr><th>Enfermedad</th><th>Cantidad</th><th>Porcentaje</th></tr>
                </thead>
                <tbody id="enfermedadesTablaBody"></tbody>
            </table>
        </div>

        <div class="chart-container" id="sexo-container">
            <canvas id="sexoChart"></canvas>
            <table class="table table-sm mt-2">
                <thead>
                    <tr><th>Sexo</th><th>Cantidad</th><th>Porcentaje</th></tr>
                </thead>
                <tbody id="sexoTablaBody"></tbody>
            </table>
        </div>

        <div id="resumen-container"></div>
    </div>

    <h4 class="mt-4">Noticias</h4>
    <div id="noticias">
        <div class="card-noticia">
            <h6>Medscape Endocrinology</h6>
            <a href="https://www.medscape.com/endocrinology" target="_blank">Ir a Medscape Endocrinology</a>
        </div>
        <div class="card-noticia">
            <h6>Endocrine News</h6>
            <a href="https://www.endocrine.org/news-and-advocacy/news-room" target="_blank">Ir a Endocrine News</a>
        </div>
        <div class="card-noticia">
            <h6>American Diabetes Association News</h6>
            <a href="https://diabetesjournals.org/care" target="_blank">Ir a ADA News</a>
        </div>
        <div class="card-noticia">
            <h6>Journal of Clinical Endocrinology & Metabolism</h6>
            <a href="https://academic.oup.com/jcem" target="_blank">Ir al sitio</a>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script>
let pacientes = <?= json_encode($pacientes) ?>;

function filtrarPacientes(sexo){
    if(sexo==='todos') return pacientes;
    return pacientes.filter(p=>p.sexo===sexo);
}

const edadCtx = document.getElementById('edadChart').getContext('2d');
const edadChart = new Chart(edadCtx, {
    type:'bar',
    data:{labels:['<40','40-49','50-59','60+'], datasets:[{label:'Cantidad de pacientes', data:[0,0,0,0], backgroundColor:'#0d6efd'}]},
    options:{responsive:true, maintainAspectRatio:true, aspectRatio:1.2, scales:{x:{title:{display:true,text:'Rango de edad'}}, y:{title:{display:true,text:'Cantidad de pacientes'}, beginAtZero:true}}, plugins:{legend:{display:true}}}
});

const enfermedadesCtx = document.getElementById('enfermedadesChart').getContext('2d');
const enfermedadesChart = new Chart(enfermedadesCtx, {
    type:'pie',
    data:{labels:[], datasets:[{label:'Enfermedades', data:[], backgroundColor:['#0d6efd','#198754','#ffc107','#dc3545','#6c757d','#fd7e14','#0dcaf0']}]},
    options:{responsive:true, maintainAspectRatio:true, aspectRatio:1, plugins:{legend:{display:true, position:'top'}}}
});

const sexoCtx = document.getElementById('sexoChart').getContext('2d');
const sexoChart = new Chart(sexoCtx, {
    type: 'pie',
    data: { labels: [], datasets: [{ label: 'Sexo', data: [], backgroundColor:['#0d6efd','#dc3545','#198754','#6c7570'] }]},
    options:{responsive:true, maintainAspectRatio:true, aspectRatio:1, plugins:{legend:{display:true, position:'top'}}}
});

function actualizarEdadChart(filtrados){
    const rangos = ['<40','40-49','50-59','60+'];
    const data = [
        filtrados.filter(p=>p.edad<40).length,
        filtrados.filter(p=>p.edad>=40 && p.edad<50).length,
        filtrados.filter(p=>p.edad>=50 && p.edad<60).length,
        filtrados.filter(p=>p.edad>=60).length
    ];
    edadChart.data.datasets[0].data = data;
    edadChart.update();

    const tbody = document.getElementById('edadTablaBody');
    tbody.innerHTML = '';
    data.forEach((c,i)=>{
        let pct = ((c/filtrados.length)*100).toFixed(1);
        tbody.innerHTML += `<tr><td>${rangos[i]}</td><td>${c}</td><td>${pct}%</td></tr>`;
    });
}

function actualizarEnfermedadesChart(filtrados){
    const enferData = {};
    filtrados.forEach(p=>{
        if(p.enfermedades){
            p.enfermedades.split(',').forEach(e=>{
                e=e.trim();
                if(e){
                    if(!enferData[e]) enferData[e]=0;
                    enferData[e]++;
                }
            });
        }
    });
    enfermedadesChart.data.labels = Object.keys(enferData);
    enfermedadesChart.data.datasets[0].data = Object.values(enferData);
    enfermedadesChart.update();

    const tbody = document.getElementById('enfermedadesTablaBody');
    tbody.innerHTML = '';
    Object.keys(enferData).forEach(e=>{
        let c = enferData[e];
        let pct = ((c/filtrados.length)*100).toFixed(1);
        tbody.innerHTML += `<tr><td>${e}</td><td>${c}</td><td>${pct}%</td></tr>`;
    });
}

function actualizarSexoChart(filtrados){
    const sexoCount = {};
    filtrados.forEach(p=>{
        let s = p.sexo || 'No definido';
        if(!sexoCount[s]) sexoCount[s]=0;
        sexoCount[s]++;
    });
    sexoChart.data.labels = Object.keys(sexoCount);
    sexoChart.data.datasets[0].data = Object.values(sexoCount);
    sexoChart.update();

    const tbody = document.getElementById('sexoTablaBody');
    tbody.innerHTML = '';
    Object.keys(sexoCount).forEach(s=>{
        let c = sexoCount[s];
        let pct = ((c/filtrados.length)*100).toFixed(1);
        tbody.innerHTML += `<tr><td>${s}</td><td>${c}</td><td>${pct}%</td></tr>`;
    });
}

function actualizarResumen(filtrados){
    let menores40 = filtrados.filter(p=>p.edad<40).length;
    let r40_49 = filtrados.filter(p=>p.edad>=40 && p.edad<50).length;
    let r50_59 = filtrados.filter(p=>p.edad>=50 && p.edad<60).length;
    let r60 = filtrados.filter(p=>p.edad>=60).length;

    let sexosCount = {};
    filtrados.forEach(p=>{
        let s = p.sexo || 'No definido';
        if(!sexosCount[s]) sexosCount[s]=0;
        sexosCount[s]++;
    });

    let enfermedadesCount = {};
    filtrados.forEach(p=>{
        if(p.enfermedades){
            p.enfermedades.split(',').forEach(e=>{
                e=e.trim();
                if(e){
                    if(!enfermedadesCount[e]) enfermedadesCount[e]=0;
                    enfermedadesCount[e]++;
                }
            });
        }
    });

    let topEnfermedades = Object.entries(enfermedadesCount)
        .sort((a,b)=>b[1]-a[1])
        .slice(0,3);

    let topEnfermedadesHtml = topEnfermedades.length > 0 
        ? `<ul>${topEnfermedades.map(([enf, c]) => `<li>${enf} (${((c/filtrados.length)*100).toFixed(1)}%)</li>`).join('')}</ul>`
        : 'N/A';

    let resumenDiv = document.getElementById('resumen-container');
    resumenDiv.innerHTML = `
        <p><strong>Total pacientes filtrados:</strong> ${filtrados.length}</p>
        <p><strong>Distribución por edad:</strong><br>
        Menores de 40: ${menores40} (${((menores40/filtrados.length)*100).toFixed(1)}%)<br>
        40-49: ${r40_49} (${((r40_49/filtrados.length)*100).toFixed(1)}%)<br>
        50-59: ${r50_59} (${((r50_59/filtrados.length)*100).toFixed(1)}%)<br>
        60+: ${r60} (${((r60/filtrados.length)*100).toFixed(1)}%)</p>
        <p><strong>Porcentaje por sexo:</strong><br>
        ${Object.keys(sexosCount).map(s=>`${s}: ${((sexosCount[s]/filtrados.length)*100).toFixed(1)}%`).join('<br>')}</p>
        <p><strong>Enfermedades más frecuentes:</strong>${topEnfermedadesHtml}</p>
    `;
}

function actualizarPantalla(){
    let sexo = document.getElementById('sexoSelect').value;
    let pregunta = document.getElementById('preguntaSelect').value;
    let filtrados = filtrarPacientes(sexo);

    actualizarResumen(filtrados);
    actualizarSexoChart(filtrados);
    actualizarEdadChart(filtrados);
    actualizarEnfermedadesChart(filtrados);

    document.getElementById('edad-container').style.display = pregunta==='edad' ? 'block' : 'none';
    document.getElementById('enfermedades-container').style.display = pregunta==='enfermedades' ? 'block' : 'none';
    document.getElementById('sexo-container').style.display = 'block';
}

document.getElementById('preguntaSelect').addEventListener('change', actualizarPantalla);
document.getElementById('sexoSelect').addEventListener('change', actualizarPantalla);

// Inicial
actualizarPantalla();
</script>

</body>
</html>

<?php $conn->close(); ?>













