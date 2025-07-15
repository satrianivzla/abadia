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
      <div class="icheck-primary">
        <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
        <label for="remember">
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

<p class="mb-1">
  <a href="forgot_password"><?php echo lang('login_forgot_password');?></a>
</p>
