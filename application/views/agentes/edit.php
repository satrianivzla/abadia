<h1 class="mb-4 text-center"><?php echo html_escape($title); ?></h1>

<?php echo form_open_multipart('agentes/update/' . $agente->id, ['id' => 'formEditarAgente']); ?>

<div class="row justify-content-center mb-4">
    <div class="col-md-3 text-center">
        <?php
        $foto_url = base_url('uploads/default_avatar.png'); // Default
        if (!empty($agente->foto_perfil)) {
            // Check if it's an absolute URL or a relative path
            if (filter_var($agente->foto_perfil, FILTER_VALIDATE_URL)) {
                $foto_url = $agente->foto_perfil;
            } else {
                // Assuming it's a relative path from CI root like './uploads/agentes_fotos/file.jpg'
                $relative_path = str_starts_with($agente->foto_perfil, './') ? substr($agente->foto_perfil, 2) : $agente->foto_perfil;
                if (file_exists(FCPATH . $relative_path)) {
                     $foto_url = base_url($relative_path);
                }
            }
        }
        ?>
        <img id="foto_perfil_preview" class="profile-image-preview" src="<?php echo $foto_url; ?>" alt="Foto de Perfil" title="Subir nueva foto de perfil">
        <div class="mt-2">
            <label for="foto_perfil" class="form-label btn btn-sm btn-outline-primary">
                <i class="fas fa-upload"></i> Cambiar Imagen
            </label>
            <input type="file" name="foto_perfil" id="foto_perfil" class="form-control d-none" onchange="previewImage(event)">
            <small class="d-block text-muted">Max 2MB. JPG, PNG, GIF. Dejar en blanco para no cambiar.</small>
        </div>
    </div>
</div>


<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
        <input type="text" name="nombres" id="nombres" value="<?php echo set_value('nombres', $agente->nombres); ?>" class="form-control" placeholder="Ej: Juan Carlos" required>
        <small class="form-text text-muted">Ingrese los nombres completos del agente.</small>
    </div>
    <div class="col-md-6">
        <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
        <input type="text" name="apellidos" id="apellidos" value="<?php echo set_value('apellidos', $agente->apellidos); ?>" class="form-control" placeholder="Ej: Pérez Rodríguez" required>
        <small class="form-text text-muted">Ingrese los apellidos completos del agente.</small>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
        <input type="text" name="cedula" id="cedula" value="<?php echo set_value('cedula', $agente->cedula); ?>" class="form-control" placeholder="Ej: V12345678 o E12345678" maxlength="12" required>
        <small class="form-text text-muted">Formato: V o E seguido del número. Ej: V12345678</small>
    </div>
    <div class="col-md-6">
        <label for="rif" class="form-label">RIF <span class="text-danger">*</span></label>
        <input type="text" name="rif" id="rif" value="<?php echo set_value('rif', $agente->rif); ?>" class="form-control" placeholder="Ej: J123456789 o V123456789" required>
        <small class="form-text text-muted">Registro de Información Fiscal.</small>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="sexo" class="form-label">Sexo <span class="text-danger">*</span></label>
        <select name="sexo" id="sexo" class="form-select" required>
            <option value="">Seleccione...</option>
            <option value="Masculino" <?php echo set_select('sexo', 'Masculino', ($agente->sexo == 'Masculino')); ?>>Masculino</option>
            <option value="Femenino" <?php echo set_select('sexo', 'Femenino', ($agente->sexo == 'Femenino')); ?>>Femenino</option>
        </select>
        <small class="form-text text-muted">Seleccione el sexo del agente.</small>
    </div>
    <div class="col-md-6">
        <label for="correo_electronico" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
        <input type="email" name="correo_electronico" id="correo_electronico" value="<?php echo set_value('correo_electronico', $agente->correo_electronico); ?>" class="form-control" placeholder="ejemplo@dominio.com" required>
        <small class="form-text text-muted">Correo electrónico principal.</small>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="telefono_celular" class="form-label">Teléfono Celular <span class="text-danger">*</span></label>
        <input type="tel" name="telefono_celular" id="telefono_celular" value="<?php echo set_value('telefono_celular', $agente->telefono_celular); ?>" class="form-control" placeholder="Ej: 0412-1234567" required>
        <small class="form-text text-muted">Número de teléfono móvil.</small>
    </div>
    <div class="col-md-6">
        <label for="telefono_local" class="form-label">Teléfono Local</label>
        <input type="tel" name="telefono_local" id="telefono_local" value="<?php echo set_value('telefono_local', $agente->telefono_local); ?>" class="form-control" placeholder="Ej: 0212-1234567">
        <small class="form-text text-muted">Número de teléfono fijo (opcional).</small>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12">
        <label for="direccion_habitacion" class="form-label">Dirección de Habitación <span class="text-danger">*</span></label>
        <textarea name="direccion_habitacion" id="direccion_habitacion" rows="3" class="form-control" placeholder="Ingrese la dirección completa" required><?php echo set_value('direccion_habitacion', $agente->direccion_habitacion); ?></textarea>
        <small class="form-text text-muted">Dirección residencial completa.</small>
    </div>
