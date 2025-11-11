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

// Controles simulados
$tipos_control = [
    'Control alergias' => [3,5,6],
    'Control hipertensión' => [4,6,8],
    'Control isquiotibial' => [1,3,5],
    'Control diabetes' => [3,6,12]
];

$controles = [];
$hoy = new DateTime();
foreach($pacientes as $p){
    $num_controles = rand(2,3); 
    $fecha_inicio = new DateTime('-1 year');
    $controles[$p['id']] = [];
    for($i=0;$i<$num_controles;$i++){
        $tipo_control = array_rand($tipos_control);
        $intervalos = $tipos_control[$tipo_control];
        $intervalo_meses = $intervalos[array_rand($intervalos)];

        $dur_total = rand(3,7); 
        $tiempo_transcurrido = rand(1,$dur_total);

        $fecha_ultimo = clone $fecha_inicio;
        $fecha_prox = clone $fecha_ultimo;
        $fecha_prox->modify("+$intervalo_meses months");

        $controles[$p['id']][] = [
            'tipo_control' => $tipo_control,
            'fecha_ultimo_control' => $fecha_ultimo->format('Y-m-d'),
            'fecha_proximo_control' => $fecha_prox->format('Y-m-d'),
            'dur_total' => $dur_total,
            'tiempo_transcurrido' => $tiempo_transcurrido
        ];

        // Próximo turno a futuro si el tratamiento no terminó
        if($tiempo_transcurrido < $dur_total){
            $fecha_futuro = clone $fecha_prox;
            $fecha_futuro->modify("+1 month");
            $controles[$p['id']][] = [
                'tipo_control' => $tipo_control,
                'fecha_ultimo_control' => $fecha_prox->format('Y-m-d'),
                'fecha_proximo_control' => $fecha_futuro->format('Y-m-d'),
                'dur_total' => $dur_total,
                'tiempo_transcurrido' => $tiempo_transcurrido
            ];
        }

        $fecha_inicio = clone $fecha_prox;
    }
}

