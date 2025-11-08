<?php
session_start();
require 'conexion.php'; // Tu conexión a la base

// Verificar que el médico esté logueado
if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener turnos desde la base
$turnos = [];
$sql = "SELECT t.fecha, t.estado, p.nombre, p.apellido 
        FROM turnos t
        JOIN pacientes p ON t.paciente_id = p.id";
$result = $conn->query($sql);
$totalesPorDia = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fechaHora = $row['fecha'];
        $fecha = date('Y-m-d', strtotime($fechaHora));
        $hora = date('H:i', strtotime($fechaHora));
        $estado = $row['estado'];
        $pacienteNombre = $row['nombre'] . " " . $row['apellido'];

        // Turno individual
        $turnos[] = [
            'title' => $pacienteNombre . " (" . $estado . ")",
            'start' => $fechaHora,
            'color' => ($estado == 'pendiente') ? 'orange' : 'green',
            'allDay' => false
        ];

        // Contador por día
        if (!isset($totalesPorDia[$fecha])) {
            $totalesPorDia[$fecha] = ['total'=>0, 'pendientes'=>0];
        }
        $totalesPorDia[$fecha]['total']++;
        if ($estado == 'pendiente') {
            $totalesPorDia[$fecha]['pendientes']++;
        }
    }
}

// Generar eventos de totales por día
foreach ($totalesPorDia as $fecha => $c) {
    $turnos[] = [
        'title' => "Total: {$c['total']} | Pendientes: {$c['pendientes']}",
        'start' => $fecha,
        'allDay' => true,
        'color' => 'blue'
    ];
}

// Convertir a JSON para FullCalendar
$turnosJSON = json_encode($turnos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Calendario de Turnos | SaludProactiva</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
/* Estilos navbar y footer tomados del paciente.php */
.navbar { background-color: #212529; padding-top: 0.2rem; padding-bottom: 0.2rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #ffffff; border: 1px solid #212529; border-radius: 5px; margin-right: 0.5rem; padding: 2px 10px; font-size: 0.9rem; transition: 0.3s; }
.navbar .btn-menu:hover { background-color: #6c757d; color: #ffffff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2);}
.navbar-text { color: #ffffff; background-color: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 5px; margin-right: 0.5rem; font-weight: 500; font-size: 0.9rem;}
.btn-salir { background-color: #dc3545; color: #ffffff; border: none; padding: 2px 10px; font-size: 0.9rem; transition: 0.3s;}
.btn-salir:hover { background-color: #b02a37; }
.content-center { margin-top: 1.5rem; margin-bottom: 1.5rem; }
footer { background-color: #212529; color: #ffffff; text-align: center; padding: 12px 0; }
footer a { color: #0d6efd; text-decoration: none; font-weight: bold; }
footer a:hover { text-decoration: underline; }
#calendar { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php" title="Ir al inicio">
      <img src="icons/logo.png" alt="Logo de SaludProactiva">
    </a>
    <div class="navbar-nav ms-auto">
        <span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($_SESSION['medico_nombre']) ?></span>
        <a class="btn btn-salir btn-sm" href="logout.php">Salir</a>
    </div>
  </div>
</nav>

<div class="container content-center">
    <h2 class="mb-4">Calendario de Turnos</h2>
    <div id="calendar"></div>
</div>

<!-- FOOTER -->
<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?= $turnosJSON ?>,
        eventDidMount: function(info) {
            info.el.style.fontSize = '0.85rem';
        }
    });
    calendar.render();
});
</script>

</body>
</html>
