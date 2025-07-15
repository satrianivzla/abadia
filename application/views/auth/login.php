<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo lang('login_heading');?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- AdminLTE Theme style (for login box look and feel) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <style>
    body {
      background-color: #f4f6f9;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .login-box {
      width: 380px;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo site_url('/'); ?>"><b>Abadia</b>App</a>
  </div>
  <!-- /.login-logo -->
  <div class="card shadow-sm">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><?php echo lang('login_subheading');?></p>

      <?php if (isset($message) && !empty($message)): ?>
          <div id="infoMessage" class="alert alert-danger text-center" role="alert">
              <?php echo $message;?>
          </div>
      <?php endif; ?>

      <?php echo form_open("auth/login");?>
        <div class="input-group mb-3">
          <?php echo form_input($identity);?>
          <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <?php echo form_input($password);?>
           <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="form-check">
              <?php echo form_checkbox('remember', '1', FALSE, 'id="remember" class="form-check-input"');?>
              <label class="form-check-label" for="remember">
                <?php echo lang('login_remember_label'); ?>
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block"><?php echo lang('login_submit_btn'); ?></button>
          </div>
          <!-- /.col -->
        </div>
      <?php echo form_close();?>

      <p class="mb-1 mt-3">
        <a href="<?php echo site_url('auth/forgot_password'); ?>"><?php echo lang('login_forgot_password');?></a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE JS is NOT loaded here to avoid Bootstrap 4 conflicts -->

</body>
</html>
