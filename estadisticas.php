<?php
session_start();
$medico_nombre = $_SESSION['medico_nombre'] ?? 'Dr. SaludProactiva';

// Función para colores de riesgo
function colorRiesgo($nivel) {
    switch(strtolower($nivel)){
        case 'alto': return '#dc3545'; // rojo
        case 'medio': return '#ffc107'; // amarillo
        case 'bajo': return '#198754'; // verde
        default: return '#6c757d'; // gris
    }
}

// Simulación de pacientes con datos de ejemplo
$pacientes = [
    [
        'dni' => '9123456',
        'nombre' => 'Salvador',
        'apellido' => 'Dali',
        'edad' => 71,
        'enfermedades' => 'Hipertensión, Diabetes',
        'riesgo_actual' => 'alto',
        'riesgo_futuro' => 'alto',
        'tendencia_param' => [130, 135, 140, 145, 150],
        'estudios_pendientes' => ['Control de presión', 'Glucemia'],
        'proximo_control' => ['2025-09-27', '2025-09-30'],
        'recomendaciones' => ['Reducir sal', 'Controlar azúcar'],
        'estudios_complementarios' => ['Electrocardiograma', 'Perfil lipídico']
    ],
    [
        'dni' => '10123456',
        'nombre' => 'Pablo',
        'apellido' => 'Picasso',
        'edad' => 44,
        'enfermedades' => 'Hipertensión',
        'riesgo_actual' => 'medio',
        'riesgo_futuro' => 'medio',
        'tendencia_param' => [120, 125, 130, 132, 135],
        'estudios_pendientes' => ['Ecografía abdominal'],
        'proximo_control' => ['2025-10-15'],
        'recomendaciones' => ['Ejercicio 30min diario'],
        'estudios_complementarios' => ['Holter de presión', 'Análisis de orina']
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
/* Navbar compacto */
.navbar { background-color: #212529; padding: 0.2rem 1rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #fff; border: 1px solid #212529; border-radius: 5px; margin-right: 0.5rem; padding: 2px 10px; font-size: 0.9rem; }
.navbar .btn-menu:hover { background-color: #6c757d; color: #fff; transform: translateY(-2px); }
.navbar-text { color: #fff; background-color: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 5px; margin-right: 0.5rem; font-weight: 500; font-size: 0.9rem;}
.btn-salir { background-color: #dc3545; color: #fff; border: none; padding: 2px 10px; font-size: 0.9rem;}
.btn-salir:hover { background-color: #b02a37; }

/* Tabla moderna */
.table-dark { background-color: #343a40 !important; color: #fff; font-weight: 600; letter-spacing: 0.5px;}
.table-hover tbody tr:hover { background-color: #e9ecef; cursor: pointer;}
.table td, .table th { vertical-align: middle; text-align: center;}
.small-chart { width: 100px !important; height: 50px !important; }

/* Footer */
footer { background-color: #212529; color: #fff; text-align: center; padding: 12px 0; }
footer a { color: #0d6efd; text-decoration: none; font-weight: bold; }
footer a:hover { text-decoration: underline; }
</style>
<script>
function enviarMensaje(nombre, dni){
    alert("Se enviará un recordatorio a " + nombre + " (DNI: " + dni + ")");
}
</script>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php" title="Ir al inicio">
      <img src="icons/logo.png" alt="Logo de SaludProactiva">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Menú de navegación">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="btn btn-menu" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="vademecum.php">Vademécum</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="pacientes.php">Pacientes</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="turnos.php">Turnos</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($medico_nombre) ?></span></li>
        <li class="nav-item"><a class="btn btn-salir btn-sm" href="logout.php">Salir</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- CONTENIDO -->
<div class="container mt-4 mb-4">
    <h3 class="mb-3">Riesgo y Control de Pacientes</h3>

    <!-- Tabla -->
    <table class="table table-striped table-hover align-middle bg-white" id="tablaPacientes">
        <thead class="table-dark text-uppercase">
            <tr>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Enfermedades</th>
                <th>Riesgo Actual</th>
                <th>Riesgo Futuro (IA)</th>
                <th>Tendencia</th>
                <th>Estudios Pendientes</th>
                <th>Próximo Control</th>
                <th>Recomendaciones</th>
                <th>Estudios Complementarios</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pacientes as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></td>
                <td><?= $p['edad'] ?></td>
                <td><?= htmlspecialchars($p['enfermedades']) ?></td>
                <td style="background-color: <?= colorRiesgo($p['riesgo_actual']) ?>; color: #fff;"><?= $p['riesgo_actual'] ?></td>
                <td style="background-color: <?= colorRiesgo($p['riesgo_futuro']) ?>; color: #fff;"><?= $p['riesgo_futuro'] ?></td>
                <td><canvas class="small-chart" id="tendencia-<?= $p['dni'] ?>"></canvas></td>
                <td><?= implode(', ', $p['estudios_pendientes']) ?></td>
                <td><?= implode(', ', $p['proximo_control']) ?></td>
                <td><?= implode(', ', $p['recomendaciones']) ?></td>
                <td><?= implode(', ', $p['estudios_complementarios']) ?></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="enviarMensaje('<?= $p['nombre'] ?>','<?= $p['dni'] ?>')">Avisar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- FOOTER -->
<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Dibujar mini gráficos de tendencia
<?php foreach($pacientes as $p): ?>
var ctx<?= $p['dni'] ?> = document.getElementById('tendencia-<?= $p['dni'] ?>').getContext('2d');
new Chart(ctx<?= $p['dni'] ?>, {
    type: 'line',
    data: {
        labels: ['Mes -4','Mes -3','Mes -2','Mes -1','Actual'],
        datasets: [{
            label: 'Parámetro',
            data: <?= json_encode($p['tendencia_param']) ?>,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.3,
            fill: false,
            pointRadius: 3
        }]
    },
    options: {
        responsive: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: false } }
    }
});
<?php endforeach; ?>
</script>
</body>
</html>

