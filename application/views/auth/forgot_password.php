<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header text-center">
                <h3><?php echo lang('forgot_password_heading');?></h3>
                <p class="text-muted"><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>
            </div>
            <div class="card-body">
                <?php if (isset($message) && !empty($message)): ?>
                    <div id="infoMessage" class="alert alert-danger" role="alert">
                        <?php echo $message;?>
                    </div>
                <?php endif; ?>

                <?php echo form_open("auth/forgot_password");?>

                    <div class="mb-3">
                        <label for="identity" class="form-label"><?php echo (($type == 'email') ? sprintf(lang('forgot_password_email_label'), $identity_label) : sprintf(lang('forgot_password_identity_label'), $identity_label));?></label>
                        <?php echo form_input($identity);?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><?php echo lang('forgot_password_submit_btn'); ?></button>
                    </div>

                <?php echo form_close();?>
            </div>
             <div class="card-footer text-center">
                <a href="<?php echo site_url('auth/login'); ?>">Volver al Login</a>
            </div>
        </div>
    </div>
</div>