</div>

<fieldset class="border p-3 mb-3">
    <legend class="w-auto px-2 h6">Ubicación Geográfica <span class="text-danger">*</span></legend>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="id_estado" class="form-label">Estado:</label>
            <select id="id_estado" name="id_estado" class="form-select" required>
                <option value="">Seleccione un Estado</option>
                <?php foreach ($estados as $estado_item): ?>
                    <option value="<?php echo $estado_item->id_estado; ?>" <?php echo set_select('id_estado', $estado_item->id_estado, ($agente->id_estado == $estado_item->id_estado)); ?>><?php echo html_escape($estado_item->estado); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="id_ciudad" class="form-label">Ciudad:</label>
            <select id="id_ciudad" name="id_ciudad" class="form-select" required>
                <option value="">Seleccione una Ciudad</option>
                 <?php foreach ($ciudades as $ciudad_item): ?>
                    <option value="<?php echo $ciudad_item->id_ciudad; ?>" <?php echo set_select('id_ciudad', $ciudad_item->id_ciudad, ($agente->id_ciudad == $ciudad_item->id_ciudad)); ?>><?php echo html_escape($ciudad_item->ciudad); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="id_municipio" class="form-label">Municipio:</label>
            <select id="id_municipio" name="id_municipio" class="form-select" required>
                <option value="">Seleccione un Municipio</option>
                <?php foreach ($municipios as $municipio_item): ?>
                    <option value="<?php echo $municipio_item->id_municipio; ?>" <?php echo set_select('id_municipio', $municipio_item->id_municipio, ($agente->id_municipio == $municipio_item->id_municipio)); ?>><?php echo html_escape($municipio_item->municipio); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="id_parroquia" class="form-label">Parroquia:</label>
            <select id="id_parroquia" name="id_parroquia" class="form-select" required>
                <option value="">Seleccione una Parroquia</option>
                 <?php foreach ($parroquias as $parroquia_item): ?>
                    <option value="<?php echo $parroquia_item->id_parroquia; ?>" <?php echo set_select('id_parroquia', $parroquia_item->id_parroquia, ($agente->id_parroquia == $parroquia_item->id_parroquia)); ?>><?php echo html_escape($parroquia_item->parroquia); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</fieldset>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso <span class="text-danger">*</span></label>
        <input type="date" name="fecha_ingreso" id="fecha_ingreso" value="<?php echo set_value('fecha_ingreso', $agente->fecha_ingreso); ?>" class="form-control" required>
        <small class="form-text text-muted">Fecha en que el agente ingresó.</small>
    </div>
    <div class="col-md-6">
        <label for="id_cargo" class="form-label">Cargo <span class="text-danger">*</span></label>
        <select name="id_cargo" id="id_cargo" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach ($cargos as $cargo_item): ?>
                <option value="<?php echo $cargo_item->id_cargo; ?>" <?php echo set_select('id_cargo', $cargo_item->id_cargo, ($agente->id_cargo == $cargo_item->id_cargo)); ?>><?php echo html_escape($cargo_item->cargo); ?></option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Cargo que desempeñará el agente.</small>
    </div>
</div>

<div class="text-end mt-4">
    <a href="<?php echo site_url('agentes'); ?>" class="btn btn-secondary me-2"><i class="fas fa-times"></i> Cancelar</a>
    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Actualizar Agente</button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
