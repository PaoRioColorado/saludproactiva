<?php
session_start();
$medico_nombre = $_SESSION['medico_nombre'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Búsqueda rápida de códigos CIE | SaludProactiva</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Hacer que el footer quede siempre abajo */
html, body {
    height: 100%;
    margin: 0;
}
body {
    display: flex;
    flex-direction: column;
}
.container-fluid.content-center {
    flex: 1;
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
}

/* Navbar */
.navbar { background-color: #212529; padding-top: 0.2rem; padding-bottom: 0.2rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #ffffff; border: 1px solid #212529; border-radius: 5px; margin-right: 0.5rem; padding: 2px 10px; font-size: 0.9rem; transition: background-color 0.3s, color 0.3s, transform 0.2s, box-shadow 0.3s; }
.navbar .btn-menu:hover { background-color: #6c757d; color: #ffffff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
.navbar-text { color: #ffffff; background-color: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 5px; margin-right: 0.5rem; font-weight: 500; font-size: 0.9rem; }
.btn-salir { background-color: #dc3545; color: #ffffff; border: none; transition: background-color 0.3s; padding: 2px 10px; font-size: 0.9rem; }
.btn-salir:hover { background-color: #b02a37; }

/* Contenido principal */
.content-center { text-align: center; margin-top: 1.5rem; margin-bottom: 1.5rem; }
.content-center h1 { color: #212529; font-weight: 600; }
.content-center p { color: #495057; font-size: 1rem; }
.main-container { display: flex; gap: 20px; align-items: flex-start; margin: 20px; }
.cie-img img { width: 600px; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.cie-list { flex: 1; }
.code-btn { margin: 2px 0; display: block; text-align: left; width: 100%; }
#selectedCode { margin-top: 20px; font-weight: bold; }
.subcat-title { font-weight: bold; margin-top: 10px; }

/* Footer */
footer { background-color: #212529; color: #ffffff; text-align: center; padding: 12px 0; }
footer a { color: #0d6efd; text-decoration: none; font-weight: bold; }
footer a:hover { text-decoration: underline; }

a:focus, button:focus { outline: 3px solid #0d6efd; outline-offset: 2px; }
</style>
</head>
<body>

<a href="#main-content" class="visually-hidden-focusable">Saltar al contenido</a>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php"><img src="icons/logo.png" alt="Logo de SaludProactiva"></a>
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

<div class="container-fluid content-center" role="main" id="main-content">
  <h1 class="mb-2">Búsqueda rápida de códigos CIE</h1>
  <p class="text-muted mb-4">Seleccione un código o busque por título</p>

  <div class="main-container">
    <div class="cie-img">
      <img src="icons/cie10_img.png" alt="Imagen CIE-10">
    </div>
    <div class="cie-list">
      <input type="text" id="searchInput" class="form-control mb-3" placeholder="Buscar por título o código...">
      <div class="accordion" id="cieAccordion">

        <!-- Categorías existentes -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading1">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
              Enfermedades infecciosas intestinales
            </button>
          </h2>
          <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
            <div class="accordion-body">
              <button class="btn btn-outline-primary code-btn" data-code="A00">A00 - Cólera</button>
              <button class="btn btn-outline-primary code-btn" data-code="A01">A01 - Fiebres tifoidea y paratifoidea</button>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="heading7">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7">
              Enfermedades cardiovasculares
            </button>
          </h2>
          <div id="collapse7" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
            <div class="accordion-body">
              <button class="btn btn-outline-primary code-btn" data-code="I10">I10 - Hipertensión esencial</button>
              <button class="btn btn-outline-primary code-btn" data-code="I20">I20 - Angina de pecho</button>
              <button class="btn btn-outline-primary code-btn" data-code="I21">I21 - Infarto agudo de miocardio</button>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="heading8">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8">
              Enfermedades respiratorias crónicas
            </button>
          </h2>
          <div id="collapse8" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
            <div class="accordion-body">
              <button class="btn btn-outline-primary code-btn" data-code="J44">J44 - EPOC</button>
              <button class="btn btn-outline-primary code-btn" data-code="J45">J45 - Asma</button>
              <button class="btn btn-outline-primary code-btn" data-code="J46">J46 - Estado asmático grave</button>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="heading9">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse9">
              Enfermedades endocrinas
            </button>
          </h2>
          <div id="collapse9" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
            <div class="accordion-body">
              <button class="btn btn-outline-primary code-btn" data-code="E10">E10 - Diabetes mellitus tipo 1</button>
              <button class="btn btn-outline-primary code-btn" data-code="E11">E11 - Diabetes mellitus tipo 2</button>
              <button class="btn btn-outline-primary code-btn" data-code="E12">E12 - Diabetes malnutricional</button>
            </div>
          </div>
        </div>

        <!-- NUEVA CATEGORÍA AGREGADA -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading10">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse10">
              Enfermedades digestivas
            </button>
          </h2>
          <div id="collapse10" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
            <div class="accordion-body">
              <button class="btn btn-outline-primary code-btn" data-code="K20">K20 - Esofagitis</button>
              <button class="btn btn-outline-primary code-btn" data-code="K21">K21 - Reflujo gastroesofágico</button>
              <button class="btn btn-outline-primary code-btn" data-code="K25">K25 - Úlcera gástrica</button>
            </div>
          </div>
        </div>

      </div>

      <div id="selectedCode">Código seleccionado: <span id="selectedCodeText">Ninguno</span></div>
    </div>
  </div>
</div>

<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const searchInput = document.getElementById('searchInput');
const codeButtons = document.querySelectorAll('.code-btn');
const selectedCodeText = document.getElementById('selectedCodeText');

searchInput.addEventListener('keyup', function() {
    const filter = searchInput.value.toLowerCase();
    codeButtons.forEach(btn => {
        const text = btn.textContent.toLowerCase();
        btn.style.display = text.includes(filter) ? '' : 'none';
    });
});

codeButtons.forEach(btn => {
    btn.addEventListener('click', function() {
        selectedCodeText.textContent = btn.getAttribute('data-code') + ' - ' + btn.textContent.split(' - ')[1];
        const accordionItems = document.querySelectorAll('.accordion-collapse');
        accordionItems.forEach(item => {
            const bsCollapse = bootstrap.Collapse.getInstance(item);
            if(bsCollapse) bsCollapse.hide();
        });
    });
});
</script>
</body>
</html>

