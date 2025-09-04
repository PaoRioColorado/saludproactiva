<?php
include 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($nombre) || empty($email) || empty($password)) {
        $mensaje = "❌ Todos los campos son obligatorios.";
    } else {
        // Encriptar contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Preparar consulta segura
        $stmt = $conn->prepare("INSERT INTO medico_registrado (nombre, email, password) VALUES (?, ?, ?)");

        if ($stmt === false) {
            $mensaje = "❌ Error al preparar la consulta: " . $conn->error;
        } else {
            $stmt->bind_param("sss", $nombre, $email, $hashed_password);

            if ($stmt->execute()) {
                $mensaje = "✅ Registro exitoso.";
            } else {
                if ($conn->errno == 1062) {
                    $mensaje = "❌ El email ya está registrado.";
                } else {
                    $mensaje = "❌ Error al registrar: " . $conn->error;
                }
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Registro de Médico</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>
</body>
</html>
