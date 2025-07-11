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
 |
 | Bcrypt specific:
 |              bcrypt_default_cost settings:  This defines how strong the encry
ption will be.
 |              However, higher the cost, longer it will take to hash (CPU usage
) So adjust
 |              this based on your server hardware.
 |
 |              You can (and should!) benchmark your server. This can be done ea
sily with this little script:
 |              https://gist.github.com/Indigo744/24062e07477e937a279bc97b378c34
02
 |
 |              With bcrypt, an example hash of "password" is:
 |              $2y$08$200Z6ZZbp3RAEXoaWcMA6uJOFicwNZaqk4oDhqTUiFXFe63MG.Daa
 |
 |
 | Argon2 specific:
 |              argon2_default_params settings:  This is an array containing the
 options for the Argon2 algorithm.
 |              You can define 3 differents keys:
 |                      memory_cost (default 4096 kB)
 |                              Maximum memory (in kBytes) that may be used to c
ompute the Argon2 hash
 |                              The spec recommends setting the memory cost to a
 power of 2.
 |                      time_cost (default 2)
 |                              Number of iterations (used to tune the running t
ime independently of the memory size).
                This defines how strong the encryption will be.
 |                      threads (default 2)
 |                              Number of threads to use for computing the Argon
2 hash
 |                              The spec recommends setting the number of thread
s to a power of 2.
 |
 |              You can (and should!) benchmark your server. This can be done ea
sily with this little script:
 |              https://gist.github.com/Indigo744/e92356282eb808b94d08d9cc6e3788
4c
 |
 |              With argon2, an example hash of "password" is:
 |              $argon2i$v=19$m=1024,t=2,p=2$VEFSSU4wSzh3cllVdE1JZQ$PDeks/7JoKek
QrJa9HlfkXIk8dAeZXOzUxLBwNFbZ44
 |
 |
 | For more information, check the password_hash function help: http://php.net/m
anual/en/function.password-hash.php
 |
 */
$config['hash_method']                  = 'bcrypt';     // bcrypt, argon2, or ar
gon2id
$config['bcrypt_default_cost']          = defined('PASSWORD_BCRYPT_DEFAULT_COST'
) ? PASSWORD_BCRYPT_DEFAULT_COST : 10;          // Set cost according to your se
rver benchmark - but no lower than 10 (default PHP value)
$config['argon2_default_params']        = [
        'memory_cost'   => defined('PASSWORD_ARGON2_DEFAULT_MEMORY_COST') ? PASS
WORD_ARGON2_DEFAULT_MEMORY_COST : 1 << 12,
        'time_cost'     => defined('PASSWORD_ARGON2_DEFAULT_TIME_COST') ? PASSWO
RD_ARGON2_DEFAULT_TIME_COST : 2,
        'threads'       => defined('PASSWORD_ARGON2_DEFAULT_THREADS') ? PASSWORD
_ARGON2_DEFAULT_THREADS : 2
];

// NOTE - the admin specific hashing config fields are no longer used, all users
 share the same hashing params now

/*
 | -------------------------------------------------------------------------
 | Authentication options.
 | -------------------------------------------------------------------------
 | maximum_login_attempts:      This maximum is not enforced by the library, but
 is used by
 |                                                      is_max_login_attempts_ex
ceeded().
 |                                                      The controller should ch
eck this function and act appropriately.
 |                                                      If this variable set to
0, there is no maximum.
 | min_password_length:         This minimum is not enforced directly by the lib
rary.
 |                                                      The controller should de
fine a validation rule to enforce it.
 |                                                      See the Auth controller
for an example implementation.
 |
 | The library will fail for empty password or password size above 4096 bytes.
 | This is an arbitrary (long) value to protect against DOS attack.
 */
$config['site_title']                 = "Abadia";       // Site Title, example.com
$config['admin_email']                = "admin@abadia.com"; // Admin Email, admi
n@example.com
$config['default_group']              = 'members';           // Default group, u
se name
$config['admin_group']                = 'admin';             // Default administ
rators group, use name
$config['identity']                   = 'email';             /* You can use any
unique column in your table as identity column.

                                    The values in this column, alongside passwor
d, will be used for login purposes

                                    IMPORTANT: If you are changing it from the d
efault (email),

                                                   update the UNIQUE constraint
in your DB */
$config['min_password_length']        = 8;                   // Minimum Required
 Length of Password (not enforced by lib - see note above)
