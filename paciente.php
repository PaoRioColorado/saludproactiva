<?php
session_start();
require 'conexion.php';
$medico_nombre = $_SESSION['medico_nombre'] ?? null;

// Guardar paciente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $estado = $_POST['estado'];
    $fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $obra_social = $_POST['obra_social'];
    $num_afiliado = $_POST['num_afiliado'];
    $enfermedades = $_POST['enfermedades'];

    // Calcular edad desde la fecha de nacimiento
    $edad = null;
    if (!empty($fecha_nacimiento)) {
        $fecha_nac = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nac)->y;
    }

    $stmt = $conn->prepare("INSERT INTO pacientes 
        (nombre, apellido, dni, estado, fecha_nacimiento, edad, telefono, email, direccion, obra_social, num_afiliado, enfermedades) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssiisssss", 
        $nombre, $apellido, $dni, $estado, $fecha_nacimiento, $edad,
        $telefono, $email, $direccion, $obra_social, $num_afiliado, $enfermedades);

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
<title>Ficha Paciente | SaludProactiva</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Navbar oscuro con botones */
.navbar { background-color: #212529; padding-top: 0.2rem; padding-bottom: 0.2rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #ffffff; border: 1px solid #212529; border-radius: 5px; margin-right: 0.5rem; padding: 2px 10px; font-size: 0.9rem; transition: 0.3s; }
.navbar .btn-menu:hover { background-color: #6c757d; color: #ffffff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2);}
.navbar-text { color: #ffffff; background-color: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 5px; margin-right: 0.5rem; font-weight: 500; font-size: 0.9rem;}
.btn-salir { background-color: #dc3545; color: #ffffff; border: none; padding: 2px 10px; font-size: 0.9rem; transition: 0.3s;}
.btn-salir:hover { background-color: #b02a37; }

/* Contenido centrado */
.content-center { margin-top: 1.5rem; margin-bottom: 1.5rem; max-width: 900px; }

/* Footer */
footer { background-color: #212529; color: #ffffff; text-align: center; padding: 12px 0; }
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
        <li class="nav-item"><a class="btn btn-menu" href="buscar_paciente.php">Pacientes</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="turnos.php">Turnos</a></li>   
        <li class="nav-item"><a class="btn btn-menu" href="estadisticas.php">Estadísticas</a></li>
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

<!-- CONTENIDO -->
<div class="container content-center">
    <h2 class="mb-4">Ficha de Paciente</h2>

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
                <input type="text" name="num_afiliado" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label>Enfermedades</label>
            <textarea name="enfermedades" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="dashboard.php" class="btn btn-secondary">Menú Principal</a>
    </form>
</div>

<!-- FOOTER -->
<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
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
        if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }
        document.getElementById("edad").value = edad;
    }
}
</script>

</body>
</html>

