<?php
session_start();
if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION['medico_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Médico</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .icon-card {
        text-align: center;
        padding: 20px;
        transition: transform 0.2s;
        cursor: pointer;
        position: relative;
    }
    .icon-card:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .icon-card img {
        max-width: 80px;
        margin-bottom: 10px;
    }
    .icon-card h6 {
        margin-top: 5px;
    }
    .pacientes-menu {
        display: none;
        flex-direction: column;
        gap: 5px;
        margin-top: 10px;
    }
</style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Bienvenido Dr./Dra. <?= htmlspecialchars($nombre) ?> 👋</h3>
    <p>Panel de control de SaludProactiva</p>

    <div class="row g-3 mt-3">
        <!-- Pacientes -->
        <div class="col-md-3 col-6">
            <div class="card icon-card" id="pacientesCard" onclick="toggleMenu('pacientesMenu')">
                <img src="icons/pacientes.png" alt="Pacientes">
                <h6>Pacientes</h6>
                <div class="pacientes-menu" id="pacientesMenu">
                    <a href="paciente.php" class="btn btn-sm btn-primary">Ingresar Paciente</a>
                    <a href="buscar_paciente.php" class="btn btn-sm btn-success">Buscar Paciente</a>
                </div>
            </div>
        </div>

        <!-- Turnos -->
        <div class="col-md-3 col-6">
            <a href="turnos.php" class="text-decoration-none text-dark">
                <div class="card icon-card" onmouseenter="closeMenu('pacientesMenu')">
                    <img src="icons/turnos.png" alt="Turnos">
                    <h6>Turnos</h6>
                </div>
            </a>
        </div>

        <!-- Profesionales -->
        <div class="col-md-3 col-6">
            <a href="profesionales.php" class="text-decoration-none text-dark">
                <div class="card icon-card" onmouseenter="closeMenu('pacientesMenu')">
                    <img src="icons/profesionales.png" alt="Profesionales">
                    <h6>Profesionales</h6>
                </div>
            </a>
        </div>

        <!-- Vademécum -->
        <div class="col-md-3 col-6">
            <a href="vademecum.php" class="text-decoration-none text-dark">
                <div class="card icon-card" onmouseenter="closeMenu('pacientesMenu')">
                    <img src="icons/vademecum.png" alt="Vademécum">
                    <h6>Vademécum</h6>
                </div>
            </a>
        </div>

        <!-- CIE-10 -->
        <div class="col-md-3 col-6">
            <a href="cie10.php" class="text-decoration-none text-dark">
                <div class="card icon-card" onmouseenter="closeMenu('pacientesMenu')">
                    <img src="icons/cie10.png" alt="CIE-10">
                    <h6>CIE-10</h6>
                </div>
            </a>
        </div>

        <!-- Recordatorios -->
        <div class="col-md-3 col-6">
            <a href="recordatorios.php" class="text-decoration-none text-dark">
                <div class="card icon-card" onmouseenter="closeMenu('pacientesMenu')">
                    <img src="icons/recordatorios.png" alt="Recordatorios">
                    <h6>Recordatorios</h6>
                </div>
            </a>
        </div>

        <!-- Estadísticas -->
        <div class="col-md-3 col-6">
            <a href="estadisticas.php" class="text-decoration-none text-dark">
                <div class="card icon-card" onmouseenter="closeMenu('pacientesMenu')">
                    <img src="icons/estadisticas.png" alt="Estadísticas">
                    <h6>Estadísticas</h6>
                </div>
            </a>
        </div>

        <!-- Cerrar sesión -->
        <div class="col-md-3 col-6">
            <a href="logout.php" class="text-decoration-none text-dark">
                <div class="card icon-card bg-danger text-white" onmouseenter="closeMenu('pacientesMenu')">
                    <img src="icons/logout.png" alt="Salir">
                    <h6>Salir</h6>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
function toggleMenu(menuId) {
    const menu = document.getElementById(menuId);
    menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
}

// Cierra el menú si el mouse entra en otra tarjeta
function closeMenu(menuId) {
    const menu = document.getElementById(menuId);
    menu.style.display = 'none';
}
</script>

</body>
</html>