$config['email_activation']           = FALSE;               // Email Activation
 for registration
$config['manual_activation']          = FALSE;               // Manual Activatio
n for registration
$config['remember_users']             = TRUE;                // Allow users to b
e remembered and enable auto-login
$config['user_expire']                = 86500;               // How long to reme
mber the user (seconds). Set to zero for no expiration - see sess_expiration in
CodeIgniter Session Config for session expiration
$config['user_extend_on_login']       = FALSE;               // Extend the users
 cookies every time they auto-login
$config['track_login_attempts']       = TRUE;                // Track the number
 of failed login attempts for each user or ip.
$config['track_login_ip_address']     = TRUE;                // Track login atte
mpts by IP Address, if FALSE will track based on identity. (Default: TRUE)
$config['maximum_login_attempts']     = 3;                   // The maximum numb
er of failed login attempts.
$config['lockout_time']               = 600;                 /* The number of se
conds to lockout an account due to exceeded attempts

                                        You should not use a value below 60 (1 m
inute) */
$config['forgot_password_expiration'] = 1800;                /* The number of se
conds after which a forgot password request will expire. If set to 0, forgot pas
sword requests will not expire.

                        30 minutes to 1 hour are good values (enough for a user
to receive the email and reset its password)

                        You should not set a value too high, as it would be a se
curity issue! */
$config['recheck_timer']              = 0;                   /* The number of se
conds after which the session is checked again against database to see if the us
er still exists and is active.

                                        Leave 0 if you don't want session rechec
k. if you really think you need to recheck the session against database, we woul
d

                                        recommend a higher value, as this would
affect performance */

/*
 | -------------------------------------------------------------------------
 | Login session hash
 | -------------------------------------------------------------------------
 | session_hash Default: sha1()
 |
 | Please customize
 */
$config['session_hash'] = 'CHANGE_THIS_LATER_6583d6c4f205998ecacc9f51b68a2a2e44ea0006'; // IMPORTANT: Change this to a unique random value!

/*
 | -------------------------------------------------------------------------
 | Cookie options.
 | -------------------------------------------------------------------------
 | remember_cookie_name Default: remember_code
 */
$config['remember_cookie_name'] = 'remember_code';

/*
 | -------------------------------------------------------------------------
 | Email options.
 | -------------------------------------------------------------------------
 | email_config:
 |        'file' = Use the default CI config or use from a config file
 |        array  = Manually set your email config settings
 */
$config['use_ci_email'] = FALSE; // Send Email using the builtin CI email class,
 if false it will return the code and the identity. Set to TRUE if you want to send emails.
$config['email_config'] = [
        'mailtype' => 'html',
];

/*
 | -------------------------------------------------------------------------
 | Email templates.
 | -------------------------------------------------------------------------
 | Folder where email templates are stored.
 | Default: auth/
 */
$config['email_templates'] = 'auth/email/';

/*
 | -------------------------------------------------------------------------
 | Activate Account Email Template
 | -------------------------------------------------------------------------
 | Default: activate.tpl.php
 */
$config['email_activate'] = 'activate.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Forgot Password Email Template
 | -------------------------------------------------------------------------
 | Default: forgot_password.tpl.php
 */
$config['email_forgot_password'] = 'forgot_password.tpl.php';

/*
 | -------------------------------------------------------------------------
 | Message Delimiters.
 | -------------------------------------------------------------------------
 */
$config['delimiters_source']       = 'config';  // "config" = use the settings d
efined here, "form_validation" = use the settings defined in CI's form validatio
n library
$config['message_start_delimiter'] = '<p>';     // Message start delimiter
$config['message_end_delimiter']   = '</p>';    // Message end delimiter
$config['error_start_delimiter']   = '<p>';             // Error message start d
elimiter
$config['error_end_delimiter']     = '</p>';    // Error message end delimiter
