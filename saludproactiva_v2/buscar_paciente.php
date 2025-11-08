<?php
session_start();
$medico_nombre = $_SESSION['medico_nombre'] ?? 'Dr. SaludProactiva';

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "saludproactiva");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Parámetros de búsqueda (GET)
$dni_buscar = $_GET['dni'] ?? '';
$apellido_buscar = $_GET['apellido'] ?? '';
$dni_select = $_GET['dni_select'] ?? '';

if (!empty($dni_select)) {
    $dni_buscar = $dni_select;
}

$query = "SELECT id, nombre, apellido, dni, fecha_nacimiento, email, telefono FROM pacientes";
$params = [];
$types = "";
$where = [];

if (!empty($dni_buscar)) {
    $where[] = "dni = ?";
    $types .= "s";
    $params[] = $dni_buscar;
} elseif (!empty($apellido_buscar)) {
    $where[] = "apellido LIKE ?";
    $types .= "s";
    $params[] = '%' . $apellido_buscar . '%';
}

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY apellido, nombre";

$stmt = $conexion->prepare($query);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$listado = $conexion->query("SELECT id, nombre, apellido, dni FROM pacientes ORDER BY apellido, nombre");
$listadoPacientes = $listado ? $listado->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Riesgo de Pacientes | SaludProactiva</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.navbar { background-color: #212529; padding: 0.2rem 1rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #fff; border-radius:5px; margin-right:0.5rem; padding:2px 10px; font-size:0.9rem;}
.navbar .btn-menu:hover { background-color: #6c757d; }
.navbar-text { color:#fff; background-color: rgba(255,255,255,0.1); padding:2px 6px; border-radius:5px; font-size:0.9rem;}
.btn-salir { background-color:#dc3545; color:#fff; border:none; padding:2px 10px; font-size:0.9rem;}
.btn-salir:hover { background-color:#b02a37; }
.data-section { border:1px solid #dee2e6; padding:15px; margin-top:15px; border-radius:5px; background:#f8f9fa;}
footer { background-color:#212529; color:#fff; text-align:center; padding:12px 0; }
footer a { color:#0d6efd; text-decoration:none; font-weight:bold; }
footer a:hover { text-decoration:underline; }
body { display:flex; flex-direction:column; min-height:100vh; }
.container-main { flex:1; padding-top:20px; padding-bottom:20px; }
.table-responsive { margin-top:15px; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php"><img src="icons/logo.png" alt="Logo"></a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="btn btn-menu" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="buscar_paciente.php">Pacientes</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="turnos.php">Turnos</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($medico_nombre) ?></span></li>
        <li class="nav-item"><a class="btn btn-salir btn-sm" href="logout.php">Salir</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container container-main">
  <h3 class="mb-3">Buscar Pacientes</h3>

  <div class="row g-3 align-items-end">
    <div class="col-md-4">
      <label for="selectPaciente" class="form-label">Seleccionar paciente (desplegable):</label>
      <form id="form_select" method="GET" action="buscar_paciente.php">
        <div class="input-group">
          <select id="selectPaciente" name="dni_select" class="form-select" onchange="document.getElementById('form_select').submit();">
            <option value="">-- Seleccione --</option>
            <?php foreach ($listadoPacientes as $p): ?>
              <option value="<?= htmlspecialchars($p['dni']) ?>" <?= ($p['dni'] === $dni_buscar || $p['dni'] === $dni_select) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['apellido'] . ", " . $p['nombre'] . " (DNI: " . $p['dni'] . ")") ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>

    <div class="col-md-4">
      <form method="GET" action="buscar_paciente.php" class="d-flex">
        <div class="me-2" style="flex:1">
          <label for="apellido" class="form-label">Buscar por apellido:</label>
          <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Ingrese apellido" value="<?= htmlspecialchars($apellido_buscar) ?>">
        </div>
        <div class="align-self-end">
          <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
      </form>
    </div>

    <div class="col-md-4">
      <form method="GET" action="buscar_paciente.php" class="d-flex">
        <div style="flex:1">
          <label for="dni" class="form-label">Buscar por DNI exacto:</label>
          <input type="text" id="dni" name="dni" class="form-control" placeholder="Ingrese DNI" value="<?= htmlspecialchars($dni_buscar) ?>">
        </div>
        <div class="align-self-end">
          <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <?php if ($result && $result->num_rows > 0): ?>
      <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
          <tr>
            <th>Apellido</th>
            <th>Nombre</th>
            <th>DNI</th>
            <th>Fecha Nac.</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['apellido']) ?></td>
              <td><?= htmlspecialchars($row['nombre']) ?></td>
              <td><?= htmlspecialchars($row['dni']) ?></td>
              <td><?= htmlspecialchars($row['fecha_nacimiento'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['telefono'] ?? '') ?></td>
              <td>
                <a href="paciente.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="ver_controles.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white">Ver controles</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info mt-3">No se encontraron pacientes. Podés probar con otro apellido o mostrar todos seleccionando del desplegable.</div>
    <?php endif; ?>
  </div>
</div>

<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

</body>
</html>

<?php
$stmt->close();
$conexion->close();
?>
