<?php
session_start();
require 'conexion.php';

// Guardar paciente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $estado = $_POST['estado'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $obra_social = $_POST['obra_social'];
    $numero_afiliado = $_POST['numero_afiliado'];
    $enfermedades = $_POST['enfermedades'];

    // Calcular edad desde la fecha de nacimiento
    $edad = null;
    if (!empty($fecha_nacimiento)) {
        $fecha_nac = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nac)->y;
    }

    $stmt = $conn->prepare("INSERT INTO pacientes 
        (nombre, apellido, dni, estado, fecha_nacimiento, edad, telefono, email, direccion, obra_social, numero_afiliado, enfermedades) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssiisssss", 
        $nombre, $apellido, $dni, $estado, $fecha_nacimiento, $edad,
        $telefono, $email, $direccion, $obra_social, $numero_afiliado, $enfermedades);

    if ($stmt->execute()) {
        $mensaje = "Paciente guardado correctamente.";
    } else {
        $mensaje = "Error al guardar el paciente: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2>Ficha de Paciente</h2>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info"><?= $mensaje ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="row mb-3">
        <div class="col">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="col">
            <label>Apellido</label>
            <input type="text" name="apellido" class="form-control" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label>DNI</label>
            <input type="text" name="dni" class="form-control" required>
        </div>
        <div class="col">
            <label>Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="form-control" onchange="calcularEdad()" required>
        </div>
        <div class="col">
            <label>Edad</label>
            <input type="text" id="edad" name="edad" class="form-control" readonly>
        </div>
    </div>

    <div class="mb-3">
        <label>Estado</label>
        <select name="estado" class="form-select">
            <option value="Alta">Alta</option>
            <option value="Baja">Baja</option>
        </select>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control">
        </div>
        <div class="col">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
    </div>

    <div class="mb-3">
        <label>Dirección</label>
        <input type="text" name="direccion" class="form-control">
    </div>

    <div class="row mb-3">
        <div class="col">
            <label>Obra Social</label>
            <input type="text" name="obra_social" class="form-control">
        </div>
        <div class="col">
            <label>Número de Afiliado</label>
            <input type="text" name="numero_afiliado" class="form-control">
        </div>
    </div>

    <div class="mb-3">
        <label>Enfermedades</label>
        <textarea name="enfermedades" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="dashboard.php" class="btn btn-secondary">Menú Principal</a>
</form>

<script>
function calcularEdad() {
    let fechaNac = document.querySelector('input[name="fecha_nacimiento"]').value;
    if (fechaNac) {
        let hoy = new Date();
        let nacimiento = new Date(fechaNac);
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        let m = hoy.getMonth() - nacimiento.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }
        document.getElementById("edad").value = edad;
    }
}
</script>

</body>
</html>
