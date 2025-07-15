<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?php echo site_url('/'); ?>" class="brand-link">
    <!-- <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
    <span class="brand-text font-weight-light">Abadia App</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <!-- We can place user image here later -->
        <img src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo html_escape($this->ion_auth->user()->row()->first_name ?? 'Usuario'); ?></a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
          <a href="<?php echo site_url('dashboard'); ?>" class="nav-link <?php echo ($this->router->fetch_class() == 'dashboard') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <?php if ($this->ion_auth->is_admin() || $this->ion_auth->in_group('leadership')): ?>
        <li class="nav-header">ADMINISTRACIÓN</li>
        <li class="nav-item">
          <a href="<?php echo site_url('agentes'); ?>" class="nav-link <?php echo ($this->router->fetch_class() == 'agentes') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Agentes</p>
          </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo site_url('afiliaciones/list'); ?>" class="nav-link <?php echo ($this->router->fetch_class() == 'afiliaciones' && $this->router->fetch_method() == 'list') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-file-contract"></i>
                <p>Ver Afiliaciones</p>
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-header">FORMULARIOS</li>
        <li class="nav-item">
          <a href="<?php echo site_url('afiliaciones/create'); ?>" class="nav-link <?php echo ($this->router->fetch_class() == 'afiliaciones' && $this->router->fetch_method() == 'create') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-edit"></i>
            <p>Nueva Afiliación</p>
          </a>
        </li>

        <li class="nav-header">CUENTA</li>
        <li class="nav-item">
          <a href="<?php echo site_url('auth/change_password'); ?>" class="nav-link">
            <i class="nav-icon fas fa-key"></i>
            <p>Cambiar Contraseña</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo site_url('auth/logout'); ?>" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Cerrar Sesión</p>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
