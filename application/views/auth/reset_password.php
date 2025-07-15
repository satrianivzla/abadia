<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo lang('reset_password_heading');?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- AdminLTE Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <style>
    body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .login-box { width: 380px; }
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
      <p class="login-box-msg"><?php echo lang('reset_password_heading');?></p>

      <?php if (isset($message) && !empty($message)): ?>
          <div id="infoMessage" class="alert alert-danger text-center" role="alert">
              <?php echo $message;?>
          </div>
      <?php endif; ?>

      <?php echo form_open('auth/reset_password/' . $code);?>
        <div class="input-group mb-3">
          <?php echo form_input($new_password);?>
          <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <?php echo form_input($new_password_confirm);?>
          <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <?php echo form_input($user_id);?>
        <?php echo form_hidden($csrf); ?>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block"><?php echo lang('reset_password_submit_btn'); ?></button>
          </div>
        </div>
      <?php echo form_close();?>

      <p class="mt-3 mb-1">
        <a href="<?php echo site_url('auth/login'); ?>">Volver al Login</a>
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

</body>
</html>
