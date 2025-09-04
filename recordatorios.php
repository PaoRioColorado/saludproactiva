<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$medico_nombre = $_SESSION['medico_nombre'] ?? null;

// Obtener turnos desde la base
$turnos = [];
$sql = "SELECT t.fecha, t.estado, p.nombre, p.apellido 
        FROM turnos t
        JOIN pacientes p ON t.paciente_id = p.id";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fechaHora = $row['fecha'];
        $estado = $row['estado'];
        $pacienteNombre = $row['nombre'] . " " . $row['apellido'];

        $turnos[] = [
            'title' => $pacienteNombre,
            'start' => $fechaHora,
            'estado' => $estado,
            'allDay' => false,
            'color' => ($estado == 'pendiente') ? 'orange' : 'green'
        ];
    }
}

$turnosJSON = json_encode($turnos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recordatorios | SaludProactiva</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
/* Footer abajo */
html, body {
    height: 100%;
    margin: 0;
}
body {
    display: flex;
    flex-direction: column;
}

/* Navbar */
.navbar { background-color: #212529; padding-top: 0.2rem; padding-bottom: 0.2rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #ffffff; border: 1px solid #212529; border-radius: 5px; margin-right: 0.5rem; padding: 2px 10px; font-size: 0.9rem; transition: 0.3s; }
.navbar .btn-menu:hover { background-color: #6c757d; color: #ffffff; }
.navbar-text { color: #ffffff; background-color: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 5px; margin-right: 0.5rem; font-weight: 500; font-size: 0.9rem;}
.btn-salir { background-color: #dc3545; color: #ffffff; border: none; padding: 2px 10px; font-size: 0.9rem; transition: 0.3s;}
.btn-salir:hover { background-color: #b02a37; }

/* Contenido principal */
.container.content-center {
    flex: 1;
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
}

/* Footer */
footer { background-color: #212529; color: #ffffff; text-align: center; padding: 12px 0; }
footer a { color: #0d6efd; text-decoration: none; font-weight: bold; }
footer a:hover { text-decoration: underline; }

/* Mini calendario compacto */
#calendar-container { 
    display: flex; 
    justify-content: center; 
    align-items: flex-start; 
    gap: 20px; 
    flex-wrap: wrap;
}

#mini-calendar { 
    max-width: 400px; 
    background: #fff; 
    padding: 5px; 
    border-radius: 8px; 
    box-shadow: 0 2px 6px rgba(0,0,0,0.1); 
    height: auto; 
    font-size: 0.75rem; 
}

#detalle-dia { 
    max-width: 250px; 
    background: #f8f9fa; 
    padding: 8px; 
    border-radius: 8px; 
    font-size: 0.8rem; 
}

#detalle-dia h5 { font-size: 0.9rem; margin-bottom: 5px; }
#detalle-dia ul { padding-left: 1rem; margin: 0; }
#pendientes-count { font-weight: bold; color: orange; margin-bottom: 5px; }
</style>
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

<div class="container content-center">
    <h2 class="mb-4">Recordatorios de Pacientes</h2>

    <div id="calendar-container">
        <!-- Calendario -->
        <div id="mini-calendar"></div>

        <!-- Detalle y contador -->
        <div id="detalle-dia">
            <div id="pendientes-count">Pacientes pendientes: 0</div>
            <div id="turnos-list"></div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('mini-calendar');
    var detalleEl = document.getElementById('turnos-list');
    var pendientesEl = document.getElementById('pendientes-count');

    var turnos = <?= $turnosJSON ?>;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'title',
            center: '',
            right: ''
        },
        height: 'auto',
        events: turnos,
        eventDidMount: function(info) {
            info.el.style.fontSize = '0.65rem';
        },
        dateClick: function(info) {
            var fecha = info.dateStr;
            var turnosDia = turnos.filter(t => t.start.startsWith(fecha));

            if(turnosDia.length === 0){
                detalleEl.innerHTML = `<p>No hay turnos para el ${fecha}</p>`;
                pendientesEl.textContent = 'Pacientes pendientes: 0';
                return;
            }

            var total = turnosDia.length;
            var pendientes = turnosDia.filter(t => t.estado === 'pendiente').length;

            pendientesEl.textContent = `Pacientes pendientes: ${pendientes}`;

            var html = `<h5>Turnos del ${fecha}</h5><ul>`;
            turnosDia.forEach(t => {
                html += `<li>${t.title} - <strong>${t.estado}</strong></li>`;
            });
            html += '</ul>';
            detalleEl.innerHTML = html;
        }
    });

    calendar.render();
});
</script>

</body>
</html>