// Función para medicación
function medicacion_simulada($tipo){
    $map = [
        'Control hipertensión' => 'Enalapril 10mg, 1 c/día',
        'Control alergias' => 'Antihistamínico, 1 c/12h',
        'Control isquiotibial' => 'Ibuprofeno 400mg, 1 c/8h',
        'Control diabetes' => 'Metformina 500mg, 1 c/12h'
    ];
    return $map[$tipo] ?? 'Sin medicación registrada';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Controles de Pacientes | SaludProactiva</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body, html { font-family:'Segoe UI',sans-serif; display:flex; flex-direction:column; margin:0; }
.navbar { background-color:#212529; }
.navbar .btn-menu { background-color:#212529; color:#fff; margin-right:0.5rem; }
.navbar .btn-menu:hover { background-color:#6c757d; }
.navbar-text { color:#fff; margin-right:0.5rem; }
.btn-salir { background-color:#dc3545; color:#fff; border:none; }
.btn-salir:hover { background-color:#b02a37; }
.container { margin-top:2rem; max-width:1200px; flex:1; }

.card { margin-bottom:1rem; }
.card-header { font-weight:600; background-color:#0d6efd; color:#fff; }
.card-body { background-color:#fff; }

.btn-recordatorio, .btn-whatsapp, .btn-estudio, .btn-indicaciones {
    border:none; border-radius:8px; padding:6px 12px; font-weight:600; margin-right:5px; cursor:pointer; color:#fff;
}
.btn-recordatorio { background: linear-gradient(135deg,#0dcaf0,#198754);}
.btn-recordatorio:hover { background: linear-gradient(135deg,#198754,#0dcaf0);}
.btn-whatsapp { background:#25D366; }
.btn-whatsapp:hover { background:#128C7E; }
.btn-estudio { background:#0d6efd; }
.btn-estudio:hover { background:#0b5ed7; }
.btn-indicaciones { background:#ffc107; color:#000; }
.btn-indicaciones:hover { background:#e0a800; }

footer { background-color:#212529; color:#fff; text-align:center; padding:12px 0; }
footer a { color:#0d6efd; text-decoration:none; font-weight:bold; }
footer a:hover { text-decoration:underline; }

#imagen-vacia img { max-height:150px; opacity:0.3; filter: grayscale(50%);}
#imagen-vacia p { color:#6c757d; font-style:italic; margin-top:10px; }

.progress { height:20px; margin-bottom:10px; }
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
            <?php
            $controlsForPatient = $controles[$p['id']] ?? [];
            $agrupados = [];
            foreach($controlsForPatient as $c){
                $agrupados[$c['tipo_control']][] = $c;
            }
            foreach($agrupados as $tipo => $controlesTipo):
                $total_duracion = 0;
                $total_transcurrido = 0;
                foreach($controlesTipo as $c){
                    $total_duracion += $c['dur_total'];
                    $total_transcurrido += $c['tiempo_transcurrido'];
                }
                $recuperacion = intval(($total_transcurrido/$total_duracion)*100);
            ?>
            <div style="margin-bottom:20px;">
                <h5><?= htmlspecialchars($tipo) ?></h5>
                <p><strong>Controles realizados:</strong> <?= count($controlesTipo) ?> | <strong>% recuperación global:</strong> <?= $recuperacion ?>%</p>

                <?php foreach($controlesTipo as $index => $c):
                    $fecha_ultimo = new DateTime($c['fecha_ultimo_control']);
                    $fecha_prox = new DateTime($c['fecha_proximo_control']);
                    $es_futuro = $fecha_prox > $hoy;
                ?>
                <p>
                    <strong>Último:</strong> <?= $fecha_ultimo->format('Y-m-d') ?><?php if($fecha_ultimo <= $hoy): ?> | <strong>Asistió:</strong> Sí<?php endif; ?><br>
                    <strong>Próximo:</strong> <?= $fecha_prox->format('Y-m-d') ?>
                    <?php if($es_futuro): ?>
                        <button class="btn-recordatorio" onclick="enviarRecordatorio('<?= htmlspecialchars($p['nombre']) ?>','control','<?= htmlspecialchars($tipo) ?>')">🔔 Recordatorio</button>
                        <button class="btn-estudio" onclick="cargarEstudios('<?= htmlspecialchars($p['nombre']) ?>')">📄 Cargar estudios</button>
                    <?php else: ?>
                        <button class="btn-estudio" onclick="descargarEstudios('<?= htmlspecialchars($p['nombre']) ?>')">📄 Descargar estudios</button>
                    <?php endif; ?>
                </p>
                <?php endforeach; ?>

                <p><strong>Medicación:</strong> <?= medicacion_simulada($tipo) ?></p>
                <button class="btn-whatsapp" onclick="enviarWhatsApp('<?= htmlspecialchars($p['nombre']) ?>','<?= medicacion_simulada($tipo) ?>')">💬 WhatsApp</button>
                <button class="btn-indicaciones" onclick="enviarIndicaciones('<?= htmlspecialchars($p['nombre']) ?>')">📝 Enviar indicaciones</button>

                <p><strong>Avance del tratamiento:</strong></p>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width:<?= $recuperacion ?>%" aria-valuenow="<?= $recuperacion ?>" aria-valuemin="0" aria-valuemax="100"><?= $recuperacion ?>%</div>
                </div>
                <p>Duración total: <?= $total_duracion ?> meses | Tiempo transcurrido: <?= $total_transcurrido ?> meses</p>
            </div>
            <?php endforeach; ?>
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
    imagenVacia.style.display = val === "" ? 'block' : 'none';
    cards.forEach(card => {
        card.style.display = card.dataset.pacienteId === val ? 'block' : 'none';
    });
});

function enviarRecordatorio(nombre, tipo, descripcion){
    const now = new Date().toLocaleString();
    alert(`Se envió un recordatorio a ${nombre} para ${tipo}: ${descripcion}\n(${now})`);
}

function enviarWhatsApp(nombre, medicacion){
    const url = `https://api.whatsapp.com/send?text=Hola ${nombre}, recuerde su medicación: ${medicacion}`;
    window.open(url,'_blank');
}

function enviarIndicaciones(nombre){
    const indicacion = prompt(`Escriba las indicaciones para ${nombre}:`, "Tomar medicación y seguir control médico");
    if(indicacion) alert(`Indicaciones enviadas a ${nombre}: ${indicacion}`);
}

function descargarEstudios(nombre){
    alert(`Descargando estudios de ${nombre}... (simulado)`);
}

function cargarEstudios(nombre){
    alert(`Cargar estudios para ${nombre}... (simulado)`);
}
</script>
</body>
</html>
