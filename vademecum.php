<?php
session_start();
$medico_nombre = $_SESSION['medico_nombre'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Vademécum | SaludProactiva</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Navbar compacta sin modificar el logo */
    .navbar {
      background-color: #212529;
      padding-top: 0.2rem;    /* menos espacio arriba */
      padding-bottom: 0.2rem; /* menos espacio abajo */
    }

    /* Logo mantiene su tamaño original */
    .navbar-brand img {
      height: 50px; /* tamaño original */
      width: auto;
    }

    /* Botones del menú oscuros, compactos solo verticalmente */
    .navbar .btn-menu {
      background-color: #212529;
      color: #ffffff;
      border: 1px solid #212529;
      border-radius: 5px;
      margin-right: 0.5rem;
      padding: 2px 10px;  /* vertical reducido */
      font-size: 0.9rem;
      transition: background-color 0.3s, color 0.3s, transform 0.2s, box-shadow 0.3s;
    }

    /* Hover: gris, levantado y sombra sutil */
    .navbar .btn-menu:hover {
      background-color: #6c757d;
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    /* Nombre del médico compacto verticalmente */
    .navbar-text {
      color: #ffffff;
      background-color: rgba(255,255,255,0.1);
      padding: 2px 6px; /* vertical reducido */
      border-radius: 5px;
      margin-right: 0.5rem;
      font-weight: 500;
      font-size: 0.9rem;
    }

    /* Botón salir compacto verticalmente */
    .btn-salir {
      background-color: #dc3545;
      color: #ffffff;
      border: none;
      transition: background-color 0.3s;
      padding: 2px 10px; /* vertical reducido */
      font-size: 0.9rem;
    }
    .btn-salir:hover {
      background-color: #b02a37;
    }

    /* Contenido centrado */
    .content-center {
      text-align: center;
      margin-top: 1.5rem;
      margin-bottom: 1.5rem;
    }
    .content-center h1 {
      color: #212529;
      font-weight: 600;
    }
    .content-center p {
      color: #495057;
      font-size: 1rem;
    }

    /* Iframe ocupa casi toda la pantalla */
    iframe {
      width: 100%;
      height: calc(100vh - 200px); /* ajustado al header más compacto */
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Footer */
    footer {
      background-color: #212529;
      color: #ffffff;
      text-align: center;
      padding: 12px 0;
    }
    footer a {
      color: #0d6efd;
      text-decoration: none;
      font-weight: bold;
    }
    footer a:hover {
      text-decoration: underline;
    }

    /* Foco visible para teclado */
    a:focus, button:focus {
      outline: 3px solid #0d6efd;
      outline-offset: 2px;
    }
  </style>
</head>
<body>

<!-- Enlace de salto -->
<a href="#main-content" class="visually-hidden-focusable">Saltar al contenido</a>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <!-- Logo -->
    <a class="navbar-brand" href="dashboard.php" title="Ir al inicio">
      <img src="icons/logo.png" alt="Logo de SaludProactiva">
    </a>

    <!-- Botón hamburguesa -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Menú de navegación">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menú -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="btn btn-menu" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="vademecum.php">Vademécum</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="pacientes.php">Pacientes</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="turnos.php">Turnos</a></li>
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

<!-- CONTENIDO PRINCIPAL -->
<div class="container-fluid content-center" role="main" id="main-content">
  <h1 class="mb-2">Vademécum Alfa Beta</h1>
  <p class="text-muted mb-4">Acceso gratuito al vademécum</p>

  <iframe src="https://www.alfabeta.net/medicamento/index-ar.jsp" title="Vademécum Alfa Beta"></iframe>
</div>

<!-- FOOTER -->
<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

