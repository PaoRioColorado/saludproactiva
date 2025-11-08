<?php
$host = "localhost";
$user = "root"; 
$pass = "";     
$db   = "saludproactiva";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Error en la conexión: " . $conn->connect_error);
} else {
    echo "✅ Conexión establecida correctamente.<br>";
}

// Verificar si existe la tabla
$sql = "SHOW TABLES LIKE 'medico_registrado'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "✅ La tabla <b>medico_registrado</b> existe en la base de datos.";
} else {
    echo "⚠️ La tabla <b>medico_registrado</b> NO existe. Creala con:<br>
    <pre>
    CREATE TABLE medico_registrado (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    </pre>";
}

$conn->close();
?>
