<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'saludproactiva';

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ No se pudo conectar a la base de datos: " . $conn->connect_error);
}
?>
