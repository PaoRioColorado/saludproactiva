<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Búsqueda rápida de códigos CIE</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { padding: 20px; }
    .code-btn { margin: 2px 0; display: block; text-align: left; width: 100%; }
    #selectedCode { margin-top: 20px; font-weight: bold; }
    .subcat-title { font-weight: bold; margin-top: 10px; }
</style>
</head>
<body>
    <!-- Botón para volver al menú principal -->
<a href="dashboard.php" class="btn btn-secondary mb-3">← Volver al menú principal</a>
<h2>Búsqueda rápida de códigos</h2>
<input type="text" id="searchInput" class="form-control mb-3" placeholder="Buscar por título o código...">

<div class="accordion" id="cieAccordion">

  <!-- Enfermedades infecciosas intestinales -->
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

  <!-- Tuberculosis y otras enfermedades respiratorias -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading2">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
        Tuberculosis y otras enfermedades respiratorias
      </button>
    </h2>
    <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
      <div class="accordion-body">
        <button class="btn btn-outline-primary code-btn" data-code="A15">A15 - Tuberculosis respiratoria confirmada</button>
        <button class="btn btn-outline-primary code-btn" data-code="A16">A16 - Tuberculosis respiratoria no confirmada</button>
      </div>
    </div>
  </div>

  <!-- Enfermedades de la piel y sistema nervioso -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading3">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
        Enfermedades de la piel y sistema nervioso
      </button>
    </h2>
    <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
      <div class="accordion-body">
        <button class="btn btn-outline-primary code-btn" data-code="A30">A30 - Lepra [enfermedad de Hansen]</button>
        <button class="btn btn-outline-primary code-btn" data-code="G60">G60 - Neuropatía hereditaria e idiopática</button>
      </div>
    </div>
  </div>

  <!-- Otras enfermedades bacterianas -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading4">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
        Otras enfermedades bacterianas
      </button>
    </h2>
    <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
      <div class="accordion-body">
        <button class="btn btn-outline-primary code-btn" data-code="A30">A30 - Lepra [enfermedad de Hansen]</button>
        <button class="btn btn-outline-primary code-btn" data-code="A31">A31 - Infecciones debidas a otras micobacterias</button>
        <button class="btn btn-outline-primary code-btn" data-code="A32">A32 - Listeriosis</button>
        <button class="btn btn-outline-primary code-btn" data-code="A33">A33 - Tétanos neonatal</button>
        <button class="btn btn-outline-primary code-btn" data-code="A34">A34 - Tétanos obstétrico</button>
        <button class="btn btn-outline-primary code-btn" data-code="A35">A35 - Otros tétanos</button>
        <button class="btn btn-outline-primary code-btn" data-code="A36">A36 - Difteria</button>
        <button class="btn btn-outline-primary code-btn" data-code="A37">A37 - Tos ferina [tos convulsiva]</button>
        <button class="btn btn-outline-primary code-btn" data-code="A38">A38 - Escarlatina</button>
        <button class="btn btn-outline-primary code-btn" data-code="A39">A39 - Infección meningococica</button>
        <button class="btn btn-outline-primary code-btn" data-code="A40">A40 - Septicemia estreptococica</button>
        <button class="btn btn-outline-primary code-btn" data-code="A41">A41 - Otras septicemias</button>
        <button class="btn btn-outline-primary code-btn" data-code="A42">A42 - Actinomicosis</button>
        <button class="btn btn-outline-primary code-btn" data-code="A43">A43 - Nocardiosis</button>
        <button class="btn btn-outline-primary code-btn" data-code="A44">A44 - Bartonelosis</button>
        <button class="btn btn-outline-primary code-btn" data-code="A46">A46 - Erisipela</button>
      </div>
    </div>
  </div>

  <!-- Enfermedades de la sangre y órganos hematopoyéticos -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading5">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
        Enfermedades de la sangre y órganos hematopoyéticos
      </button>
    </h2>
    <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
      <div class="accordion-body">
        <div class="subcat-title">Anemias nutricionales</div>
        <button class="btn btn-outline-primary code-btn" data-code="D50">D50 - Anemias por deficiencia de hierro</button>
        <button class="btn btn-outline-primary code-btn" data-code="D51">D51 - Anemia por deficiencia de vitamina B12</button>
        <button class="btn btn-outline-primary code-btn" data-code="D52">D52 - Anemia por deficiencia de folatos</button>
        <button class="btn btn-outline-primary code-btn" data-code="D53">D53 - Otras anemias nutricionales</button>

        <div class="subcat-title">Anemias hemolíticas</div>
        <button class="btn btn-outline-primary code-btn" data-code="D55">D55 - Anemia debida a trastornos enzimáticos</button>
        <button class="btn btn-outline-primary code-btn" data-code="D56">D56 - Talasemia</button>
        <button class="btn btn-outline-primary code-btn" data-code="D57">D57 - Trastornos falciformes</button>
        <button class="btn btn-outline-primary code-btn" data-code="D58">D58 - Otras anemias hemolíticas hereditarias</button>
        <button class="btn btn-outline-primary code-btn" data-code="D59">D59 - Anemia hemolítica adquirida</button>

        <div class="subcat-title">Anemias aplásticas y otras anemias</div>
        <button class="btn btn-outline-primary code-btn" data-code="D60">D60 - Aplasia adquirida, exclusiva de la serie roja [eritroblastopenia]</button>
        <button class="btn btn-outline-primary code-btn" data-code="D61">D61 - Otras anemias aplásticas</button>
        <button class="btn btn-outline-primary code-btn" data-code="D62">D62 - Anemia posthemorrágica aguda</button>
        <button class="btn btn-outline-primary code-btn" data-code="D63">D63 - Anemia en enfermedades crónicas clasificadas en otra parte</button>
        <button class="btn btn-outline-primary code-btn" data-code="D64">D64 - Otras anemias</button>

        <div class="subcat-title">Defectos de la coagulación, púrpura y otras afecciones hemorrágicas</div>
        <button class="btn btn-outline-primary code-btn" data-code="D65">D65 - Coagulación intravascular diseminada [síndrome de desfibrinación]</button>
        <button class="btn btn-outline-primary code-btn" data-code="D66">D66 - Deficiencia hereditaria del factor VIII</button>
        <button class="btn btn-outline-primary code-btn" data-code="D67">D67 - Deficiencia hereditaria del factor IX</button>
        <button class="btn btn-outline-primary code-btn" data-code="D68">D68 - Otros defectos de la coagulación</button>
        <button class="btn btn-outline-primary code-btn" data-code="D69">D69 - Púrpura y otras afecciones hemorrágicas</button>
      </div>
    </div>
  </div>

  <!-- Enfermedades endocrinas, nutricionales y metabólicas -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading6">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6">
        Enfermedades endocrinas, nutricionales y metabólicas
      </button>
    </h2>
    <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#cieAccordion">
      <div class="accordion-body">
        <div class="subcat-title">Trastornos de la glándula tiroides</div>
        <button class="btn btn-outline-primary code-btn" data-code="E00">E00 - Síndrome congénito de deficiencia de yodo</button>
        <button class="btn btn-outline-primary code-btn" data-code="E01">E01 - Trastornos tiroideos vinculados a deficiencia de yodo y afecciones</button>
        <button class="btn btn-outline-primary code-btn" data-code="E02">E02 - Hipotiroidismo subclínico por deficiencia de yodo</button>
        <button class="btn btn-outline-primary code-btn" data-code="E03">E03 - Otro hipotiroidismo</button>
        <button class="btn btn-outline-primary code-btn" data-code="E04">E04 - Otro bocio no tóxico</button>
        <button class="btn btn-outline-primary code-btn" data-code="E05">E05 - Tirotoxicosis [hipertiroidismo]</button>
        <button class="btn btn-outline-primary code-btn" data-code="E06">E06 - Tiroiditis</button>
        <button class="btn btn-outline-primary code-btn" data-code="E07">E07 - Otros trastornos tiroideos</button>
      </div>
    </div>
  </div>

</div>

<div id="selectedCode">Código seleccionado: <span id="selectedCodeText">Ninguno</span></div>

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
        // Cerrar todos los acordeones
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
