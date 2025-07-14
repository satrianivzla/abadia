<div class="container py-4">
  <h4 class="mb-4 text-center"><?php echo html_escape($title); ?></h4>

  <!-- Progress Bar -->
  <div class="progress mb-4" style="height: 25px;">
    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">Paso 1 de 4</div>
  </div>

  <?php echo form_open_multipart('afiliaciones/store', ['id' => 'affiliationForm']); ?>
    <!-- CSRF Token -->
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

    <!-- Step 1: Titular Data -->
    <fieldset class="step active" id="step-1">
      <legend>🧑 Paso 1: Datos del Titular del Servicio</legend>
      <div class="card p-3 shadow-sm mb-3">
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label for="contractNumber" class="form-label">N° de Contrato</label>
            <input type="text" class="form-control" id="contractNumber" name="contractNumber" readonly>
          </div>
          <div class="col-md-4">
            <label for="currentDate" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="currentDate" name="currentDate" readonly>
          </div>
          <div class="col-md-4">
            <label for="advisor" class="form-label">Asesor Responsable</label>
            <select class="form-select" id="advisor" name="advisor" required>
                <option value="">Seleccione un asesor...</option>
                <?php foreach($asesores as $asesor): ?>
                    <option value="<?php echo $asesor->id; ?>"><?php echo html_escape($asesor->nombres . ' ' . $asesor->apellidos); ?></option>
                <?php endforeach; ?>
            </select>
          </div>
        </div>

        <h6 class="mb-2">📝 Identificación del Titular</h6>
        <div class="row g-3 mb-3">
          <div class="col-md-3">
            <label for="firstName" class="form-label">Nombres</label>
            <input type="text" class="form-control" id="firstName" name="titular[nombres]" required>
          </div>
          <div class="col-md-3">
            <label for="lastName" class="form-label">Apellidos</label>
            <input type="text" class="form-control" id="lastName" name="titular[apellidos]" required>
          </div>
          <div class="col-md-3">
            <label for="ci" class="form-label">Cédula</label>
            <input type="text" class="form-control" id="ci" name="titular[cedula]" required>
          </div>
          <div class="col-md-3">
            <label for="birthdate" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="birthdate" name="titular[birthdate]" required>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-3">
            <label for="age" class="form-label">Edad</label>
            <input type="number" class="form-control" id="age" name="titular[age]" readonly>
          </div>
          <div class="col-md-3">
            <label class="form-label">Parentesco</label>
            <input type="text" class="form-control" value="Titular" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label">Foto de Perfil</label>
            <div class="d-flex align-items-center gap-3">
              <img id="photoPreview" src="<?php echo base_url('uploads/default_avatar.png'); ?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
              <input type="file" class="form-control" id="photo" name="titular_photo" accept="image/*">
            </div>
          </div>
        </div>
      </div>
      <button type="button" class="btn btn-primary next-step">Siguiente</button>
    </fieldset>

    <!-- Step 2: Family Group -->
    <fieldset class="step d-none" id="step-2">
      <legend>👨‍👩‍👧‍👦 Paso 2: Grupo Familiar</legend>
      <div class="card p-3 shadow-sm mb-3" id="familyGroup">
        <!-- Titular row (read-only) -->
        <div id="titularRow" class="row g-2 mb-2 align-items-center">
          <div class="col"><input type="text" class="form-control" id="titularLastName" readonly placeholder="Apellidos"></div>
          <div class="col"><input type="text" class="form-control" id="titularFirstName" readonly placeholder="Nombres"></div>
          <div class="col"><input type="text" class="form-control" id="titularCI" readonly placeholder="C.I."></div>
          <div class="col"><input type="date" class="form-control" id="titularBirthdate" readonly></div>
          <div class="col"><input type="number" class="form-control" id="titularAge" readonly placeholder="Edad"></div>
          <div class="col"><input type="text" class="form-control" value="Titular" readonly></div>
          <div class="col-auto"><button type="button" class="btn btn-sm btn-light" disabled>❌</button></div>
        </div>
        <!-- Add Family Member button -->
        <button type="button" class="btn btn-secondary btn-sm add-familiar-btn">➕ Agregar Familiar</button>
      </div>
      <button type="button" class="btn btn-secondary prev-step">Anterior</button>
      <button type="button" class="btn btn-primary next-step">Siguiente</button>
    </fieldset>

    <!-- Step 3: Plan and Payment -->
    <fieldset class="step d-none" id="step-3">
      <legend>💳 Paso 3: Plan y Pago</legend>
      <div class="card p-3 shadow-sm mb-3">
        <div class="mb-3">
          <label for="planType" class="form-label">Tipo de Plan</label>
          <select id="planType" name="planType" class="form-select" required>
            <option value="">Seleccionar</option>
            <option value="esenciaSC">Esencia S/C ($10)</option>
            <option value="esencia">Esencia ($20)</option>
            <option value="cobertura">Cobertura ($30)</option>
            <option value="proteccion">Protección ($40)</option>
          </select>
        </div>
        <p id="planAmountText" class="fw-bold">Monto: —</p>
        <input type="hidden" name="planAmount" id="planAmountInput">
        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <label for="cuotas" class="form-label">Número de Cuotas</label>
            <input type="number" class="form-control" id="cuotas" name="cuotas" required min="1">
          </div>
          <div class="col-md-6">
            <p id="valorCuota" class="fw-bold mt-4 pt-2">Valor por cuota: —</p>
          </div>
        </div>
        <label for="paymentType" class="form-label">Forma de Pago</label>
        <select id="paymentType" name="paymentType" class="form-select" required>
          <option value="">Seleccionar</option>
          <option value="nomina">Nómina</option>
          <option value="domiciliacion">Domiciliación</option>
          <option value="consignacion">Consignación Directa</option>
        </select>
        <div id="datosBancarios" class="mt-3 d-none">
          <input type="text" name="bank_name" class="form-control mb-2" placeholder="Banco">
          <input type="text" name="account_number" class="form-control mb-2" placeholder="N° de Cuenta">
          <select class="form-select" name="account_type">
            <option value="">Tipo de Cuenta</option>
            <option value="ahorro">Ahorro</option>
            <option value="corriente">Corriente</option>
          </select>
        </div>
      </div>
      <button type="button" class="btn btn-secondary prev-step">Anterior</button>
      <button type="button" class="btn btn-primary next-step">Siguiente</button>
    </fieldset>

    <!-- Step 4: Final Confirmation -->
    <fieldset class="step d-none" id="step-4">
      <legend>✅ Paso 4: Confirmación Final</legend>
      <div class="card p-3 shadow-sm mb-3">
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="termsCheck" name="termsCheck" required>
          <label class="form-check-label" for="termsCheck">
            Acepto los <a href="#">términos y condiciones</a> del servicio ofrecido por la empresa.
          </label>
        </div>
        <label for="observaciones" class="form-label">Observaciones adicionales</label>
        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
        <div class="alert alert-info mt-3">
          ✅ El proceso de afiliación está listo para finalizar.
        </div>
      </div>
      <button type="button" class="btn btn-secondary prev-step">Anterior</button>
      <button type="submit" class="btn btn-success">Finalizar Afiliación</button>
    </fieldset>
  <?php echo form_close(); ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  let currentStep = 1;
  const totalSteps = document.querySelectorAll(".step").length;
  let familiarIndex = 0; // To keep track of family member array index

  const planPrices = {
    esenciaSC: 10,
    esencia: 20,
    cobertura: 30,
    proteccion: 40
  };

  function showStep(step) {
    document.querySelectorAll(".step").forEach((el, idx) => {
      el.classList.toggle("d-none", idx + 1 !== step);
      el.classList.toggle("active", idx + 1 === step);
    });
    const progressBar = document.getElementById("progressBar");
    const percentage = Math.round(((step -1) / (totalSteps-1)) * 100);
    progressBar.style.width = `${percentage}%`;
    progressBar.textContent = `Paso ${step} de ${totalSteps}`;
  }

  function validateStep(step) {
    let valid = true;
    const activeStep = document.querySelector(`#step-${step}`);
    // Clear previous invalid states within the current step
    activeStep.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    const inputs = activeStep.querySelectorAll("input[required], select[required], textarea[required]");
    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.classList.add("is-invalid");
        valid = false;
      }
    });
    return valid;
  }

  document.querySelectorAll(".next-step").forEach(btn => {
    btn.addEventListener("click", (e) => {
      if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
          if (currentStep === 1) { // When moving from step 1 to 2
            poblarTitularEnFamilia();
          }
          currentStep++;
          showStep(currentStep);
        }
      }
    });
  });

  document.querySelectorAll(".prev-step").forEach(btn => {
    btn.addEventListener("click", () => {
      if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
      }
    });
  });

  // Calculate age from birthdate
  function calculateAge(birthdateInput, ageInput) {
      if (!birthdateInput.value) {
          ageInput.value = "";
          return;
      }
      const birthDate = new Date(birthdateInput.value);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
      ageInput.value = age >= 0 ? age : "";
  }

  document.getElementById("birthdate")?.addEventListener("change", (e) => {
      calculateAge(e.target, document.getElementById("age"));
  });

  // Photo Preview
  document.getElementById("photo")?.addEventListener("change", e => {
    const [file] = e.target.files;
    document.getElementById("photoPreview").src = file ? URL.createObjectURL(file) : "<?php echo base_url('uploads/default_avatar.png'); ?>";
  });

  // Autogenerate contract and date
  const now = new Date();
  const contractId = `CN-${now.getFullYear()}${now.getMonth()+1}${now.getDate()}-${Math.floor(Math.random()*10000)}`;
  document.getElementById("contractNumber").value = contractId;
  document.getElementById("currentDate").value = now.toISOString().split("T")[0];

  function poblarTitularEnFamilia() {
    document.getElementById("titularFirstName").value = document.getElementById("firstName").value;
    document.getElementById("titularLastName").value = document.getElementById("lastName").value;
    document.getElementById("titularCI").value = document.getElementById("ci").value;
    document.getElementById("titularBirthdate").value = document.getElementById("birthdate").value;
    document.getElementById("titularAge").value = document.getElementById("age").value;
  }

  // Plan and cuota dynamics
  const planSelect = document.getElementById("planType");
  const cuotasInput = document.getElementById("cuotas");
  const montoText = document.getElementById("planAmountText");
  const montoInput = document.getElementById("planAmountInput");
  const cuotaLabel = document.getElementById("valorCuota");

  function updateMontoYCuotas() {
    const plan = planSelect.value;
    const monto = planPrices[plan] || 0;
    const cuotas = parseInt(cuotasInput.value) || 1;
    const valorCuota = cuotas > 0 ? monto / cuotas : monto;
    montoText.textContent = `Monto: $${monto.toFixed(2)}`;
    montoInput.value = monto.toFixed(2);
    cuotaLabel.textContent = `Valor por cuota: $${valorCuota.toFixed(2)}`;
  }

  planSelect?.addEventListener("change", updateMontoYCuotas);
  cuotasInput?.addEventListener("input", updateMontoYCuotas);

  // Show bank fields
  document.getElementById("paymentType")?.addEventListener("change", (e) => {
    document.getElementById("datosBancarios").classList.toggle("d-none", e.target.value !== "domiciliacion");
  });

  // Add/Remove family members
  const familyGroup = document.getElementById("familyGroup");
  familyGroup.addEventListener("click", e => {
    if (e.target.classList.contains("add-familiar-btn")) {
        const newRow = document.createElement("div");
        newRow.className = "row g-2 mb-2 align-items-center familiar-row";
        newRow.innerHTML = `
          <div class="col"><input type="text" name="familiares[${familiarIndex}][apellidos]" class="form-control" placeholder="Apellidos" required></div>
          <div class="col"><input type="text" name="familiares[${familiarIndex}][nombres]" class="form-control" placeholder="Nombres" required></div>
          <div class="col"><input type="text" name="familiares[${familiarIndex}][cedula]" class="form-control" placeholder="C.I." required></div>
          <div class="col"><input type="date" name="familiares[${familiarIndex}][birthdate]" class="form-control fecha-nac" required></div>
          <div class="col"><input type="number" class="form-control edad-calc" placeholder="Edad" readonly></div>
          <div class="col">
            <select class="form-select" name="familiares[${familiarIndex}][parentesco]" required>
              <option value="">Parentesco</option>
              <option value="conyuge">Cónyuge</option>
              <option value="hijo">Hijo/a</option>
              <option value="padre">Padre</option>
              <option value="madre">Madre</option>
            </select>
          </div>
          <div class="col-auto"><button type="button" class="btn btn-sm btn-danger remove-familiar-btn">❌</button></div>
        `;
        familyGroup.insertBefore(newRow, e.target);
        familiarIndex++; // Increment index for next familiar
    }
    if (e.target.classList.contains("remove-familiar-btn")) {
        e.target.closest(".familiar-row").remove();
    }
  });

  familyGroup.addEventListener("change", e => {
    if (e.target.classList.contains("fecha-nac")) {
      const edadInput = e.target.closest(".row").querySelector(".edad-calc");
      if (edadInput) calculateAge(e.target, edadInput);
    }
  });

  // Initial setup call
  showStep(currentStep);
});
</script>
