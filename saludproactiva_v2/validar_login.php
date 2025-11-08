<?php
session_start();
include "conexion.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM medico_registrado WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $medico = $result->fetch_assoc();

    if (password_verify($password, $medico['password'])) {
        $_SESSION['medico'] = $medico['nombre'];
        header("Location: panel.php");
        exit();
    } else {
        echo "❌ Contraseña incorrecta.";
    }
} else {
    echo "❌ Médico no encontrado.";
}
?>
