<?php if (!$agente): ?>
    <div class="alert alert-danger">
        <h4 class="alert-heading">Error de Perfil</h4>
        <p>Su cuenta de usuario no está vinculada a un perfil de agente. Por favor, contacte a un administrador.</p>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo isset($stats['Pendiente']) ? $stats['Pendiente'] : 0; ?></h3>
                    <p>Afiliaciones Pendientes</p>
                </div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo isset($stats['Activo']) ? $stats['Activo'] : 0; ?></h3>
                    <p>Afiliaciones Activas</p>
                </div>
                <div class="icon"><i class="fas fa-check"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?php echo isset($stats['Rechazado']) ? $stats['Rechazado'] : 0; ?></h3>
                    <p>Afiliaciones Rechazadas</p>
                </div>
                <div class="icon"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>$<?php echo number_format($total_commission, 2); ?></h3>
                    <p>Comisiones (placeholder)</p>
                </div>
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <?php
                        $foto_url = base_url('uploads/default_avatar.png');
                        if (!empty($agente->foto_perfil) && file_exists($agente->foto_perfil)) {
                            $foto_url = base_url($agente->foto_perfil);
                        }
                        ?>
                        <img class="profile-user-img img-fluid img-circle" src="<?php echo $foto_url; ?>" alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center"><?php echo html_escape($agente->nombres . ' ' . $agente->apellidos); ?></h3>
                    <p class="text-muted text-center"><?php echo html_escape($agente->cargo_nombre); ?></p>
                    <a href="<?php echo site_url('agentes/edit/' . $agente->id); ?>" class="btn btn-primary btn-block"><b>Editar Perfil</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mis Afiliaciones Recientes</h3>
                </div>
                <div class="card-body">
                    <p>Aquí se mostrará una tabla con las últimas afiliaciones realizadas por usted.</p>
                    <!-- Placeholder for a table of affiliations -->
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
