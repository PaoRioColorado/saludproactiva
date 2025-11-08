<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$medico_nombre = $_SESSION['medico_nombre'] ?? 'Dr. SaludProactiva';

// Obtener pacientes
$pacientes = [];
$sql = "SELECT id, nombre, apellido, dni FROM pacientes ORDER BY nombre";
$result = $conn->query($sql);
if($result){
    while($row = $result->fetch_assoc()){
        $pacientes[] = $row;
    }
}

// Controles
$controles = [];
$sql = "SELECT * FROM controles ORDER BY fecha_proximo_control ASC";
$result = $conn->query($sql);
if($result){
    while($row = $result->fetch_assoc()){
        $controles[$row['paciente_id']][] = $row;
    }
}

// Estudios
$estudios = [];
$sql = "SELECT * FROM estudios ORDER BY proximo_control ASC";
$result = $conn->query($sql);
if($result){
    while($row = $result->fetch_assoc()){
        $estudios[$row['paciente_id']][] = $row;
    }
}

// Medicación
$medicacion = [];
$sql = "SELECT * FROM medicacion ORDER BY paciente_id ASC";
$result = $conn->query($sql);
if($result){
    while($row = $result->fetch_assoc()){
        $medicacion[$row['paciente_id']][] = $row;
    }
}

// Cargar CIE-10 CSV
$cie_file = __DIR__ . "/tabla-salud_cie10.csv";
$cie_data = [];
if (($handle = fopen($cie_file, "r")) !== false) {
    $header = fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $cie_data[] = [
            'codigo' => $row[0],
            'titulo' => $row[1],
            'sintomas' => isset($row[2]) ? array_map('trim', explode(',', $row[2])) : [],
            // Probabilidad de complicaciones si no se trata (ejemplo)
            'complicaciones' => $row[3] ?? ''
        ];
    }
    fclose($handle);
}

// Cargar medicamentos CSV
$med_file = __DIR__ . "/vademecum 2018.csv";
$med_data = [];
if (($handle = fopen($med_file, "r")) !== false) {
    $header = fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $med_data[] = [
            'nombre' => $row[0],
            'principio' => $row[1],
            'presentacion' => $row[2],
            'posologia' => $row[3] ?? '',
            'aprobado' => $row[4] ?? ''
        ];
    }
    fclose($handle);
}

