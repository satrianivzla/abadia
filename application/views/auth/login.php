<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header text-center">
                <h3><?php echo lang('login_heading');?></h3>
                <p class="text-muted"><?php echo lang('login_subheading');?></p>
            </div>
            <div class="card-body">
                <?php if (isset($message) && !empty($message)): ?>
                    <div id="infoMessage" class="alert alert-danger" role="alert">
                        <?php echo $message;?>
                    </div>
                <?php endif; ?>

                <?php echo form_open("auth/login");?>

                    <div class="mb-3">
                        <?php echo lang('login_identity_label', 'identity', ['class' => 'form-label']);?>
                        <?php echo form_input($identity);?>
                    </div>

                    <div class="mb-3">
                        <?php echo lang('login_password_label', 'password', ['class' => 'form-label']);?>
                        <?php echo form_input($password);?>
                    </div>

                    <div class="mb-3 form-check">
                        <?php echo form_checkbox('remember', '1', FALSE, 'id="remember" class="form-check-input"');?>
                        <?php echo lang('login_remember_label', 'remember', ['class' => 'form-check-label']);?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><?php echo lang('login_submit_btn'); ?></button>
                    </div>

                <?php echo form_close();?>
            </div>
            <div class="card-footer text-center">
                <a href="forgot_password"><?php echo lang('login_forgot_password');?></a>
            </div>
        </div>
    </div>
</div>
