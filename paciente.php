<?php
session_start();
require 'conexion.php';

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$medico_nombre = $_SESSION['medico_nombre'] ?? '';

// Obtener pacientes desde la base
$query = "SELECT * FROM pacientes ORDER BY nombre ASC";
$resultado = $conn->query($query);

if (!$resultado) {
    die("Error al obtener pacientes: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            margin-top: 40px;
        }
        .table {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .btn-info {
            background-color: #007bff;
            border: none;
        }
        .btn-info:hover {
            background-color: #0056b3;
        }
        footer {
            margin-top: 40px;
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Pacientes del Dr. <?= htmlspecialchars($medico_nombre) ?></h2>

    <?php if ($resultado->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>DNI</th>
                        <th>Fecha de nacimiento</th>
                        <th>Sexo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= htmlspecialchars($row['dni']) ?></td>
                            <td><?= htmlspecialchars($row['fecha_nacimiento']) ?></td>
                            <td><?= htmlspecialchars($row['sexo']) ?></td>
                            <td>
                                <a href="ver_controles.php?dni=<?= urlencode($row['dni']) ?>" class="btn btn-info btn-sm">
                                    Ver controles
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No hay pacientes registrados.</div>
    <?php endif; ?>

    <footer>
        &copy; <?= date('Y') ?> Salud Proactiva | Sistema Médico
    </footer>
</div>

</body>
</html>

