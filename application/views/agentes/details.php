<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><?php echo html_escape($title); ?></h3>
        <a href="<?php echo site_url('agentes'); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver a la Lista</a>
    </div>
    <div class="card-body">
        <?php if ($agente->deleted_at): ?>
            <div class="alert alert-warning" role="alert">
                <strong>Atención:</strong> Este agente fue eliminado el <?php echo date('d/m/Y h:i A', strtotime($agente->deleted_at)); ?>.
                <?php if ($this->ion_auth->is_admin() || $this->ion_auth->in_group('leadership')): ?>
                    <a href="<?php echo site_url('agentes/restore/' . $agente->id); ?>" class="btn btn-success btn-sm float-end" onclick="return confirm('¿Está seguro de que desea restaurar este agente?');">
                        <i class="fas fa-undo"></i> Restaurar Agente
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3 text-center">
                <?php
                $foto_url = base_url('uploads/default_avatar.png');
                if (!empty($agente->foto_perfil) && file_exists($agente->foto_perfil)) {
                    $foto_url = base_url($agente->foto_perfil);
                }
                ?>
                <img src="<?php echo $foto_url; ?>" class="img-thumbnail rounded-circle mb-3" alt="Foto de Perfil" style="width: 150px; height: 150px; object-fit: cover;">
                <h4><?php echo html_escape($agente->nombres . ' ' . $agente->apellidos); ?></h4>
                <p class="text-muted"><?php echo html_escape($agente->cargo_nombre); ?></p>
            </div>
            <div class="col-md-9">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Cédula:</strong> <?php echo html_escape($agente->cedula); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>RIF:</strong> <?php echo html_escape($agente->rif); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Correo Electrónico:</strong> <a href="mailto:<?php echo html_escape($agente->correo_electronico); ?>"><?php echo html_escape($agente->correo_electronico); ?></a>
                    </li>
                    <li class="list-group-item">
                        <strong>Teléfono Celular:</strong> <?php echo html_escape($agente->telefono_celular); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Teléfono Local:</strong> <?php echo html_escape($agente->telefono_local ?: 'N/A'); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Sexo:</strong> <?php echo html_escape($agente->sexo); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Fecha de Ingreso:</strong> <?php echo date('d/m/Y', strtotime($agente->fecha_ingreso)); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Dirección:</strong>
                        <p class="mb-0 mt-1">
                            <?php echo nl2br(html_escape($agente->direccion_habitacion)); ?><br>
                            <?php echo html_escape($agente->parroquia_nombre); ?>,
                            <?php echo html_escape($agente->municipio_nombre); ?>,
                            <?php echo html_escape($agente->ciudad_nombre); ?>,
                            Estado <?php echo html_escape($agente->estado_nombre); ?>.
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
