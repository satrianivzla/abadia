<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header text-center">
                <h3><?php echo lang('reset_password_heading');?></h3>
            </div>
            <div class="card-body">
                <?php if (isset($message) && !empty($message)): ?>
                    <div id="infoMessage" class="alert alert-danger" role="alert">
                        <?php echo $message;?>
                    </div>
                <?php endif; ?>

                <?php echo form_open('auth/reset_password/' . $code);?>

                    <div class="mb-3">
                        <label for="new_password" class="form-label"><?php echo sprintf(lang('reset_password_new_password_label'), $min_password_length);?></label>
                        <?php echo form_input($new_password);?>
                    </div>

                    <div class="mb-3">
                        <?php echo lang('reset_password_new_password_confirm_label', 'new_password_confirm', ['class' => 'form-label']);?>
                        <?php echo form_input($new_password_confirm);?>
                    </div>

                    <?php echo form_input($user_id);?>
                    <?php echo form_hidden($csrf); ?>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><?php echo lang('reset_password_submit_btn'); ?></button>
                    </div>

                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>
