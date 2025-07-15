<?php
/**
 * Name:    Ion Auth
 * Author:  Ben Edmunds
 *           ben.edmunds@gmail.com
 *           @benedmunds
 *
 * Added Awesomeness: Phil Sturgeon
 *
 * Created:  10.01.2009
 *
 * Description:  Modified auth system based on redux_auth with extensive customi
zation. This is basically what Redux Auth 2 should be.
 * Original Author name has been kept but that does not mean that the method has
 not been modified.
 *
 * Requirements: PHP5.6 or above
 *
 * @package    CodeIgniter-Ion-Auth
 * @author     Ben Edmunds
 * @link       http://github.com/benedmunds/CodeIgniter-Ion-Auth
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 | -------------------------------------------------------------------------
 | Database group name option.
 | -------------------------------------------------------------------------
 | Allows to select a specific group for the database connection
 |
 | Default is empty: uses default group defined in CI's configuration
 | (see application/config/database.php, $active_group variable)
 */
$config['database_group_name'] = '';

/*
| -------------------------------------------------------------------------
| Tables.
| -------------------------------------------------------------------------
| Database table names.
*/
$config['tables']['users']           = 'users';
$config['tables']['groups']          = 'groups';
$config['tables']['users_groups']    = 'users_groups';
$config['tables']['login_attempts']  = 'login_attempts';

/*
 | Users table column and Group table column you want to join WITH.
 |
 | Joins from users.id
 | Joins from groups.id
 */
$config['join']['users']  = 'user_id';
$config['join']['groups'] = 'group_id';

/*
 | -------------------------------------------------------------------------
 | Hash Method (bcrypt or argon2)
 | -------------------------------------------------------------------------
 | Bcrypt is available in PHP 5.3+
 | Argon2 is available in PHP 7.2
 | Argon2id is available in PHP 7.3
 |
 | Bcrypt is the current PHP language default.
 */
$config['hash_method']                  = 'argon2';     // bcrypt, argon2, or ar
gon2id
$config['bcrypt_default_cost']          = defined('PASSWORD_BCRYPT_DEFAULT_COST'
) ? PASSWORD_BCRYPT_DEFAULT_COST : 10;
$config['argon2_default_params']        = [
        'memory_cost'   => defined('PASSWORD_ARGON2_DEFAULT_MEMORY_COST') ? PASS
WORD_ARGON2_DEFAULT_MEMORY_COST : 1 << 12,
        'time_cost'     => defined('PASSWORD_ARGON2_DEFAULT_TIME_COST') ? PASSWO
RD_ARGON2_DEFAULT_TIME_COST : 2,
        'threads'       => defined('PASSWORD_ARGON2_DEFAULT_THREADS') ? PASSWORD
_ARGON2_DEFAULT_THREADS : 2
];


/*
 | -------------------------------------------------------------------------
 | Authentication options.
 | -------------------------------------------------------------------------
 */
$config['site_title']                 = "Abadia";       // Site Title
$config['admin_email']                = "admin@abadia.com"; // Admin Email
$config['default_group']              = 'members';           // Default group, u
se name
$config['admin_group']                = 'admin';             // Default administ
rators group, use name
$config['identity']                   = 'email';             /* You can use any
unique column in your table as identity column. */
$config['min_password_length']        = 8;
$config['email_activation']           = FALSE;               // Email Activation
 for registration
$config['manual_activation']          = FALSE;               // Manual Activatio
n for registration
$config['remember_users']             = TRUE;                // Allow users to b
e remembered and enable auto-login
$config['user_expire']                = 86500;               // How long to reme
mber the user (seconds).
$config['user_extend_on_login']       = FALSE;               // Extend the users
 cookies every time they auto-login
$config['track_login_attempts']       = TRUE;                // Track the number
 of failed login attempts for each user or ip.
$config['track_login_ip_address']     = TRUE;
$config['maximum_login_attempts']     = 3;
$config['lockout_time']               = 600;                 /* The number of se
conds to lockout an account due to exceeded attempts */
$config['forgot_password_expiration'] = 1800;                /* The number of se
conds after which a forgot password request will expire. */
$config['recheck_timer']              = 0;                   /* The number of se
conds after which the session is checked again against database */

/*
 | -------------------------------------------------------------------------
 | Login session hash
 | -------------------------------------------------------------------------
 */
$config['session_hash'] = 'PLEASE_CHANGE_ME_TO_A_UNIQUE_RANDOM_STRING'; // IMPORTANT: Change this!

/*
 | -------------------------------------------------------------------------
 | Cookie options.
 | -------------------------------------------------------------------------
 */
$config['remember_cookie_name'] = 'remember_code';

/*
 | -------------------------------------------------------------------------
 | Email options.
 | -------------------------------------------------------------------------
 */
$config['use_ci_email'] = FALSE; // Send Email using the builtin CI email class
$config['email_config'] = [
        'mailtype' => 'html',
];

/*
 | -------------------------------------------------------------------------
 | Email templates.
 | -------------------------------------------------------------------------
 */
$config['email_templates'] = 'auth/email/';
$config['email_activate'] = 'activate.tpl.php';
$config['email_forgot_password'] = 'forgot_password.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Message Delimiters.
 | -------------------------------------------------------------------------
 */
$config['delimiters_source']       = 'config';
$config['message_start_delimiter'] = '<p>';
$config['message_end_delimiter']   = '</p>';
$config['error_start_delimiter']   = '<p>';
$config['error_end_delimiter']     = '</p>';
