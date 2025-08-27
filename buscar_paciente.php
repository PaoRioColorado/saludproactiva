<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$pacientes = [];
$buscar = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buscar = $_POST['dni'] ?? '';

    $sql = "SELECT p.*, t.fecha AS proximo_turno
            FROM pacientes p
            LEFT JOIN turnos t ON t.paciente_id = p.id AND t.fecha >= CURDATE()
            WHERE p.dni LIKE ?
            ORDER BY t.fecha ASC";

    $stmt = $conn->prepare($sql);
    $like = "%$buscar%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $pacientes = $result->fetch_all(MYSQLI_ASSOC);
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

    <form method="POST" class="row g-3 mb-3">
        <div class="col-md-4">
            <input type="text" name="dni" class="form-control" placeholder="Ingrese DNI" value="<?= htmlspecialchars($buscar) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
        <div class="col-md-2">
            <a href="dashboard.php" class="btn btn-secondary">Volver al menú</a>
        </div>
    </form>

    <?php if(!empty($pacientes)): ?>
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>DNI</th>
                <th>Obra Social / Prepaga</th>
                <th>Próximo Turno</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pacientes as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['apellido']) ?></td>
                <td><?= htmlspecialchars($p['dni']) ?></td>
                <td><?= htmlspecialchars($p['obra_social']) ?></td>
                <td><?= !empty($p['proximo_turno']) ? $p['proximo_turno'] : '-' ?></td>
                <td><a href="paciente.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php elseif($_SERVER['REQUEST_METHOD']==='POST'): ?>
        <div class="alert alert-info">No se encontraron pacientes con ese DNI.</div>
    <?php endif; ?>
</div>
</body>
</html>