// Función para limpiar texto de palabras como "control", "consulta", etc.
function limpiar_sintoma($s){
    $s = strtolower($s);
    $s = str_replace(['control','consulta','examen'], '', $s);
    return trim($s);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Controles de Pacientes | SaludProactiva</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body, html { height: 100%; margin:0; font-family: 'Segoe UI', sans-serif; display:flex; flex-direction:column; }
.navbar { background-color: #212529; }
.navbar .btn-menu { background-color: #212529; color: #fff; margin-right:0.5rem; }
.navbar .btn-menu:hover { background-color: #6c757d; }
.navbar-text { color:#fff; margin-right:0.5rem; }
.btn-salir { background-color:#dc3545; color:#fff; border:none; }
.btn-salir:hover { background-color:#b02a37; }
.container { margin-top:2rem; max-width:1200px; flex:1; }

.card { margin-bottom:1rem; }
.card-header { font-weight:600; background-color:#0d6efd; color:#fff; }
.card-body { background-color:#fff; }

.btn-recordatorio {
    background: linear-gradient(135deg,#0dcaf0,#198754);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 8px 16px;
    margin-right:5px;
    font-weight: 600;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    transition: 0.3s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-recordatorio:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    background: linear-gradient(135deg,#198754,#0dcaf0);
}

footer { background-color:#212529; color:#fff; text-align:center; padding:12px 0; }
footer a { color:#0d6efd; text-decoration:none; font-weight:bold; }
footer a:hover { text-decoration:underline; }

.alerta-vencido { color: #dc3545; font-weight: 600; }
.alerta-proximo { color: #ffc107; font-weight: 600; }

#imagen-vacia img { max-height:150px; opacity:0.3; filter: grayscale(50%); }
#imagen-vacia p { color:#6c757d; font-style:italic; margin-top:10px; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">
      <img src="icons/logo.png" alt="Logo" style="height:50px;">
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="btn btn-menu" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="buscar_paciente.php">Pacientes</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="turnos.php">Turnos</a></li>
      </ul>
      <ul class="navbar-nav">
        <?php if($medico_nombre): ?>
          <li class="nav-item"><span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($medico_nombre) ?></span></li>
          <li class="nav-item"><a class="btn btn-salir btn-sm" href="logout.php">Salir</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
<h2 class="mb-4">Controles de Pacientes</h2>

<div class="mb-4">
    <label for="selectPaciente" class="form-label">Seleccione un paciente:</label>
    <select id="selectPaciente" class="form-select">
        <option value="">-- Elegir paciente --</option>
        <?php foreach($pacientes as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?> (<?= $p['dni'] ?>)</option>
        <?php endforeach; ?>
    </select>
</div>

<div id="imagen-vacia" style="text-align:center; margin-top:50px;">
    <img src="icons/estetoscopio.png" alt="Estetoscopio">
    <p>Seleccione un paciente para ver sus controles</p>
</div>

<div id="paciente-seleccionado">
    <?php foreach($pacientes as $p): ?>
        <div class="card paciente-card" data-paciente-id="<?= $p['id'] ?>" style="display:none;">
            <div class="card-header">
                Controles de <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>
            </div>
            <div class="card-body">
                <?php if(isset($controles[$p['id']])): ?>
                    <h6>Controles:</h6>
                    <ul>
                    <?php
                    $paciente_sintomas = array_map(function($c){ return limpiar_sintoma($c['tipo_control']); }, $controles[$p['id']]);
                    foreach($controles[$p['id']] as $c):
                        $hoy = new DateTime();
                        $fecha_prox = new DateTime($c['fecha_proximo_control']);
                        $clase_alerta = '';
                        if($fecha_prox < $hoy) $clase_alerta = 'alerta-vencido';
                        elseif($fecha_prox <= (new DateTime())->modify('+3 days')) $clase_alerta = 'alerta-proximo';
                    ?>
                        <li>
                            <strong>Tipo:</strong> <?= htmlspecialchars($c['tipo_control']) ?> |
                            <strong>Último:</strong> <?= $c['fecha_ultimo_control'] ?> |
                            <strong>Próximo:</strong> <span class="<?= $clase_alerta ?>"><?= $c['fecha_proximo_control'] ?></span>
                            <br>
                            <button class="btn-recordatorio" onclick="enviarRecordatorio('<?= htmlspecialchars($p['nombre']) ?>','control','<?= htmlspecialchars($c['tipo_control']) ?>')">🔔 Recordatorio</button>
                        </li>
                    <?php endforeach; ?>
                    </ul>

                    <?php
                    // Pronóstico / coincidencias CIE-10
                    $cie_matches = [];
                    foreach($cie_data as $cie){
                        foreach($cie['sintomas'] as $s){
                            foreach($paciente_sintomas as $ps){
                                if(stripos($ps, trim($s)) !== false || stripos(trim($s), $ps) !== false){
                                    $cie_matches[$cie['codigo']] = $cie;
                                }
                            }
                        }
                    }

                    if(count($cie_matches) > 0){
                        echo "<h6 class='mt-3'>Coincidencias CIE-10 para los síntomas registrados:</h6><ul>";
                        $meds_global = []; // Para no repetir medicamentos
                        foreach($cie_matches as $cod => $cie){
                            echo "<li><strong>$cod</strong>: {$cie['titulo']}</li>";
                            if(!empty($cie['complicaciones'])){
                                echo "<p><em>Probabilidad de complicaciones si no se trata: {$cie['complicaciones']}</em></p>";
                            }
                            // Medicamentos sugeridos
                            foreach($med_data as $m){
                                if(stripos($cie['titulo'], $m['nombre']) !== false || stripos($cie['titulo'], $m['principio']) !== false){
                                    $meds_global[$m['nombre']] = $m;
                                }
                            }
                        }
                        echo "</ul>";

                        if(count($meds_global) > 0){
                            echo "<h6 class='mt-2'>Medicamentos sugeridos:</h6><ul>";
                            foreach($meds_global as $m){
                                echo "<li><strong>{$m['nombre']}</strong> ({$m['principio']}), {$m['presentacion']}, Posología: {$m['posologia']}</li>";
                            }
                            echo "</ul>";
                        }
                    } else {
                        echo "<p class='text-danger mt-2'>No se encontraron coincidencias en CIE-10 para los síntomas registrados.</p>";
                    }
                    ?>
                <?php else: ?>
                    <p>No hay controles registrados.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>

<footer>
<small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const selectPaciente = document.getElementById('selectPaciente');
const cards = document.querySelectorAll('.paciente-card');
const imagenVacia = document.getElementById('imagen-vacia');

selectPaciente.addEventListener('change', function(){
    const val = this.value;
    if(val === ""){
        imagenVacia.style.display = 'block';
    } else {
        imagenVacia.style.display = 'none';
    }
    cards.forEach(card => {
        card.style.display = card.dataset.pacienteId === val ? 'block' : 'none';
    });
});

function enviarRecordatorio(nombre, tipo, descripcion){
    const now = new Date().toLocaleString();
    alert(`Se envió un recordatorio a ${nombre} para ${tipo}: ${descripcion}\n(${now})`);
}
</script>
</body>
</html>


