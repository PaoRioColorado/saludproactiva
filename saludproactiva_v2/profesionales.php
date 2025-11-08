<?php
session_start();
$medico_nombre = $_SESSION['medico_nombre'] ?? null;

$profesionales = [
    ['nombre' => 'Guillermo Albizua', 'especialidad' => 'Ortopedia y Traumatología', 'telefono' => '2914000001'],
    ['nombre' => 'Guadalupe Alduvino Vacas', 'especialidad' => 'Ginecología y Obstetricia', 'telefono' => '2914000002'],
    ['nombre' => 'Luciano Caraballo', 'especialidad' => 'Ginecología y Uroginecología', 'telefono' => '2914000003'],
    ['nombre' => 'Victoria B. Gimenez', 'especialidad' => 'Psicología - Adolescentes y Adultos', 'telefono' => '2914000004'],
    ['nombre' => 'Osvaldo Giorgetti', 'especialidad' => 'Especialista Consultor en Cirugía', 'telefono' => '2914000005'],
    ['nombre' => 'María Laura Iturburu', 'especialidad' => 'Nutricionista', 'telefono' => '2914000006'],
    ['nombre' => 'Ricardo Javier Lucero', 'especialidad' => 'Especialista en Columna Adultos y Niños', 'telefono' => '2914000007'],
    ['nombre' => 'Juan Pedro Molina', 'especialidad' => 'Endocrinología y Diabetes', 'telefono' => '2914000008'],
    ['nombre' => 'Carlos Moyano', 'especialidad' => 'Especialista en Columna Adultos y Niños', 'telefono' => '2914000009'],
    ['nombre' => 'Juan Pedro Pesci', 'especialidad' => 'Ortopedia y Traumatología', 'telefono' => '2914000010'],
    ['nombre' => 'María Rosa Romero', 'especialidad' => 'Clínica Médica', 'telefono' => '2914000011'],
    ['nombre' => 'Matías Sabbatini', 'especialidad' => 'Cirugía Gral. Adultos', 'telefono' => '2914000012'],
    ['nombre' => 'Valeria Salsi', 'especialidad' => 'Nutricionista', 'telefono' => '2914000013'],
    ['nombre' => 'Oliver Schamis', 'especialidad' => 'Ortopedia y Traumatología', 'telefono' => '2914000014'],
    ['nombre' => 'María Paula Sofio', 'especialidad' => 'Clínica Médica', 'telefono' => '2914000015'],
    ['nombre' => 'Mauricio Traversaro', 'especialidad' => 'Cardiología - ECG - Riesgo Quirúrgico', 'telefono' => '2914000016'],
    ['nombre' => 'Gisela Urriaga', 'especialidad' => 'Medicina Gral. y Familiar', 'telefono' => '2914000017'],
    ['nombre' => 'Sonia Alejandra Vazquez', 'especialidad' => 'Clínica Médica', 'telefono' => '2914000018'],
];

// Obtener lista de especialidades únicas
$especialidades = array_unique(array_map(fn($p) => $p['especialidad'], $profesionales));
sort($especialidades);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Profesionales | SaludProactiva</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script>
function mostrarTelefono(numero) {
    alert("Teléfono de contacto: " + numero);
}

// Función de filtrado en tiempo real
function filtrarTabla() {
    let inputNombre = document.getElementById('filtroNombre').value.toLowerCase();
    let filtroEspecialidad = document.getElementById('filtroEspecialidad').value;
    let table = document.getElementById('tablaProfesionales');
    let rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // saltamos encabezado
        let nombre = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
        let especialidad = rows[i].getElementsByTagName('td')[1].textContent;

        if (nombre.includes(inputNombre) && (filtroEspecialidad === "" || especialidad === filtroEspecialidad)) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}
</script>
<style>
/* Navbar compacto */
.navbar {
  background-color: #212529;
  padding-top: 0.2rem;
  padding-bottom: 0.2rem;
}
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #ffffff; border: 1px solid #212529; border-radius: 5px; margin-right: 0.5rem; padding: 2px 10px; font-size: 0.9rem; transition: 0.3s; }
.navbar .btn-menu:hover { background-color: #6c757d; color: #ffffff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2);}
.navbar-text { color: #ffffff; background-color: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 5px; margin-right: 0.5rem; font-weight: 500; font-size: 0.9rem;}
.btn-salir { background-color: #dc3545; color: #ffffff; border: none; padding: 2px 10px; font-size: 0.9rem; transition: 0.3s;}
.btn-salir:hover { background-color: #b02a37; }

/* Contenido moderno */
.content-center { margin-top: 1.5rem; margin-bottom: 1.5rem; }
.table-striped tbody tr:hover { background-color: #f2f2f2; cursor: pointer; transition: background-color 0.2s; }

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
        <li class="nav-item"><a class="btn btn-menu" href="buscar_paciente.php">Pacientes</a></li>
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

<!-- CONTENIDO -->
<div class="container content-center">
    <h3 class="mb-3">Profesionales de la Salud</h3>

    <!-- Filtros -->
    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <input type="text" id="filtroNombre" class="form-control" placeholder="Buscar por nombre" onkeyup="filtrarTabla()">
        </div>
        <div class="col-md-6 mb-2">
            <select id="filtroEspecialidad" class="form-select" onchange="filtrarTabla()">
                <option value="">Todas las especialidades</option>
                <?php foreach($especialidades as $esp): ?>
                    <option value="<?= htmlspecialchars($esp) ?>"><?= htmlspecialchars($esp) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Tabla -->
    <table class="table table-bordered table-striped bg-white" id="tablaProfesionales">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Especialidad</th>
                <th>Contacto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($profesionales as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['especialidad']) ?></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="mostrarTelefono('<?= $p['telefono'] ?>')">Contactar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button class="btn btn-secondary mt-3" onclick="window.location.href='dashboard.php'">Volver al menú</button>
</div>

<!-- FOOTER -->
<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
