<div class="row">
    <div class="col-lg-4 col-md-6">
        <!-- Total Agentes -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo isset($total_agentes) ? $total_agentes : '0'; ?></h3>
                <p>Agentes Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="<?php echo site_url('agentes'); ?>" class="small-box-footer">Ver Lista <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-md-6">
        <!-- Total Afiliaciones -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo isset($total_afiliaciones) ? $total_afiliaciones : '0'; ?></h3>
                <p>Afiliaciones Totales</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-contract"></i>
            </div>
            <a href="#" class="small-box-footer">Ver Afiliaciones <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-md-6">
        <!-- Total Users -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo isset($total_users) ? $total_users : '0'; ?></h3>
                <p>Usuarios del Sistema</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <a href="<?php echo site_url('auth'); ?>" class="small-box-footer">Gestionar Usuarios <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Acciones Rápidas</h3>
            </div>
            <div class="card-body">
                <p>Utilice estos accesos directos para empezar a configurar el sistema.</p>
                <a href="<?php echo site_url('agentes/create'); ?>" class="btn btn-primary"><i class="fas fa-user-tie"></i> Crear Nuevo Agente</a>
                <a href="<?php echo site_url('afiliaciones/create'); ?>" class="btn btn-success"><i class="fas fa-file-alt"></i> Crear Nueva Afiliación</a>
                <?php if ($this->ion_auth->is_admin()): ?>
                <a href="<?php echo site_url('auth/create_user'); ?>" class="btn btn-info"><i class="fas fa-user-plus"></i> Crear Nuevo Usuario</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
