<?php
session_start();
require 'conexion.php';
require 'config.php'; // tu API Key si usarás IA

if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$paciente = null;
$recomendaciones = '';
$mensaje = '';
$buscar = '';

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
        $stmt_p = $conn->prepare("SELECT nombre, email FROM pacientes WHERE id=?");
        $stmt_p->bind_param("i", $paciente_id);
        $stmt_p->execute();
        $pac = $stmt_p->get_result()->fetch_assoc();

        if($pac && !empty($pac['email'])) {
            $to = $pac['email'];
            $subject = "Recordatorio de control médico: $enfermedad";
            $message = "Hola " . $pac['nombre'] . ",\n\nLe recordamos que debe realizar un control por su enfermedad: $enfermedad.\nPor favor coordine un turno con su médico.\n\nSaludos.";
            $headers = "From: clinica@saludproactiva.com";

            if(mail($to, $subject, $message, $headers)) {
                $mensaje = "Recordatorio enviado a " . $pac['nombre'] . " para la enfermedad $enfermedad.";
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
<title>Buscar Paciente</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Buscar Paciente por DNI</h3>

    <?php if($mensaje) echo "<div class='alert alert-info'>$mensaje</div>"; ?>

    <form method="POST" class="row g-3 mb-3">
        <div class="col-md-4">
            <input type="text" name="dni" class="form-control" placeholder="Ingrese DNI" value="<?= htmlspecialchars($buscar) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">Menú principal</button>
        </div>
    </form>

    <?php if ($paciente): ?>
        <div class="card p-3 bg-white">
            <h5>Datos del Paciente</h5>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($paciente['nombre']) ?></p>
            <p><strong>DNI:</strong> <?= htmlspecialchars($paciente['dni']) ?></p>
            <p><strong>Edad:</strong> <?= $paciente['edad'] ?></p>
            <p><strong>Enfermedades:</strong> <?= htmlspecialchars($paciente['enfermedades']) ?></p>

            <h5>Turnos</h5>
            <p><strong>Último turno:</strong> <?= $last_turno ? date('d/m/Y H:i', strtotime($last_turno['fecha'])) : 'No tiene' ?></p>
            <p><strong>Próximo turno:</strong> <?= $next_turno ? date('d/m/Y H:i', strtotime($next_turno['fecha'])) : 'No tiene' ?></p>

            <h5>Recomendaciones IA</h5>
            <p><?= $recomendaciones ?></p>

            <h5>Recordatorio de control</h5>
            <?php
                $enfermedades = explode(',', $paciente['enfermedades']);
                foreach($enfermedades as $enf):
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
        <div class="alert alert-danger">No se encontraron pacientes con ese DNI.</div>
    <?php endif; ?>
</div>
</body>
</html>


