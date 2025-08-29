<?php
session_start();
require 'conexion.php'; // Conexión a la base de datos

if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$medico_id = $_SESSION['medico_id'];
$action = $_GET['action'] ?? '';
$turno_id = $_GET['id'] ?? null;

// AGREGAR O EDITAR TURNO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_POST['paciente_id'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $notas = $_POST['notas'] ?? '';
    $estado = $_POST['estado'] ?? 'pendiente';

    $datetime = $fecha . ' ' . $hora;

    if (isset($_POST['turno_id']) && $_POST['turno_id'] != '') {
        // Editar turno
        $stmt = $conn->prepare("UPDATE turnos SET fecha=?, motivo=?, notas=?, estado=? WHERE id=? AND medico_id=?");
        $stmt->bind_param("sssiii", $datetime, $motivo, $notas, $estado, $_POST['turno_id'], $medico_id);
        $stmt->execute();
    } else {
        // Nuevo turno
        $stmt = $conn->prepare("INSERT INTO turnos (paciente_id, medico_id, fecha, motivo, notas, estado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $paciente_id, $medico_id, $datetime, $motivo, $notas, $estado);
        $stmt->execute();
    }

    header("Location: turnos.php");
    exit;
}

// OBTENER TURNOS DEL MÉDICO
$turnos = [];
$sql = "SELECT t.*, p.nombre, p.apellido, p.dni, p.obra_social 
        FROM turnos t
        JOIN pacientes p ON p.id = t.paciente_id
        WHERE t.medico_id = ?
        ORDER BY t.fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $medico_id);
$stmt->execute();
$result = $stmt->get_result();
$turnos = $result->fetch_all(MYSQLI_ASSOC);

// OBTENER PACIENTES PARA EL SELECT
$pacientes = [];
$sql_p = "SELECT id, nombre, apellido FROM pacientes";
$res_p = $conn->query($sql_p);
$pacientes = $res_p->fetch_all(MYSQLI_ASSOC);

// Si se edita un turno
$editar_turno = null;
if ($action === 'editar' && $turno_id) {
    $stmt = $conn->prepare("SELECT * FROM turnos WHERE id=? AND medico_id=?");
    $stmt->bind_param("ii", $turno_id, $medico_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $editar_turno = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Turnos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Turnos del Médico</h3>

    <!-- LISTA DE TURNOS -->
    <table class="table table-bordered bg-white mb-4">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>DNI</th>
                <th>Obra Social / Prepaga</th>
                <th>Fecha y Hora</th>
                <th>Motivo</th>
                <th>Notas</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($turnos as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['apellido'] . ' ' . $t['nombre']) ?></td>
                <td><?= htmlspecialchars($t['dni']) ?></td>
                <td><?= htmlspecialchars($t['obra_social']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($t['fecha'])) ?></td>
                <td><?= htmlspecialchars($t['motivo']) ?></td>
                <td><?= htmlspecialchars($t['notas']) ?></td>
                <td><?= ucfirst($t['estado']) ?></td>
                <td><a href="turnos.php?action=editar&id=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Editar</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- FORMULARIO AGREGAR / EDITAR TURNO -->
    <h4><?= $editar_turno ? 'Editar Turno' : 'Agregar Nuevo Turno' ?></h4>
    <form method="POST" class="row g-3 bg-white p-3 border rounded">
        <input type="hidden" name="turno_id" value="<?= $editar_turno['id'] ?? '' ?>">
        <div class="col-md-4">
            <label>Paciente</label>
            <select name="paciente_id" class="form-select" required>
                <option value="">Seleccione un paciente</option>
                <?php foreach($pacientes as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($editar_turno && $editar_turno['paciente_id']==$p['id'])?'selected':'' ?>>
                        <?= htmlspecialchars($p['apellido'] . ' ' . $p['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label>Fecha</label>
            <input type="date" name="fecha" class="form-control" value="<?= $editar_turno ? date('Y-m-d', strtotime($editar_turno['fecha'])) : '' ?>" required>
        </div>
        <div class="col-md-2">
            <label>Hora</label>
            <input type="time" name="hora" class="form-control" value="<?= $editar_turno ? date('H:i', strtotime($editar_turno['fecha'])) : '' ?>" required>
        </div>
        <div class="col-md-2">
            <label>Motivo</label>
            <select name="motivo" class="form-select" required>
                <?php
                $motivos = ['Consulta general','Control','Resultado laboratorio','Ecografía','Otros'];
                foreach($motivos as $m){
                    $selected = ($editar_turno && $editar_turno['motivo']==$m)?'selected':'';
                    echo "<option value='$m' $selected>$m</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <label>Estado</label>
            <select name="estado" class="form-select">
                <?php
                $estados = ['pendiente','confirmado','cancelado'];
                foreach($estados as $e){
                    $selected = ($editar_turno && $editar_turno['estado']==$e)?'selected':'';
                    echo "<option value='$e' $selected>".ucfirst($e)."</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-12">
            <label>Notas</label>
            <textarea name="notas" class="form-control"><?= $editar_turno['notas'] ?? '' ?></textarea>
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary"><?= $editar_turno ? 'Actualizar Turno' : 'Agregar Turno' ?></button>
            <a href="dashboard.php" class="btn btn-secondary">Volver al Menú</a>
        </div>
    </form>
</div>
</body>
</html>

