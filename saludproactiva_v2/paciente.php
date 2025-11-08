<?php
session_start();
require 'conexion.php';
$medico_nombre = $_SESSION['medico_nombre'] ?? null;

// Detectar si viene un ID para editar
$editar_id = $_GET['id'] ?? null;
$paciente = [];

if ($editar_id) {
    $stmt = $conn->prepare("SELECT * FROM pacientes WHERE id=?");
    $stmt->bind_param("i", $editar_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $paciente = $resultado->fetch_assoc() ?? [];
    $stmt->close();
}

// Guardar paciente (INSERT o UPDATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $estado = $_POST['estado'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $obra_social = $_POST['obra_social'];
    $num_afiliado = $_POST['num_afiliado'];
    $enfermedades = $_POST['enfermedades'];

    // Calcular edad
    $edad = null;
    if (!empty($fecha_nacimiento)) {
        $fecha_nac = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nac)->y;
    }

    if ($editar_id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE pacientes SET nombre=?, apellido=?, estado=?, fecha_nacimiento=?, edad=?, telefono=?, email=?, direccion=?, obra_social=?, num_afiliado=?, enfermedades=? WHERE id=?");
        $stmt->bind_param("ssssiisssssi",
            $nombre, $apellido, $estado, $fecha_nacimiento, $edad,
            $telefono, $email, $direccion, $obra_social, $num_afiliado, $enfermedades, $editar_id
        );
        $mensaje = $stmt->execute() ? "Paciente actualizado correctamente." : "Error al actualizar: " . $stmt->error;
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO pacientes (nombre, apellido, dni, estado, fecha_nacimiento, edad, telefono, email, direccion, obra_social, num_afiliado, enfermedades) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiisssss",
            $nombre, $apellido, $dni, $estado, $fecha_nacimiento, $edad,
            $telefono, $email, $direccion, $obra_social, $num_afiliado, $enfermedades
        );
        $mensaje = $stmt->execute() ? "Paciente guardado correctamente." : "Error al guardar: " . $stmt->error;
    }

    $stmt->close();
    // Recargar datos del paciente después de guardar
    if ($editar_id) {
        $stmt2 = $conn->prepare("SELECT * FROM pacientes WHERE id=?");
        $stmt2->bind_param("i", $editar_id);
        $stmt2->execute();
        $resultado2 = $stmt2->get_result();
        $paciente = $resultado2->fetch_assoc() ?? [];
        $stmt2->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ficha Paciente | SaludProactiva</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.navbar { background-color: #212529; padding-top: 0.2rem; padding-bottom: 0.2rem; }
.navbar-brand img { height:50px; width:auto; }
.navbar .btn-menu { background-color: #212529; color:#fff; border:1px solid #212529; border-radius:5px; margin-right:0.5rem; padding:2px 10px; font-size:0.9rem; }
.navbar .btn-menu:hover { background-color:#6c757d; }
.navbar-text { color:#fff; background-color: rgba(255,255,255,0.1); padding:2px 6px; border-radius:5px; margin-right:0.5rem; font-weight:500; font-size:0.9rem; }
.btn-salir { background-color:#dc3545; color:#fff; border:none; padding:2px 10px; font-size:0.9rem;}
.btn-salir:hover { background-color:#b02a37; }
.container { max-width:900px; margin-top:20px; margin-bottom:20px; }

footer {
    background-color: #212529;
    color: #ccc;
    text-align: center;
    padding: 15px 0;
    font-size: 0.9rem;
    margin-top: 40px;
}
footer a {
    color: #9dc3e6;
    text-decoration: none;
}
footer a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php"><img src="icons/logo.png" alt="Logo"></a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="btn btn-menu" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="vademecum.php">Vademécum</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="buscar_paciente.php">Pacientes</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($medico_nombre) ?></span></li>
        <li class="nav-item"><a class="btn btn-salir btn-sm" href="logout.php">Salir</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <h2><?= $editar_id ? "Editar Paciente" : "Nuevo Paciente" ?></h2>
    <?php if (!empty($mensaje)) echo '<div class="alert alert-info">' . $mensaje . '</div>'; ?>
    <form method="POST" action="">
        <div class="row mb-3">
            <div class="col">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($paciente['nombre'] ?? '') ?>" required>
            </div>
            <div class="col">
                <label>Apellido</label>
                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($paciente['apellido'] ?? '') ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label>DNI</label>
                <input type="text" name="dni" class="form-control" value="<?= htmlspecialchars($paciente['dni'] ?? '') ?>" <?= $editar_id ? 'readonly' : '' ?> required>
            </div>
            <div class="col">
                <label>Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($paciente['fecha_nacimiento'] ?? '') ?>" onchange="calcularEdad()" required>
            </div>
            <div class="col">
                <label>Edad</label>
                <input type="text" id="edad" name="edad" class="form-control" value="<?= htmlspecialchars($paciente['edad'] ?? '') ?>" readonly>
            </div>
        </div>

        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-select">
                <option value="Alta" <?= ($paciente['estado'] ?? '')==='Alta'?'selected':'' ?>>Alta</option>
                <option value="Baja" <?= ($paciente['estado'] ?? '')==='Baja'?'selected':'' ?>>Baja</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($paciente['telefono'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($paciente['email'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label>Dirección</label>
            <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($paciente['direccion'] ?? '') ?>">
        </div>

        <div class="row mb-3">
            <div class="col">
                <label>Obra Social</label>
                <input type="text" name="obra_social" class="form-control" value="<?= htmlspecialchars($paciente['obra_social'] ?? '') ?>">
            </div>
            <div class="col">
                <label>Número de Afiliado</label>
                <input type="text" name="num_afiliado" class="form-control" value="<?= htmlspecialchars($paciente['num_afiliado'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-3">
            <label>Enfermedades</label>
            <textarea name="enfermedades" class="form-control"><?= htmlspecialchars($paciente['enfermedades'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="dashboard.php" class="btn btn-secondary">Volver</a>
    </form>
</div>

<footer>
    <small>© <?= date('Y') ?> SaludProactiva | Desarrollado por <a href="mailto:paoladf.it@gmail.com">Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function calcularEdad() {
    let fechaNac = document.querySelector('input[name="fecha_nacimiento"]').value;
    if (fechaNac) {
        let hoy = new Date();
        let nacimiento = new Date(fechaNac);
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        let m = hoy.getMonth() - nacimiento.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) edad--;
        document.getElementById("edad").value = edad;
    }
}
</script>

</body>
</html>
