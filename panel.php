<?php
session_start();
if (!isset($_SESSION['medico'])) {
    header("Location: login.php");
    exit();
}
?>

<h2>Bienvenido Dr./Dra. <?php echo $_SESSION['medico']; ?> 👋</h2>
<p>Este es el panel privado de SaludProactiva.</p>
<a href="logout.php">Cerrar sesión</a>
