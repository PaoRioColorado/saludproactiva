<?php

// Configuración de la conexión a la base de datos
// Estas variables deben coincidir con la configuración de tu XAMPP y phpMyAdmin
$host = '127.0.0.1'; // O 'localhost'. Es la dirección de tu servidor local.
$db   = 'saludproactiva'; // El nombre de la base de datos que creaste.
$user = 'root'; // El usuario por defecto de XAMPP para MySQL/MariaDB.
$pass = ''; // La contraseña del usuario 'root' en XAMPP es vacía por defecto.
$charset = 'utf8mb4'; // La codificación de caracteres.

// Construcción del DSN (Data Source Name)
// Es una cadena que contiene toda la información necesaria para la conexión.
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones de configuración para la conexión PDO
$options = [
    // Manejo de errores: PDO lanzará una excepción en caso de error.
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Modo de obtención de datos por defecto: devuelve un array asociativo.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Desactiva la emulación de sentencias preparadas para seguridad.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // Intenta crear una nueva instancia de PDO (la conexión a la base de datos)
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     // Si la conexión es exitosa, muestra este mensaje
     echo "¡Conexión exitosa a la base de datos!";

} catch (\PDOException $e) {
     // Si la conexión falla, captura la excepción y muestra el mensaje de error.
     // Esto es útil para la depuración.
     echo "Error de conexión: " . $e->getMessage();
     // También puedes lanzar la excepción si prefieres manejarla en otro lugar
     // throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>