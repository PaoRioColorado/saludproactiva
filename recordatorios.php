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

        $color = ($estado == 'pendiente') ? '#1e90ff' : '#28a745'; // azul pendientes, verde confirmados

        $turnos[] = [
            'title' => $pacienteNombre,
            'start' => $fechaHora,
            'estado' => $estado,
            'allDay' => false,
            'color' => $color
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
html, body { height: 100%; margin: 0; font-family: 'Segoe UI', sans-serif; }
body { display: flex; flex-direction: column; background-color: #f2f4f7; }

.navbar { background-color: #212529; padding-top: 0.3rem; padding-bottom: 0.3rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #fff; border-radius: 5px; margin-right: 0.5rem; padding: 3px 10px; font-size: 0.9rem; }
.navbar .btn-menu:hover { background-color: #6c757d; }
.navbar-text { color: #fff; background-color: rgba(255,255,255,0.1); padding: 3px 8px; border-radius: 5px; margin-right: 0.5rem; font-weight: 500; font-size: 0.9rem;}
.btn-salir { background-color: #dc3545; color: #fff; border: none; padding: 3px 10px; font-size: 0.9rem;}
.btn-salir:hover { background-color: #b02a37; }

.container.content-center { flex: 1; margin: 2rem auto; width: 95%; max-width: 1200px; }

#calendar-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 25px; }
#mini-calendar { max-width: 700px; width: 100%; background: #fff; padding: 10px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-size: 0.85rem; }
#detalle-dia { max-width: 400px; width: 100%; background: #fff; padding: 12px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-size: 0.9rem; word-wrap: break-word; max-height: 600px; overflow-y: auto; }

#detalle-dia h4 { font-weight: bold; border-bottom: 2px solid #eee; padding-bottom: 5px; margin-bottom: 15px; text-align: center; color: #0d6efd;}
#detalle-dia h6 { font-weight: 600; margin-bottom: 8px; color: #333; }
#detalle-dia ul { padding-left: 1rem; margin: 0; }

.card-contador { display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; border-radius: 8px; color: #fff; font-weight: 600; margin-bottom: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); font-size: 0.95rem; }
.bg-pendientes { background-color: #1e90ff; }
.bg-espera { background-color: #ff5722; }

/* Footer original */
footer { background-color: #212529; color: #ffffff; text-align: center; padding: 12px 0; }
footer a { color: #0d6efd; text-decoration: none; font-weight: bold; }
footer a:hover { text-decoration: underline; }
</style>
</head>
<body>

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
        <div id="mini-calendar"></div>
        <div id="detalle-dia">
            <h4>📋 Panel de Turnos</h4>

            <section class="mb-3">
                <h6>Resumen del día</h6>
                <div class="card-contador bg-pendientes">
                    <span>Pacientes pendientes</span>
                    <span id="pendientes-count">0</span>
                </div>
                <div class="card-contador bg-espera">
                    <span>Pacientes en sala de espera</span>
                    <span id="espera-count">0</span>
                </div>
            </section>

            <section>
                <h6>Turnos programados</h6>
                <div id="turnos-list"></div>
            </section>
        </div>
    </div>
</div>

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
    var esperaEl = document.getElementById('espera-count');

    var turnos = <?= $turnosJSON ?>;

    function mostrarTurnosDelDia(fecha) {
        var turnosDia = turnos.filter(t => t.start.startsWith(fecha));
        if(turnosDia.length === 0){
            detalleEl.innerHTML = `<p>No hay turnos para el ${fecha}</p>`;
            pendientesEl.textContent = '0';
            esperaEl.textContent = '0';
            return;
        }

        var pendientes = turnosDia.filter(t => t.estado === 'pendiente').length;
        pendientesEl.textContent = pendientes;
        esperaEl.textContent = pendientes;

        var html = `<ul class="list-unstyled">`;
        turnosDia.forEach(t => {
            let nombreCompleto = t.title;
            let apellido = nombreCompleto.split(' ').slice(-1)[0];
            let hora = t.start.split('T')[1] ? t.start.split('T')[1].substring(0,5) : '';

            let mensajeIA = `Hola ${nombreCompleto}, le recordamos su turno el ${fecha} a las ${hora}. Por favor, traiga toda la documentación necesaria.`;

            let botonWhatsapp = `<a href="https://wa.me/5491111111111?text=${encodeURIComponent(mensajeIA)}" target="_blank" class="btn btn-sm btn-success ms-2 mb-1">Enviar WhatsApp</a>`;

            let botonLlamar = `<button class="btn btn-sm btn-info ms-2 mb-1" onclick="alert('Paciente ${apellido}, por favor acérquese al consultorio')">Llamar</button>`;

            let inputIndicaciones = `
                <div class="mt-1">
                    <textarea id="ind-${apellido}" class="form-control form-control-sm" placeholder="Escriba indicaciones..." rows="2"></textarea>
                    <button class="btn btn-sm btn-primary mt-1" onclick="
                        let msg = document.getElementById('ind-${apellido}').value;
                        if(msg.trim() === ''){ alert('Ingrese las indicaciones antes de enviar'); return; }
                        alert('Indicaciones enviadas a ${apellido}: ' + msg);
                        document.getElementById('ind-${apellido}').value = '';
                    ">Enviar indicaciones</button>
                </div>
            `;

            html += `<li class="mb-3">${hora} - ${nombreCompleto} - <strong>${t.estado}</strong> ${botonWhatsapp} ${botonLlamar} ${inputIndicaciones}</li>`;
        });

        html += '</ul>';
        detalleEl.innerHTML = html;
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: { left: 'title', center: '', right: '' },
        height: 'auto',
        events: turnos,
        weekends: false,
        eventDidMount: function(info) { info.el.style.fontSize = '0.75rem'; },
        dateClick: function(info) { mostrarTurnosDelDia(info.dateStr); }
    });

    calendar.render();

    var hoy = new Date().toISOString().split('T')[0];
    mostrarTurnosDelDia(hoy);
});
</script>

</body>
</html>
