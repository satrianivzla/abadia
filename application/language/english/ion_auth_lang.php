<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - English
*
* Author: Ben Edmunds
*         ben.edmunds@gmail.com
*         @benedmunds
*
* Location: https://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  03.14.2010
*
* Description:  English language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']            = 'Account Successfully Created';
$lang['account_creation_unsuccessful']          = 'Unable to Create Account';
$lang['account_creation_duplicate_email']       = 'Email Already Used or Invalid';
$lang['account_creation_duplicate_identity']    = 'Identity Already Used or Invalid';
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';

// Password
$lang['password_change_successful']          = 'Password Successfully Changed';
$lang['password_change_unsuccessful']        = 'Unable to Change Password';
$lang['forgot_password_successful']          = 'Password Reset Email Sent';
$lang['forgot_password_unsuccessful']        = 'Unable to email the Reset Password link'; // Corrected: "Unable to email the Reset Password link" is more accurate than "Unable to Reset Password" if email sending fails.

// Activation
$lang['activate_successful']                 = 'Account Activated';
$lang['activate_unsuccessful']               = 'Unable to Activate Account';
$lang['deactivate_successful']               = 'Account De-Activated';
$lang['deactivate_unsuccessful']             = 'Unable to De-Activate Account';
$lang['activation_email_successful']         = 'Activation Email Sent. Please check your inbox or spam';
$lang['activation_email_unsuccessful']       = 'Unable to Send Activation Email';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful']                    = 'Logged In Successfully';
$lang['login_unsuccessful']                  = 'Incorrect Login Details'; // Changed from "Incorrect Login" for clarity
$lang['login_unsuccessful_not_active']       = 'Account is inactive. Please activate your account first.'; // Added instruction
$lang['login_timeout']                       = 'You have made too many login attempts. Your account has been temporarily locked. Please try again later.'; // More descriptive
$lang['logout_successful']                   = 'Logged Out Successfully';
$lang['login_unsuccessful_empty_credentials'] = 'Please enter your email and password.'; // New specific error for model

// Account Changes
$lang['update_successful']                   = 'Account Information Successfully Updated';
$lang['update_unsuccessful']                 = 'Unable to Update Account Information';
$lang['delete_successful']                   = 'User Deleted Successfully'; // Added "Successfully"
$lang['delete_unsuccessful']                 = 'Unable to Delete User';
$lang['delete_current_user_unsuccessful']    = 'You cannot delete your own account.'; // New specific error for model
$lang['update_unsuccessful_user_not_found']  = 'User not found, unable to update information.'; // New specific error for model
$lang['account_update_duplicate_identity']   = 'The identity is already in use by another account.'; // New specific error for model
$lang['account_update_duplicate_email']      = 'The email address is already in use by another account.'; // New specific error for model
$lang['update_unsuccessful_password_hash_failed'] = 'Failed to update password. Please try again.'; // New specific error for model


// Groups
$lang['group_creation_successful']           = 'Group Created Successfully'; // Corrected "created"
$lang['group_already_exists']                = 'Group name already taken';
$lang['group_update_successful']             = 'Group details updated successfully'; // Added "successfully"
$lang['group_delete_successful']             = 'Group deleted successfully'; // Added "successfully"
$lang['group_delete_unsuccessful']           = 'Unable to delete group';
$lang['group_delete_notallowed']             = 'Cannot delete the administrators\' group'; // Corrected "Can\'t"
$lang['group_name_required']                 = 'Group name is a required field';
$lang['group_name_admin_not_alter']          = 'Admin group name can not be changed';
$lang['group_creation_unsuccessful']         = 'Unable to create group'; // New error
$lang['group_update_unsuccessful_not_found'] = 'Group not found, unable to update.'; // New error
$lang['group_update_no_data']                = 'No data provided to update the group.'; // New error
$lang['group_delete_missing_id']             = 'Group ID is missing for delete operation.'; // New error
$lang['group_delete_unsuccessful_not_found'] = 'Group not found, unable to delete.'; // New error


// Activation Email
$lang['email_activation_subject']            = 'Account Activation';
$lang['email_activate_heading']              = 'Activate account for %s';
$lang['email_activate_subheading']           = 'Please click this link to %s.';
$lang['email_activate_link']                 = 'Activate Your Account';

// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Forgotten Password Verification';
$lang['email_forgot_password_heading']       = 'Reset Password for %s';
$lang['email_forgot_password_subheading']    = 'Please click this link to %s.';
$lang['email_forgot_password_link']          = 'Reset Your Password';

// Password Reset
$lang['password_reset_successful']           = 'Password Reset Successfully'; // Added for reset_password success
$lang['password_reset_unsuccessful']         = 'Unable to Reset Password'; // Added for reset_password failure

// Misc
$lang['remember_user_unsuccessful']          = 'Failed to set remember me cookie.';
$lang['remember_user_successful']            = 'Remember me cookie set successfully.';
$lang['post_login_remembered_user_unsuccessful'] = 'Failed to login with remember me cookie.';
$lang['post_login_remembered_user_successful'] = 'Successfully logged in with remember me cookie.';
$lang['rehash_password_successful']          = 'Password rehashed and updated successfully.';
$lang['rehash_password_unsuccessful']        = 'Failed to rehash and update password.';
$lang['post_forgotten_password_unsuccessful']= 'Failed to process forgotten password request.';
$lang['post_forgotten_password_successful']  = 'Forgotten password process initiated successfully.';
$lang['post_activate_unsuccessful']          = 'Post activation process failed.';
$lang['post_activate_successful']            = 'Post activation process successful.';
$lang['post_change_password_unsuccessful']   = 'Post password change process failed.';
$lang['post_change_password_successful']     = 'Post password change process successful.';
$lang['post_delete_user_unsuccessful']       = 'Post user deletion process failed.';
$lang['post_delete_user_successful']         = 'Post user deletion process successful.';
$lang['post_update_user_unsuccessful']       = 'Post user update process failed.';
$lang['post_update_user_successful']         = 'Post user update process successful.';
$lang['pre_sha1_password_migration']         = 'Attempting SHA1 password migration.';
$lang['post_sha1_password_migration_unsuccessful'] = 'SHA1 password migration failed.';
$lang['post_sha1_password_migration_successful'] = 'SHA1 password migrated successfully.';

?>
