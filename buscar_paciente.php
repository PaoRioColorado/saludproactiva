<?php
session_start();
require 'conexion.php';
require 'config.php'; // tu API Key si usarás IA

$medico_nombre = $_SESSION['medico_nombre'] ?? null;

if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$paciente = null;
$recomendaciones = '';
$mensaje = '';
$buscar = '';
$last_turno = null;
$next_turno = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['dni'])) {
        $buscar = $_POST['dni'] ?? '';

        // Buscar paciente por DNI
        $stmt = $conn->prepare("SELECT * FROM pacientes WHERE dni=?");
        $stmt->bind_param("s", $buscar);
        $stmt->execute();
        $result = $stmt->get_result();
        $paciente = $result->fetch_assoc();

        if ($paciente) {
            $paciente_id = $paciente['id'];

            // Calcular edad automáticamente si no está
            if (empty($paciente['edad']) && !empty($paciente['fecha_nacimiento'])) {
                $fecha_nac = new DateTime($paciente['fecha_nacimiento']);
                $hoy = new DateTime();
                $paciente['edad'] = $hoy->diff($fecha_nac)->y;
            }

            // Último turno
            $stmt_last = $conn->prepare("SELECT * FROM turnos WHERE paciente_id=? AND fecha < NOW() ORDER BY fecha DESC LIMIT 1");
            $stmt_last->bind_param("i", $paciente_id);
            $stmt_last->execute();
            $last_turno = $stmt_last->get_result()->fetch_assoc();

            // Próximo turno
            $stmt_next = $conn->prepare("SELECT * FROM turnos WHERE paciente_id=? AND fecha >= NOW() ORDER BY fecha ASC LIMIT 1");
            $stmt_next->bind_param("i", $paciente_id);
            $stmt_next->execute();
            $next_turno = $stmt_next->get_result()->fetch_assoc();

            // Recomendaciones IA simuladas
            $recomendaciones = "Recomendaciones de control médico basadas en enfermedades: " . $paciente['enfermedades'];
        }
    }

    // Enviar recordatorio
    if(isset($_POST['recordatorio'])) {
        $paciente_id = $_POST['paciente_id'];
        $enfermedad = $_POST['enfermedad'];

        // Obtener datos del paciente
        $stmt_p = $conn->prepare("SELECT nombre, apellido, email FROM pacientes WHERE id=?");
        $stmt_p->bind_param("i", $paciente_id);
        $stmt_p->execute();
        $pac = $stmt_p->get_result()->fetch_assoc();

        if($pac && !empty($pac['email'])) {
            $to = $pac['email'];
            $subject = "Recordatorio de control médico: $enfermedad";
            $message = "Hola " . $pac['nombre'] . " " . $pac['apellido'] . ",\n\nLe recordamos que debe realizar un control por su enfermedad: $enfermedad.\nPor favor coordine un turno con su médico.\n\nSaludos.";
            $headers = "From: clinica@saludproactiva.com";

            if(mail($to, $subject, $message, $headers)) {
                $mensaje = "Recordatorio enviado a " . $pac['nombre'] . " " . $pac['apellido'] . " para la enfermedad $enfermedad.";
            } else {
                $mensaje = "No se pudo enviar el recordatorio. Revise la configuración de correo.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Buscar Paciente | SaludProactiva</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Body y Footer */
html, body { height: 100%; margin: 0; display: flex; flex-direction: column; font-family: 'Segoe UI', sans-serif; background-color: #f2f4f7; }
body { flex: 1; }

/* Navbar (color original) */
.navbar { background-color: #212529; padding: 0.3rem 1rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #fff; border: 1px solid #212529; border-radius: 6px; margin-right: 0.5rem; padding: 4px 12px; font-size: 0.9rem; transition: all 0.3s; }
.navbar .btn-menu:hover { background-color: #6c757d; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.15);}
.navbar-text { color: #fff; background-color: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 5px; margin-right: 0.5rem; font-weight: 500; font-size: 0.9rem;}
.btn-salir { background-color: #dc3545; color: #fff; border: none; padding: 4px 12px; font-size: 0.9rem; transition: 0.3s;}
.btn-salir:hover { background-color: #b02a37; }

/* Contenedor principal */
.content-center { margin: 2rem auto; max-width: 900px; flex: 1; }

/* Card Paciente moderno */
.card { background-color: #ffffff; border-radius: 10px; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.card h5 { border-bottom: 1px solid #dee2e6; padding-bottom: 5px; margin-bottom: 1rem; color: #212529; }

/* Botones recordatorio */
.btn-info { background-color: #3498db; border-color: #3498db; color: #fff; transition: 0.3s; }
.btn-info:hover { background-color: #2980b9; border-color: #2980b9; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }

/* Botones formulario modernos */
.btn-primary { background-color: #5dade2; border-color: #5dade2; color: #fff; transition: 0.3s; }
.btn-primary:hover { background-color: #3498db; border-color: #3498db; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

.btn-success { background-color: #82e0aa; border-color: #82e0aa; color: #212529; transition: 0.3s; }
.btn-success:hover { background-color: #58d68d; border-color: #58d68d; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

/* Footer (color original) */
footer { background-color: #212529; color: #fff; text-align: center; padding: 12px 0; }
footer a { color: #0d6efd; text-decoration: none; font-weight: bold; }
footer a:hover { text-decoration: underline; }
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
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- CONTENIDO -->
<div class="container content-center">
    <h3 class="mb-4 text-center">Buscar Paciente por DNI</h3>

    <?php if($mensaje) echo "<div class='alert alert-info'>$mensaje</div>"; ?>

    <form method="POST" class="row g-3 justify-content-center mb-4">
        <div class="col-md-5">
            <input type="text" name="dni" class="form-control form-control-lg" placeholder="Ingrese DNI" value="<?= htmlspecialchars($buscar) ?>" autofocus>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-search"></i> Buscar
            </button>
        </div>
        <div class="col-md-3 d-grid">
            <button type="button" class="btn btn-success btn-lg" onclick="window.location.href='paciente.php'">
                <i class="bi bi-person-plus"></i> Nuevo Paciente
            </button>
        </div>
    </form>

    <?php if ($paciente): ?>
        <div class="card">
            <h5>Datos del Paciente</h5>
            <p><strong>Nombre y Apellido:</strong> <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']) ?></p>
            <p><strong>DNI:</strong> <?= htmlspecialchars($paciente['dni']) ?></p>
            <p><strong>Edad:</strong> <?= $paciente['edad'] ?></p>
            <p><strong>Enfermedades:</strong> <?= htmlspecialchars($paciente['enfermedades']) ?></p>

            <h5 class="mt-3">Turnos</h5>
            <p><strong>Último turno:</strong> <?= $last_turno ? date('d/m/Y H:i', strtotime($last_turno['fecha'])) : 'No tiene' ?></p>
            <p><strong>Próximo turno:</strong> <?= $next_turno ? date('d/m/Y H:i', strtotime($next_turno['fecha'])) : 'No tiene' ?></p>

            <h5 class="mt-3">Recomendaciones IA</h5>
            <p><?= $recomendaciones ?></p>

            <h5 class="mt-3">Recordatorio de control</h5>
            <?php
                $enfermedades_arr = explode(',', $paciente['enfermedades']);
                foreach($enfermedades_arr as $enf):
                    $enf = trim($enf);
                    if($enf):
            ?>
                <form method="POST" style="display:inline-block;">
                    <input type="hidden" name="paciente_id" value="<?= $paciente['id'] ?>">
                    <input type="hidden" name="enfermedad" value="<?= $enf ?>">
                    <button type="submit" name="recordatorio" class="btn btn-sm btn-info mb-1">
                        Recordatorio: <?= $enf ?>
                    </button>
                </form>
            <?php 
                    endif;
                endforeach;
            ?>
        </div>
    <?php elseif($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-danger text-center">No se encontraron pacientes con ese DNI.</div>
    <?php endif; ?>
</div>

<!-- FOOTER -->
<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