// Image preview function
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('foto_perfil_preview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

$(document).ready(function() {
    var base_url = "<?php echo site_url(); ?>/"; // Use CI's site_url for base

    function resetDropdown(dropdownId, defaultOptionText, disabled = true) {
        $(dropdownId).empty().append('<option value="">' + defaultOptionText + '</option>');
        if (disabled) {
            $(dropdownId).prop('disabled', true);
        } else {
            $(dropdownId).prop('disabled', false);
        }
    }

    // Function to populate a dropdown and select a value
    function populateAndSelect(dropdownId, data, selectedValue, valueField, textField, defaultOption) {
        var $dropdown = $(dropdownId);
        $dropdown.empty().append('<option value="">' + defaultOption + '</option>');
        if (data && data.length > 0) {
            $.each(data, function(key, item) {
                $dropdown.append($('<option>', {
                    value: item[valueField],
                    text: item[textField]
                }));
            });
            $dropdown.val(selectedValue); // Set the selected value
            $dropdown.prop('disabled', false);
        } else {
            $dropdown.prop('disabled', true);
        }
    }


    $('#id_estado').change(function() {
        var estado_id = $(this).val();
        resetDropdown('#id_ciudad', 'Seleccione una Ciudad');
        resetDropdown('#id_municipio', 'Seleccione un Municipio');
        resetDropdown('#id_parroquia', 'Seleccione una Parroquia');

        if (estado_id) {
            // Fetch Ciudades
            $.ajax({
                url: base_url + 'agentes/get_ciudades',
                method: 'POST',
                data: {estado_id: estado_id},
                dataType: 'json',
                success: function(data) {
                    populateAndSelect('#id_ciudad', data, '<?php echo set_value('id_ciudad', $agente->id_ciudad); ?>', 'id_ciudad', 'ciudad', 'Seleccione una Ciudad');
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching ciudades: " + error);
                }
            });

            // Fetch Municipios for the selected state
            $.ajax({
                url: base_url + 'agentes/get_municipios',
                method: 'POST',
                data: {estado_id: estado_id},
                dataType: 'json',
                success: function(data) {
                     populateAndSelect('#id_municipio', data, '<?php echo set_value('id_municipio', $agente->id_municipio); ?>', 'id_municipio', 'municipio', 'Seleccione un Municipio');
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching municipios: " + error);
                }
            });
        }
    });

    // Trigger change on id_estado if it has a value on page load to populate dependent dropdowns
    // if ($('#id_estado').val()) {
    //    $('#id_estado').trigger('change'); // This will re-fetch and might override pre-populated values if not handled carefully.
                                          // The controller already sends pre-populated lists for edit.
    // }
    // Instead of triggering change, ensure dropdowns are enabled if they have selected values
    if ($('#id_ciudad').val()) $('#id_ciudad').prop('disabled', false);
    if ($('#id_municipio').val()) $('#id_municipio').prop('disabled', false);
    if ($('#id_parroquia').val()) $('#id_parroquia').prop('disabled', false);


    $('#id_municipio').change(function() {
        var municipio_id = $(this).val();
        resetDropdown('#id_parroquia', 'Seleccione una Parroquia');

        if (municipio_id) {
            $.ajax({
                url: base_url + 'agentes/get_parroquias',
                method: 'POST',
                data: {municipio_id: municipio_id},
                dataType: 'json',
                success: function(data) {
                    populateAndSelect('#id_parroquia', data, '<?php echo set_value('id_parroquia', $agente->id_parroquia); ?>', 'id_parroquia', 'parroquia', 'Seleccione una Parroquia');
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching parroquias: " + error);
                }
            });
        }
    });

    // if ($('#id_municipio').val()) { // If municipio is pre-selected
    //     $('#id_municipio').trigger('change'); // This might override parroquia if not careful
    // }


    // jQuery Validation (Example)
    $('#formEditarAgente').submit(function(event) {
        var isValid = true;
        var errors = [];

        var cedula = $('#cedula').val();
        if (!/^[VEve]\d{6,9}$/.test(cedula)) {
            isValid = false;
            errors.push("Cédula inválida. Formato: V1234567 o E1234567.");
            $('#cedula').addClass('is-invalid');
        } else {
            $('#cedula').removeClass('is-invalid');
        }

        var rif = $('#rif').val();
        if (!/^[JVGEjvge]\d{7,9}(-\d{1})?$/.test(rif)) {
            isValid = false;
            errors.push("RIF inválido. Formato: J123456789 o V12345678-9.");
            $('#rif').addClass('is-invalid');
        } else {
            $('#rif').removeClass('is-invalid');
        }

        var email = $('#correo_electronico').val();
        var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailRegex.test(email)) {
            isValid = false;
            errors.push("Correo electrónico inválido.");
            $('#correo_electronico').addClass('is-invalid');
        } else {
            $('#correo_electronico').removeClass('is-invalid');
        }

        $('#formEditarAgente [required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                 var label = $("label[for='" + $(this).attr('id') + "']").text().replace('*','').trim();
                errors.push("El campo '" + label + "' es obligatorio.");
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            event.preventDefault();
            var errorHtml = '<ul>';
            $.each(errors, function(index, error) {
                errorHtml += '<li>' + error + '</li>';
            });
            errorHtml += '</ul>';
            $('#validation-summary').remove();
            $('#formEditarAgente').prepend('<div id="validation-summary" class="alert alert-danger" role="alert">' + errorHtml + '</div>');
            $('html, body').animate({ scrollTop: $('#validation-summary').offset().top -70 }, 'slow');
        } else {
            $('#validation-summary').remove();
        }
    });
});
</script>
