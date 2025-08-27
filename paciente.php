<?php
session_start();
require 'conexion.php'; // conexión a la base de datos

// ID del paciente (para editar)
$paciente_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cargar datos si es edición
$paciente = [];
if ($paciente_id > 0) {
    $sql = "SELECT * FROM pacientes WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $paciente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $paciente = $result->fetch_assoc() ?: [];
}

// Guardar datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apellido = $_POST['apellido'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?: null;
    $edad = $_POST['edad'] ?: null;
    $estado_civil = $_POST['estado_civil'] ?? '';
    $grupo_sanguineo = $_POST['grupo_sanguineo'] ?? '';
    $movil = $_POST['movil'] ?? '';
    $email = $_POST['email'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $obra_social = $_POST['obra_social'] ?? '';
    $poliza = $_POST['poliza'] ?? '';
    $enfermedades = $_POST['enfermedades'] ?? '';

    if ($paciente_id > 0) {
        // Actualizar paciente
        $sql_update = "UPDATE pacientes SET 
            apellido=?, nombre=?, dni=?, genero=?, estado=?, fecha_nacimiento=?, edad=?, estado_civil=?, grupo_sanguineo=?,
            movil=?, email=?, direccion=?, obra_social=?, poliza=?, enfermedades=?
            WHERE id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param(
            "sssssssssssssssi",
            $apellido, $nombre, $dni, $genero, $estado, $fecha_nacimiento, $edad, $estado_civil, $grupo_sanguineo,
            $movil, $email, $direccion, $obra_social, $poliza, $enfermedades, $paciente_id
        );
        $stmt_update->execute();
        echo "<div class='alert alert-success'>Paciente actualizado correctamente.</div>";
    } else {
        // Insertar nuevo paciente
        $sql_insert = "INSERT INTO pacientes 
            (apellido, nombre, dni, genero, estado, fecha_nacimiento, edad, estado_civil, grupo_sanguineo, movil, email, direccion, obra_social, poliza, enfermedades)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param(
            "sssssssssssssss",
            $apellido, $nombre, $dni, $genero, $estado, $fecha_nacimiento, $edad, $estado_civil, $grupo_sanguineo,
            $movil, $email, $direccion, $obra_social, $poliza, $enfermedades
        );
        $stmt_insert->execute();
        echo "<div class='alert alert-success'>Paciente ingresado correctamente.</div>";
        $paciente_id = $conn->insert_id; // para editar después si se desea
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ficha Paciente</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .form-section { border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin-bottom: 20px; background-color: #fff; }
    .form-section h5 { margin-bottom: 15px; font-weight: 600; }
</style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <form method="POST" action="">
        <div class="d-flex justify-content-between mb-3">
            <div>
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="dashboard.php" class="btn btn-secondary">Volver al Menú</a>
            </div>
        </div>

        <!-- Datos Personales -->
        <div class="form-section">
            <h5>Datos Personales</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label>Apellido</label>
                    <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($paciente['apellido'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label>Nombre</label>
                    <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($paciente['nombre'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label>DNI</label>
                    <input type="text" class="form-control" name="dni" value="<?= htmlspecialchars($paciente['dni'] ?? '') ?>">
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-2">
                    <label>Género</label>
                    <select class="form-select" name="genero">
                        <option value="">--</option>
                        <option <?= (isset($paciente['genero']) && $paciente['genero']=='Varón')?'selected':'' ?>>Varón</option>
                        <option <?= (isset($paciente['genero']) && $paciente['genero']=='Mujer')?'selected':'' ?>>Mujer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">--</option>
                        <option <?= (isset($paciente['estado']) && $paciente['estado']=='Alta')?'selected':'' ?>>Alta</option>
                        <option <?= (isset($paciente['estado']) && $paciente['estado']=='Baja')?'selected':'' ?>>Baja</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Fecha Nacimiento</label>
                    <input type="date" class="form-control" name="fecha_nacimiento" value="<?= $paciente['fecha_nacimiento'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label>Edad</label>
                    <input type="number" class="form-control" name="edad" value="<?= $paciente['edad'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label>Estado Civil</label>
                    <input type="text" class="form-control" name="estado_civil" value="<?= htmlspecialchars($paciente['estado_civil'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label>Grupo Sanguíneo</label>
                    <input type="text" class="form-control" name="grupo_sanguineo" value="<?= htmlspecialchars($paciente['grupo_sanguineo'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- Contacto y Obra Social -->
        <div class="form-section">
            <h5>Contacto y Obra Social</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label>Móvil / Celular</label>
                    <input type="text" class="form-control" name="movil" value="<?= htmlspecialchars($paciente['movil'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($paciente['email'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label>Dirección</label>
                    <input type="text" class="form-control" name="direccion" value="<?= htmlspecialchars($paciente['direccion'] ?? '') ?>">
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label>Obra Social / Prepaga</label>
                    <input type="text" class="form-control" name="obra_social" value="<?= htmlspecialchars($paciente['obra_social'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label>Número Obra Social / Prepaga</label>
                    <input type="text" class="form-control" name="poliza" value="<?= htmlspecialchars($paciente['poliza'] ?? '') ?>">
                </div>
            </div>

            <!-- Enfermedades -->
            <div class="row g-3 mt-3">
                <div class="col-md-12">
                    <label>Enfermedades</label>
                    <textarea class="form-control" name="enfermedades" rows="3"><?= htmlspecialchars($paciente['enfermedades'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
</html>



