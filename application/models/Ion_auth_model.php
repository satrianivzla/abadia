<?php
/**
 * Name:    Ion Auth Model
 * Author:  Ben Edmunds
 *           ben.edmunds@gmail.com
 * @benedmunds
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

/**
 * Class Ion Auth Model
 * @property Ion_auth $ion_auth The Ion_auth library
 * @property CI_DB_query_builder $db The Database object
 * @property CI_Config $config The CI Config object
 * @property CI_Session $session The CI Session object
 * @property CI_Input $input The CI Input object
 * @property CI_Lang $lang The CI Lang object
 */
class Ion_auth_model extends CI_Model
{
        /**
         * Max cookie lifetime constant
         */
        const MAX_COOKIE_LIFETIME = 63072000; // 2 years = 60*60*24*365*2 = 6307
2000 seconds;

        /**
         * Max password size constant
         */
        const MAX_PASSWORD_SIZE_BYTES = 4096;

        /**
         * Holds an array of tables used
         *
         * @var array
         */
        public $tables = [];

        /**
         * Holds an array of joins used
         *
         * @var array
         */
        public $join = [];

        /**
         * activation code
         *
         * Set by deactivate() function
         * Also set on register() function, if email_activation
         * option is activated
         *
         * This is the value devs should give to the user
         * (in an email, usually)
         *
         * It contains the *user* version of the activation code
         * It's a value of the form "selector.validator"
         *
         * This is not the same activation_code as the one in DB.
         * The DB contains a *hashed* version of the validator
         * and a selector in another column.
         *
         * THe selector is not private, and only used to lookup
         * the validator.
         *
         * The validator is private, and to be only known by the user
         * So in case of DB leak, nothing could be actually used.
         *
         * @var string
         */
        public $activation_code;

        /**
         * new password
         *
         * @var string
         */
        public $new_password;

        /**
         * Identity
         *
         * @var string
         */
        public $identity;

        /**
         * Identity column
         *
         * @var string
         */
        public $identity_column;

        /**
         * Message start delimiter
         *
         * @var string
         */
        public $message_start_delimiter ;

        /**
         * Message end delimiter
         *
         * @var string
         */
        public $message_end_delimiter ;

        /**
         * Hash method
         *
         * @var string
         */
        public $hash_method;

        /**
         * Where
         *
         * @var array
         */
        public $_ion_where = [];

        /**
         * Select
         *
         * @var array
         */
        public $_ion_select = [];

        /**
         * Like
         *
         * @var array
         */
        public $_ion_like = [];

        /**
         * Limit
         *
         * @var string
         */
        public $_ion_limit = NULL;

        /**
         * Offset
         *
         * @var string
         */
        public $_ion_offset = NULL;

        /**
         * Order By
         *
         * @var string
         */
        public $_ion_order_by = NULL;

        /**
         * Order
         *
         * @var string
         */
        public $_ion_order = NULL;

        /**
         * Hooks
         *
         * @var object
         */
        protected $_ion_hooks;

        /**
         * Response
         *
         * @var string
         */
        protected $response = NULL;

        /**
         * message (uses lang file)
         *
         * @var string
         */
        protected $messages;

        /**
         * error message (uses lang file)
         *
         * @var string
         */
        protected $errors;

        /**
         * error start delimiter
         *
         * @var string
         */
        protected $error_start_delimiter;

        /**
         * error end delimiter
         *
         * @var string
         */
        protected $error_end_delimiter;

        /**
         * caching of users and their groups
         *
         * @var array
         */
        public $_cache_user_in_group = [];

        /**
         * caching of groups
         *
         * @var array
         */
        protected $_cache_groups = [];

        /**
         * Database object
         *
         * @var CI_DB_query_builder
         */
        // protected $db; // This will be auto-assigned by CI_Model

        public function __construct()
        {
                parent::__construct(); // Call parent constructor
                $this->load->config('ion_auth', TRUE); // Load config with TRUE to get as object, or access via $this->config->item('item', 'ion_auth')
                $this->load->helper(['cookie', 'date']);
                $this->load->language('ion_auth'); // Corrected: use language() instead of lang() for loading
                $this->load->library('session'); // Ensure session is loaded

                // initialize the database
                $group_name = $this->config->item('database_group_name', 'ion_auth');
                if (empty($group_name))
                {
                        // By default, use CI's db that should be already loaded
                        // $CI =& get_instance(); // Not needed, $this->db is available
                        // $this->db = $CI->db;
                }
                else
                {
                        // For specific group name, open a new specific connecti
on
                        $this->db = $this->load->database($group_name, TRUE, TRU
E);
                }

                // initialize db tables data
                $this->tables = $this->config->item('tables', 'ion_auth');

                // initialize data
                $this->identity_column = $this->config->item('identity', 'ion_au
th');
                $this->join = $this->config->item('join', 'ion_auth');

                // initialize hash method options (Bcrypt)
                $this->hash_method = $this->config->item('hash_method', 'ion_aut
h');

                // initialize messages and error
                $this->messages    = [];
                $this->errors      = [];
                $delimiters_source = $this->config->item('delimiters_source', 'i
on_auth');

                // load the error delimeters either from the config file or use
what's been supplied to form validation
                if ($delimiters_source === 'form_validation')
                {
                        // load in delimiters from form_validation
                        // to keep this simple we'll load the value using reflec
tion since these properties are protected
                        $this->load->library('form_validation');
                        $form_validation_class = new ReflectionClass("CI_Form_va
lidation");

                        $error_prefix = $form_validation_class->getProperty("_er
ror_prefix");
                        $error_prefix->setAccessible(TRUE);
                        $this->error_start_delimiter = $error_prefix->getValue($
this->form_validation);
                        $this->message_start_delimiter = $this->error_start_deli
miter;

                        $error_suffix = $form_validation_class->getProperty("_er
ror_suffix");
                        $error_suffix->setAccessible(TRUE);
                        $this->error_end_delimiter = $error_suffix->getValue($th
is->form_validation);
                        $this->message_end_delimiter = $this->error_end_delimite
r;
                }
                else
                {
                        // use delimiters from config
                        $this->message_start_delimiter = $this->config->item('me
ssage_start_delimiter', 'ion_auth');
                        $this->message_end_delimiter = $this->config->item('mess
age_end_delimiter', 'ion_auth');
                        $this->error_start_delimiter = $this->config->item('erro
r_start_delimiter', 'ion_auth');
                        $this->error_end_delimiter = $this->config->item('error_
end_delimiter', 'ion_auth');
                }

                // initialize our hooks object
                $this->_ion_hooks = new stdClass;

                $this->trigger_events('model_constructor');
        }

        /**
         * Getter to the DB connection used by Ion Auth
         * May prove useful for debugging
         *
         * @return object
         */
        public function db()
        {
                return $this->db;
        }

        /**
         * Hashes the password to be stored in the database.
         *
         * @param string $password
         * @param string $identity Deprecated, identity is no longer used when hashing pas
swords
         *
         * @return false|string
         * @author Mathew
         */
        public function hash_password($password, $identity = NULL)
        {
                // Check for empty password, or password containing null char, o
r password above limit
                // Null char may pose issue: http://php.net/manual/en/function.p
assword-hash.php#118603
                // Long password may pose DOS issue (note: strlen gives size in
bytes and not in multibyte symbol)
                if (empty($password) || strpos($password, "\0") !== FALSE ||
                        strlen($password) > self::MAX_PASSWORD_SIZE_BYTES)
                {
                        return FALSE;
                }

                $algo = $this->_get_hash_algo();
                $params = $this->_get_hash_parameters();

                if ($algo !== FALSE && $params !== FALSE)
                {
                        $hash = password_hash($password, $algo, $params);
                        if (is_null($hash) || $hash === FALSE) { // Check explicitly for null or false
                                return FALSE;
                        }
                        return $hash;
                }

                return FALSE;
        }

        /**
         * This function takes a password and validates it
         * against an entry in the users table.
         *
         * @param string        $password
         * @param string        $hash_password_db
         * @param string        $identity                       optional @deprec
ated only for BC SHA1
         *
         * @return bool
         * @author Mathew
         */
        public function verify_password($password, $hash_password_db, $identity
= NULL)
        {
                // Check for empty id or password, or password containing null c
har, or password above limit
                // Null char may pose issue: http://php.net/manual/en/function.p
assword-hash.php#118603
                // Long password may pose DOS issue (note: strlen gives size in
bytes and not in multibyte symbol)
                if (empty($password) || empty($hash_password_db) || strpos($pass
word, "\0") !== FALSE
                        || strlen($password) > self::MAX_PASSWORD_SIZE_BYTES)
                {
                        return FALSE;
                }

                // password_hash always starts with $
                if (strpos($hash_password_db, '$') === 0)
                {
                        return password_verify($password, $hash_password_db);
                }
                else
                {
                        // Handle legacy SHA1 @TODO to delete in later revision
                        return $this->_password_verify_sha1_legacy($identity, $p
assword, $hash_password_db);
                }
        }

        /**
         * Check if password needs to be rehashed
         * If true, then rehash and update it in DB
         *
         * @param string $hash
         * @param string $identity
         * @param string $password
         *
         */
        public function rehash_password_if_needed($hash, $identity, $password)
        {
                $algo = $this->_get_hash_algo();
                $params = $this->_get_hash_parameters();

                if ($algo !== FALSE && $params !== FALSE)
                {
                        if (password_needs_rehash($hash, $algo, $params))
                        {
                                if ($this->_set_password_db($identity, $password
))
                                {
                                        $this->trigger_events(['rehash_password'
, 'rehash_password_successful']);
                                }
                                else
                                {
                                        $this->trigger_events(['rehash_password'
, 'rehash_password_unsuccessful']);
                                }
                        }
                }
        }

        /**
         * Get a user by its activation code
         *
         * @param string       $user_code the activation code. It's the *user* one, containing "selector.validator"
         *
         * @return    bool|object
         * @author Indigo
         */
        public function get_user_by_activation_code($user_code)
        {
                // Retrieve the token object from the code
                $token = $this->_retrieve_selector_validator_couple($user_code);

                if ($token)
                {
                        // Retrieve the user according to this selector
                        $user = $this->where('activation_selector', $token->sele
ctor)->users()->row();

                        if ($user)
                        {
                                // Check the hash against the validator
                                if ($this->verify_password($token->validator, $u
ser->activation_code)) // activation_code in DB is the hashed validator
                                {
                                        return $user;
                                }
                        }
                }

                return FALSE;
        }

        /**
         * Validates and removes activation code.
         *
         * @param int|string $id                the user identifier
         * @param string|bool       $code              the *user* activation code
         *                                                              if omitt
ed, simply activate the user without check
         *
         * @return bool
         * @author Mathew
         */
        public function activate($id, $code = FALSE)
        {
                $this->trigger_events('pre_activate');

                $user = FALSE; // Initialize user
                if ($code !== FALSE) {
                        $user = $this->get_user_by_activation_code($code);
                }

                // Activate if no code is given
                // Or if a user was found with this code, and that it matches th
e id
                if ($code === FALSE || ($user && (int)$user->id === (int)$id)) // Ensure type comparison for ID
                {
                        $data = [
                            'activation_selector' => NULL,
                            'activation_code' => NULL,
                            'active'          => 1
                        ];

                        $this->trigger_events('extra_where');
                        $this->db->update($this->tables['users'], $data, ['id' =
> $id]);

                        if ($this->db->affected_rows() === 1)
                        {
                                $this->trigger_events(['post_activate', 'post_ac
tivate_successful']);
                                $this->set_message('activate_successful');
                                return TRUE;
                        }
                }

                $this->trigger_events(['post_activate', 'post_activate_unsuccess
ful']);
                $this->set_error('activate_unsuccessful');
                return FALSE;
        }


        /**
         * Updates a users row with an activation code.
         *
         * @param int|string|null $id
         *
         * @return bool
         * @author Mathew
         */
        public function deactivate($id = NULL)
        {
                $token = $this->_generate_selector_validator_couple(20, 40);
                $this->activation_code = $token->user_code; // This is "selector.validator"

                $data = [
                    'activation_selector' => $token->selector,
                    'activation_code' => $token->validator_hashed, // Store the hashed validator
                    'active'          => 0
                ];

                $this->trigger_events('extra_where');
                $this->db->update($this->tables['users'], $data, ['id' => $id]);

                $return = $this->db->affected_rows() == 1;
                if ($return)
                {
                        $this->set_message('deactivate_successful');
                }
                else
                {
                        $this->set_error('deactivate_unsuccessful');
                }

                return $return;
        }

        /**
         * Clear the forgotten password code for a user
         *
         * @param string $identity
         *
         * @return bool Success
         */
        public function clear_forgotten_password_code($identity) {

                if (empty($identity))
                {
                        return FALSE;
                }

                $data = [
                        'forgotten_password_selector' => NULL,
                        'forgotten_password_code' => NULL,
                        'forgotten_password_time' => NULL
                ];

                $this->trigger_events('extra_where'); // Add this line
                $this->db->update($this->tables['users'], $data, [$this->identit
y_column => $identity]);

                return TRUE; // Assume success if query runs, check affected_rows if specific result needed
        }

        /**
         * Clear the remember code for a user
         *
         * @param string $identity
         *
         * @return bool Success
         */
        public function clear_remember_code($identity) {

                if (empty($identity))
                {
                        return FALSE;
                }

                $data = [
                        'remember_selector' => NULL,
                        'remember_code' => NULL
                ];

                $this->trigger_events('extra_where'); // Add this line
                $this->db->update($this->tables['users'], $data, [$this->identit
y_column => $identity]);

                return TRUE; // Assume success
        }

        /**
         * Reset password
         *
         * @param    string $identity
         * @param    string $new
         *
         * @return bool
         * @author Mathew
         */
        public function reset_password($identity, $new) {
                $this->trigger_events('pre_change_password');

                if (!$this->identity_check($identity)) {
                        $this->trigger_events(['post_change_password', 'post_cha
nge_password_unsuccessful']);
                        $this->set_error('password_reset_unsuccessful'); // More specific error
                        return FALSE;
                }

                $return = $this->_set_password_db($identity, $new);

                if ($return)
                {
                        // Clear forgotten password code after successful reset
                        $this->clear_forgotten_password_code($identity);
                        $this->trigger_events(['post_change_password', 'post_cha
nge_password_successful']);
                        $this->set_message('password_change_successful');
                }
                else
                {
                        $this->trigger_events(['post_change_password', 'post_cha
nge_password_unsuccessful']);
                        $this->set_error('password_change_unsuccessful');
                }

                return $return;
        }

        /**
         * Change password
         *
         * @param    string $identity
         * @param    string $old
         * @param    string $new
         *
         * @return bool
         * @author Mathew
         */
        public function change_password($identity, $old, $new)
        {
                $this->trigger_events('pre_change_password');

                $this->trigger_events('extra_where');

                $query = $this->db->select('id, password, ' . $this->identity_column . ' as identity_col') // Select identity for verify_password legacy
                                  ->where($this->identity_column, $identity)
                                  ->limit(1)
                                  ->order_by('id', 'desc')
                                  ->get($this->tables['users']);

                if ($query->num_rows() !== 1)
                {
                        $this->trigger_events(['post_change_password', 'post_cha
nge_password_unsuccessful']);
                        $this->set_error('password_change_unsuccessful'); // User not found
                        return FALSE;
                }

                $user = $query->row();

                if ($this->verify_password($old, $user->password, $user->identity_col)) // Pass identity for legacy
                {
                        $result = $this->_set_password_db($identity, $new);

                        if ($result)
                        {
                                $this->trigger_events(['post_change_password', '
post_change_password_successful']);
                                $this->set_message('password_change_successful')
;
                        }
                        else
                        {
                                $this->trigger_events(['post_change_password', '
post_change_password_unsuccessful']);
                                $this->set_error('password_change_unsuccessful')
; // DB update failed
                        }

                        return $result;
                }
                $this->trigger_events(['post_change_password', 'post_change_password_unsuccessful']); // Add this line
                $this->set_error('password_change_unsuccessful'); // Incorrect old password
                return FALSE;
        }

        /**
         * Checks username
         *
         * @param string $username
         *
         * @return bool
         * @author Mathew
         */
        public function username_check($username = '')
        {
                $this->trigger_events('username_check');

                if (empty($username))
                {
                        return FALSE;
                }

                $this->trigger_events('extra_where');

                return $this->db->where('username', $username)
                                                ->limit(1)
                                                ->count_all_results($this->table
s['users']) > 0;
        }

        /**
         * Checks email
         *
         * @param string $email
         *
         * @return bool
         * @author Mathew
         */
        public function email_check($email = '')
        {
                $this->trigger_events('email_check');

                if (empty($email))
                {
                        return FALSE;
                }

                $this->trigger_events('extra_where');

                return $this->db->where('email', $email)
                                                ->limit(1)
                                                ->count_all_results($this->table
s['users']) > 0;
        }

        /**
         * Identity check
         *
         * @param $identity string
         *
         * @return bool
         * @author Mathew
         */
        public function identity_check($identity = '')
        {
                $this->trigger_events('identity_check');

                if (empty($identity))
                {
                        return FALSE;
                }
                $this->trigger_events('extra_where'); // Add this line
                return $this->db->where($this->identity_column, $identity)
                                                ->limit(1)
                                                ->count_all_results($this->table
s['users']) > 0;
        }

        /**
         * Get user ID from identity
         *
         * @param $identity string
         *
         * @return bool|int
         */
        public function get_user_id_from_identity($identity = '')
        {
                if (empty($identity))
                {
                        return FALSE;
                }
                $this->trigger_events('extra_where'); // Add this line
                $query = $this->db->select('id')
                                                  ->where($this->identity_column
, $identity)
                                                  ->limit(1)
                                                  ->get($this->tables['users']);

                if ($query->num_rows() !== 1)
                {
                        return FALSE;
                }

                $user = $query->row();

                return $user->id;
        }

        /**
         * Insert a forgotten password key.
         *
         * @param    string $identity
         *
         * @return    bool|string
         * @author  Mathew
         * @updated Ryan
         */
        public function forgotten_password($identity)
        {
                if (empty($identity))
                {
                        $this->trigger_events(['post_forgotten_password', 'post_
forgotten_password_unsuccessful']);
                        return FALSE;
                }

                // Generate random token: smaller size because it will be in the
 URL
                $token = $this->_generate_selector_validator_couple(20, 80); // Sizes from IonAuth v2, v3 uses 40, 128 by default in _generate_selector_validator_couple()

                $update = [
                        'forgotten_password_selector' => $token->selector,
                        'forgotten_password_code' => $token->validator_hashed,
                        'forgotten_password_time' => time()
                ];

                $this->trigger_events('extra_where');
                $this->db->update($this->tables['users'], $update, [$this->ident
ity_column => $identity]);

                if ($this->db->affected_rows() === 1)
                {
                        $this->trigger_events(['post_forgotten_password', 'post_
forgotten_password_successful']);
                        return $token->user_code;
                }
                else
                {
                        $this->trigger_events(['post_forgotten_password', 'post_
forgotten_password_unsuccessful']);
                        return FALSE;
                }
        }

        /**
         * Get a user from a forgotten password key.
         *
         * @param    string $user_code
         *
         * @return    bool|object
         * @author  Mathew
         * @updated Ryan
         */
        public function get_user_by_forgotten_password_code($user_code)
        {
                // Retrieve the token object from the code
                $token = $this->_retrieve_selector_validator_couple($user_code);

                if($token) {
                        // Retrieve the user according to this selector
                        $user = $this->where('forgotten_password_selector', $tok
en->selector)->users()->row();

                        if ($user)
                        {
                                // Check the hash against the validator
                                if ($this->verify_password($token->validator, $u
ser->forgotten_password_code)) // forgotten_password_code in DB is the hashed validator
                                {
                                        return $user;
                                }
                        }
                }

                return FALSE;
        }

        /**
         * Register
         *
         * @param    string $identity
         * @param    string $password
         * @param    string $email
         * @param    array  $additional_data
         * @param    array  $groups An array of group IDs, not names
         *
         * @return    bool|int The new user's ID or FALSE on failure
         * @author    Mathew
         */
        public function register($identity, $password, $email, $additional_data
= [], $groups = [])
        {
                $this->trigger_events('pre_register');

                $manual_activation = $this->config->item('manual_activation', 'i
on_auth');

                if ($this->identity_check($identity))
                {
                        $this->set_error('account_creation_duplicate_identity');
                        return FALSE;
                }
                // Check for email duplicate if identity is not email
                else if ($this->identity_column !== 'email' && $this->email_check($email))
                {
                        $this->set_error('account_creation_duplicate_email');
                        return FALSE;
                }


                // check if the default set in config exists in database
                $default_group_name = $this->config->item('default_group', 'ion_auth');
                $default_group = NULL;
                if ($default_group_name) {
                    $default_group_query = $this->db->get_where($this->tables['groups'], ['name' => $default_group_name], 1);
                    if ($default_group_query->num_rows() > 0) {
                        $default_group = $default_group_query->row();
                    }
                }


                if (!$default_group && empty($groups))
                {
                        $this->set_error('account_creation_missing_default_group
');
                        return FALSE;
                }


                // IP Address
                $ip_address = $this->input->ip_address();

                // Do not pass $identity as user is not known yet so there is no
 need
                $hashed_password = $this->hash_password($password); // Renamed variable

                if ($hashed_password === FALSE)
                {
                        $this->set_error('account_creation_unsuccessful'); // Password hashing failed
                        return FALSE;
                }

                // Users table.
                $data = [
                        $this->identity_column => $identity,
                        'password' => $hashed_password,
                        'email' => $email,
                        'ip_address' => $ip_address,
                        'created_on' => time(),
                        'active' => ($manual_activation === FALSE ? 1 : 0)
                ];

                // filter out any data passed that doesnt have a matching column
 in the users table
                // and merge the set user data and the additional data
                $user_data = array_merge($this->_filter_data($this->tables['user
s'], $additional_data), $data);

                $this->trigger_events('extra_set');

                $this->db->insert($this->tables['users'], $user_data);

                $id = $this->db->insert_id($this->tables['users'] . '_id_seq'); // For PostgreSQL an sequence name might be needed

                if(!$id) { // Check if insert was successful
                    $this->set_error('account_creation_unsuccessful');
                    return FALSE;
                }

                // add in groups array if it doesn't exists and stop adding into
 default group if default group ids are set
                if ($default_group && isset($default_group->id) && empty($groups))
                {
                        $groups[] = $default_group->id;
                }

                if (!empty($groups))
                {
                        // add to groups
                        foreach ($groups as $group_id) // $group should be $group_id
                        {
                                $this->add_to_group($group_id, $id);
                        }
                }

                $this->trigger_events('post_register');

                return $id; // Return the ID
        }

        /**
         * login
         *
         * @param    string $identity
         * @param    string $password
         * @param    bool   $remember
         *
         * @return    bool
         * @author    Mathew
         */
        public function login($identity, $password, $remember=FALSE)
        {
                $this->trigger_events('pre_login');

                if (empty($identity) || empty($password))
                {
                        $this->set_error('login_unsuccessful_empty_credentials'); // More specific error
                        return FALSE;
                }

                $this->trigger_events('extra_where');

                $query = $this->db->select($this->identity_column . ', email, id
, password, active, last_login')
                                                  ->where($this->identity_column
, $identity)
                                                  ->limit(1)
                                                  ->order_by('id', 'desc') // Not strictly necessary if identity is unique
                                                  ->get($this->tables['users']);

                if ($this->is_max_login_attempts_exceeded($identity))
                {
                        // Hash something anyway, just to take up time
                        $this->hash_password($password); // No identity needed for hashing

                        $this->trigger_events('post_login_unsuccessful');
                        $this->set_error('login_timeout');

                        return FALSE;
                }

                if ($query->num_rows() === 1)
                {
                        $user = $query->row();

                        if ($this->verify_password($password, $user->password, $user->{$this->identity_column})) // Pass identity for legacy SHA1
                        {
                                if ($user->active == 0)
                                {
                                        $this->trigger_events('post_login_unsucc
essful');
                                        $this->set_error('login_unsuccessful_not
_active');

                                        return FALSE;
                                }

                                $this->set_session($user);

                                $this->update_last_login($user->id);

                                $this->clear_login_attempts($identity);
                                $this->clear_forgotten_password_code($identity); // Clear on successful login

                                if ($this->config->item('remember_users', 'ion_a
uth'))
                                {
                                        if ($remember)
                                        {
                                                $this->remember_user($identity);
                                        }
                                        else
                                        {
                                                $this->clear_remember_code($iden
tity);
                                        }
                                }

                                // Rehash if needed
                                $this->rehash_password_if_needed($user->password
, $identity, $password);

                                // Regenerate the session (for security purpose:
 to avoid session fixation)
                                if (empty($this->session->userdata('ion_auth_session_hash')) || $this->session->userdata('ion_auth_session_hash') !== $this->config->item('session_hash', 'ion_auth')) {
                                    $this->session->sess_regenerate(FALSE); // Regenerate if hash is not set or doesn't match
                                }


                                $this->trigger_events(['post_login', 'post_login
_successful']);
                                $this->set_message('login_successful');

                                return TRUE;
                        }
                }

                // Hash something anyway, just to take up time
                $this->hash_password($password); // No identity needed

                $this->increase_login_attempts($identity);

                $this->trigger_events('post_login_unsuccessful');
                $this->set_error('login_unsuccessful');

                return FALSE;
        }

        /**
         * Verifies if the session should be rechecked according to the configur
ation item recheck_timer. If it does, then
         * it will check if the user is still active
         * @return bool
         */
        public function recheck_session()
        {
                $identity_val = $this->session->userdata($this->identity_column); // Use configured identity column
                if (empty($identity_val))
                {
                        return FALSE;
                }

                $recheck = (NULL !== $this->config->item('recheck_timer', 'ion_a
uth')) ? $this->config->item('recheck_timer', 'ion_auth') : 0;

                if ($recheck !== 0)
                {
                        $last_login = $this->session->userdata('last_check');
                        if ($last_login + $recheck < time())
                        {
                                $query = $this->db->select('id')
                                                                  ->where([
                                                                          $this-
>identity_column => $identity_val, // Use configured identity column
                                                                          'activ
e' => '1'
                                                                  ])
                                                                  ->limit(1)
                                                                  ->order_by('id
', 'desc')
                                                                  ->get($this->t
ables['users']);
                                if ($query->num_rows() === 1)
                                {
                                        $this->session->set_userdata('last_check
', time());
                                }
                                else
                                {
                                        $this->trigger_events('logout');

                                        // $identity_config = $this->config->item('identity', 'ion_auth'); // Already have $this->identity_column

                                        $this->session->unset_userdata([$this->identity_column, 'id', 'user_id', 'email', 'ion_auth_session_hash', 'last_check', 'old_last_login']); // Unset all relevant session data

                                        return FALSE;
                                }
                        }
                }

                $session_hash = $this->session->userdata('ion_auth_session_hash'
);
                // Check if session_hash exists before comparing
                return !empty($session_hash) && $session_hash === $this->config->i
tem('session_hash', 'ion_auth');
        }

        /**
         * is_max_login_attempts_exceeded
         * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/i
lkon/Tank-Auth)
         *
         * @param string      $identity   user's identity
         * @param string|null $ip_address IP address
         *                                Only used if track_login_ip_address is
 set to TRUE.
         *                                If NULL (default value), the current I
P address is used.
         *                                Use get_last_attempt_ip($identity) to
retrieve a user's last IP
         *
         * @return boolean
         */
        public function is_max_login_attempts_exceeded($identity, $ip_address =
NULL)
        {
                if ($this->config->item('track_login_attempts', 'ion_auth'))
                {
                        $max_attempts = $this->config->item('maximum_login_attem
pts', 'ion_auth');
                        if ($max_attempts > 0)
                        {
                                $attempts = $this->get_attempts_num($identity, $
ip_address);
                                return $attempts >= $max_attempts;
                        }
                }
                return FALSE;
        }

        /**
         * Get number of login attempts for the given IP-address or identity
         * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/i
lkon/Tank-Auth)
         *
         * @param string      $identity   User's identity
         * @param string|null $ip_address IP address
         *                                Only used if track_login_ip_address is
 set to TRUE.
         *                                If NULL (default value), the current I
P address is used.
         *                                Use get_last_attempt_ip($identity) to
retrieve a user's last IP
         *
         * @return int
         */
        public function get_attempts_num($identity, $ip_address = NULL)
        {
                if ($this->config->item('track_login_attempts', 'ion_auth'))
                {
                        $this->db->select('1', FALSE); // No need to select data
                        $this->db->where('login', $identity);
                        if ($this->config->item('track_login_ip_address', 'ion_a
uth'))
                        {
                                if (!isset($ip_address))
                                {
                                        $ip_address = $this->input->ip_address()
;
                                }
                                $this->db->where('ip_address', $ip_address);
                        }
                        $this->db->where('time >', time() - $this->config->item(
'lockout_time', 'ion_auth'), FALSE);
                        return $this->db->count_all_results($this->tables['login_attempts']); // Use count_all_results for efficiency
                }
                return 0;
        }

        /**
         * Get the last time a login attempt occurred from given identity
         *
         * @param string      $identity   User's identity
         * @param string|null $ip_address IP address
         *                                Only used if track_login_ip_address is
 set to TRUE.
         *                                If NULL (default value), the current I
P address is used.
         *                                Use get_last_attempt_ip($identity) to
retrieve a user's last IP
         *
         * @return int The time of the last login attempt for a given IP-address
 or identity
         */
        public function get_last_attempt_time($identity, $ip_address = NULL)
        {
                if ($this->config->item('track_login_attempts', 'ion_auth'))
                {
                        $this->db->select('time');
                        $this->db->where('login', $identity);
                        if ($this->config->item('track_login_ip_address', 'ion_a
uth'))
                        {
                                if (!isset($ip_address))
                                {
                                        $ip_address = $this->input->ip_address()
;
                                }
                                $this->db->where('ip_address', $ip_address);
                        }
                        $this->db->order_by('id', 'desc');
                        $qres = $this->db->get($this->tables['login_attempts'],
1);

                        if ($qres->num_rows() > 0)
                        {
                                return $qres->row()->time;
                        }
                }

                return 0;
        }

        /**
         * Get the IP address of the last time a login attempt occurred from giv
en identity
         *
         * @param string $identity User's identity
         *
         * @return string
         */
        public function get_last_attempt_ip($identity)
        {
                if ($this->config->item('track_login_attempts', 'ion_auth') && $
this->config->item('track_login_ip_address', 'ion_auth'))
                {
                        $this->db->select('ip_address');
                        $this->db->where('login', $identity);
                        $this->db->order_by('id', 'desc');
                        $qres = $this->db->get($this->tables['login_attempts'],
1);

                        if ($qres->num_rows() > 0)
                        {
                                return $qres->row()->ip_address;
                        }
                }

                return '';
        }

        /**
         * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/i
lkon/Tank-Auth)
         *
         * Note: the current IP address will be used if track_login_ip_address c
onfig value is TRUE
         *
         * @param string $identity User's identity
         *
         * @return bool
         */
        public function increase_login_attempts($identity)
        {
                if ($this->config->item('track_login_attempts', 'ion_auth'))
                {
                        $data = ['ip_address' => '', 'login' => $identity, 'time
' => time()];
                        if ($this->config->item('track_login_ip_address', 'ion_a
uth'))
                        {
                                $current_ip = $this->input->ip_address();
                                // Ensure IP address is not null before inserting.
                                $data['ip_address'] = $current_ip ? $current_ip : '';
                        }
                        return $this->db->insert($this->tables['login_attempts']
, $data);
                }
                return FALSE;
        }

        /**
         * clear_login_attempts
         * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/i
lkon/Tank-Auth)
         *
         * @param string      $identity                   User's identity
         * @param int         $old_attempts_expire_period In seconds, any attemp
ts older than this value will be removed.
         *                                                It is used for regular
ly purging the attempts table.
         *                                                (for security reason,
minimum value is lockout_time config value)
         * @param string|null $ip_address                 IP address
         *                                                Only used if track_log
in_ip_address is set to TRUE.
         *                                                If NULL (default value
), the current IP address is used.
         *                                                Use get_last_attempt_i
p($identity) to retrieve a user's last IP
         *
         * @return bool
         */
        public function clear_login_attempts($identity, $old_attempts_expire_per
iod = 86400, $ip_address = NULL)
        {
                if ($this->config->item('track_login_attempts', 'ion_auth'))
                {
                        // Make sure $old_attempts_expire_period is at least equ
als to lockout_time
                        $lockout_time = $this->config->item('lockout_time', 'ion_auth');
                        $old_attempts_expire_period = max($old_attempts_expire_p
eriod, $lockout_time ? $lockout_time : 600); // Ensure lockout_time has a fallback

                        $this->db->where('login', $identity);
                        if ($this->config->item('track_login_ip_address', 'ion_a
uth'))
                        {
                                if (!isset($ip_address))
                                {
                                        $ip_address = $this->input->ip_address()
;
                                }
                                if ($ip_address) { // Only add IP condition if IP is available
                                    $this->db->where('ip_address', $ip_address);
                                }
                        }
                        // Purge obsolete login attempts
                        $this->db->or_where('time <', time() - $old_attempts_exp
ire_period, FALSE);

                        return $this->db->delete($this->tables['login_attempts']
);
                }
                return FALSE;
        }

        /**
         * @param int $limit
         *
         * @return static
         */
        public function limit($limit)
        {
                $this->trigger_events('limit');
                $this->_ion_limit = $limit;

                return $this;
        }

        /**
         * @param int $offset
         *
         * @return static
         */
        public function offset($offset)
        {
                $this->trigger_events('offset');
                $this->_ion_offset = $offset;

                return $this;
        }

        /**
         * @param array|string $where
         * @param null|string  $value
         *
         * @return static
         */
        public function where($where, $value = NULL)
        {
                $this->trigger_events('where');

                if (!is_array($where))
                {
                        $where = [$where => $value];
                }

                array_push($this->_ion_where, $where);

                return $this;
        }

        /**
         * @param string      $like
         * @param string|null $value
         * @param string      $position
         *
         * @return static
         */
        public function like($like, $value = NULL, $position = 'both')
        {
                $this->trigger_events('like');

                array_push($this->_ion_like, [
                        'like'     => $like,
                        'value'    => $value,
                        'position' => $position
                ]);

                return $this;
        }

        /**
         * @param array|string $select
         *
         * @return static
         */
        public function select($select)
        {
                $this->trigger_events('select');

                $this->_ion_select[] = $select;

                return $this;
        }

        /**
         * @param string $by
         * @param string $order
         *
         * @return static
         */
        public function order_by($by, $order='desc')
        {
                $this->trigger_events('order_by');

                $this->_ion_order_by = $by;
                $this->_ion_order    = $order;

                return $this;
        }

        /**
         * @return object|mixed
         */
        public function row()
        {
                $this->trigger_events('row');

                $row = $this->response->row();
                $this->_clear_query_vars(); // Clear for next query
                return $row;
        }

        /**
         * @return array|mixed
         */
        public function row_array()
        {
                $this->trigger_events(['row', 'row_array']);

                $row = $this->response->row_array();
                $this->_clear_query_vars(); // Clear for next query
                return $row;
        }

        /**
         * @return mixed
         */
        public function result()
        {
                $this->trigger_events('result');

                $result = $this->response->result();
                $this->_clear_query_vars(); // Clear for next query
                return $result;
        }

        /**
         * @return array|mixed
         */
        public function result_array()
        {
                $this->trigger_events(['result', 'result_array']);

                $result = $this->response->result_array();
                $this->_clear_query_vars(); // Clear for next query
                return $result;
        }

        /**
         * @return int
         */
        public function num_rows()
        {
                $this->trigger_events(['num_rows']);

                $result = $this->response->num_rows();
                $this->_clear_query_vars(); // Clear for next query (though num_rows usually means end of query chain)
                return $result;
        }

        /**
         * users
         *
         * @param array|null $groups
         *
         * @return static
         * @author Ben Edmunds
         */
        public function users($groups = NULL)
        {
                $this->trigger_events('users');

                if (isset($this->_ion_select) && !empty($this->_ion_select))
                {
                        foreach ($this->_ion_select as $select)
                        {
                                $this->db->select($select);
                        }

                        // $this->_ion_select = []; // Clearing moved to _clear_query_vars
                }
                else
                {
                        // default selects
                        $this->db->select([
                            $this->tables['users'].'.*',
                            $this->tables['users'].'.id as id', // Alias for consistency if needed
                            $this->tables['users'].'.id as user_id' // Alias for consistency
                        ]);
                }

                // filter by group id(s) if passed
                if (isset($groups))
                {
                        // build an array if only one group was passed
                        if (!is_array($groups))
                        {
                                $groups = [$groups];
                        }

                        // join and then run a where_in against the group ids
                        if (isset($groups) && !empty($groups))
                        {
                                $this->db->distinct();
                                $this->db->join(
                                    $this->tables['users_groups'],
                                    $this->tables['users_groups'].'.'.$this->joi
n['users'].'='.$this->tables['users'].'.id',
                                    'inner'
                                );
                        }

                        // verify if group name or group id was used and create
and put elements in different arrays
                        $group_ids = [];
                        $group_names = [];
                        foreach($groups as $group)
                        {
                                if(is_numeric($group)) $group_ids[] = (int) $group; // Cast to int
                                else $group_names[] = $group;
                        }
                        $or_where_in = (!empty($group_ids) && !empty($group_name
s)) ? 'or_where_in' : 'where_in';
                        // if group name was used we do one more join with group
s
                        if(!empty($group_names))
                        {
                                $this->db->join($this->tables['groups'], $this->
tables['users_groups'] . '.' . $this->join['groups'] . ' = ' . $this->tables['gr
oups'] . '.id', 'inner');
                                $this->db->where_in($this->tables['groups'] . '.
name', $group_names);
                        }
                        if(!empty($group_ids))
                        {
                                $this->db->{$or_where_in}($this->tables['users_g
roups'].'.'.$this->join['groups'], $group_ids);
                        }
                }

                $this->trigger_events('extra_where');

                // run each where that was passed
                if (isset($this->_ion_where) && !empty($this->_ion_where))
                {
                        foreach ($this->_ion_where as $where)
                        {
                                $this->db->where($where);
                        }

                        // $this->_ion_where = []; // Clearing moved
                }

                if (isset($this->_ion_like) && !empty($this->_ion_like))
                {
                        foreach ($this->_ion_like as $like)
                        {
                                $this->db->or_like($like['like'], $like['value']
, $like['position']);
                        }

                        // $this->_ion_like = []; // Clearing moved
                }

                if (isset($this->_ion_limit) && isset($this->_ion_offset))
                {
                        $this->db->limit($this->_ion_limit, $this->_ion_offset);

                        // $this->_ion_limit  = NULL; // Clearing moved
                        // $this->_ion_offset = NULL; // Clearing moved
                }
                else if (isset($this->_ion_limit))
                {
                        $this->db->limit($this->_ion_limit);

                        // $this->_ion_limit  = NULL; // Clearing moved
                }

                // set the order
                if (isset($this->_ion_order_by) && isset($this->_ion_order))
                {
                        $this->db->order_by($this->_ion_order_by, $this->_ion_or
der);

                        // $this->_ion_order    = NULL; // Clearing moved
                        // $this->_ion_order_by = NULL; // Clearing moved
                }

                $this->response = $this->db->get($this->tables['users']);
                // Note: Not calling _clear_query_vars() here, as it's usually called by row(), result() etc.
                return $this;
        }

        /**
         * user
         *
         * @param int|string|null $id
         *
         * @return static
         * @author Ben Edmunds
         */
        public function user($id = NULL)
        {
                $this->trigger_events('user');

                // if no id was passed use the current users id
                $id = isset($id) ? $id : $this->session->userdata('user_id');

                $this->limit(1);
                $this->order_by($this->tables['users'].'.id', 'desc'); // Order by id desc is not strictly necessary if id is primary and unique
                $this->where($this->tables['users'].'.id', $id);

                $this->users(); // This sets $this->response

                return $this; // Return $this to allow chaining to row(), result() etc.
        }

        /**
         * get_users_groups
         *
         * @param int|string|bool $id
         *
         * @return CI_DB_result
         * @author Ben Edmunds
         */
        public function get_users_groups($id = FALSE)
        {
                $this->trigger_events('get_users_groups'); // Corrected event name

                // if no id was passed use the current users id
                $id || $id = $this->session->userdata('user_id');

                return $this->db->select($this->tables['users_groups'].'.'.$this
->join['groups'].' as id, '.$this->tables['groups'].'.name, '.$this->tables['gro
ups'].'.description')
                                ->where($this->tables['users_groups'].'.'.$this-
>join['users'], $id)
                                ->join($this->tables['groups'], $this->tables['u
sers_groups'].'.'.$this->join['groups'].'='.$this->tables['groups'].'.id') // No type casting for join needed here usually
                                ->get($this->tables['users_groups']);
        }

        /**
         * @param int|string|array $check_group group(s) to check
         * @param int|string|bool  $id          user id
         * @param bool             $check_all   check if all groups is present,
or any of the groups
         *
         * @return bool Whether the/all user(s) with the given ID(s) is/are in t
he given group
         * @author Phil Sturgeon
         **/
        public function in_group($check_group, $id = FALSE, $check_all = FALSE)
        {
                $this->trigger_events('in_group');

                $id || $id = $this->session->userdata('user_id');

                if (empty($id)) { // If no user ID, cannot check group
                    return FALSE;
                }

                if (!is_array($check_group))
                {
                        $check_group = [$check_group];
                }

                if (isset($this->_cache_user_in_group[$id]))
                {
                        $groups_array = $this->_cache_user_in_group[$id];
                }
                else
                {
                        $users_groups = $this->get_users_groups($id)->result();
                        $groups_array = [];
                        foreach ($users_groups as $group)
                        {
                                $groups_array[$group->id] = $group->name;
                        }
                        $this->_cache_user_in_group[$id] = $groups_array;
                }
                // If groups_array is empty, user is in no groups
                if (empty($groups_array) && !empty($check_group)) {
                    return FALSE;
                }


                foreach ($check_group as $key => $value)
                {
                        $groups = (is_numeric($value)) ? array_keys($groups_arra
y) : $groups_array;

                        /**
                         * if !all (default), in_array
                         * if all, !in_array
                         */
                        if (in_array($value, $groups) xor $check_all)
                        {
                                /**
                                 * if !all (default), true
                                 * if all, false
                                 */
                                return !$check_all;
                        }
                }

                /**
                 * if !all (default), false
                 * if all, true
                 */
                return $check_all;
        }

        /**
         * add_to_group
         *
         * @param array|int|float|string $group_ids
         * @param bool|int|float|string  $user_id
         *
         * @return int The number of groups added
         * @author Ben Edmunds
         */
        public function add_to_group($group_ids, $user_id = FALSE)
        {
                $this->trigger_events('add_to_group');

                // if no id was passed use the current users id
                $user_id || $user_id = $this->session->userdata('user_id');

                if (empty($user_id)) { // Cannot add if no user_id
                    return 0;
                }

                if(!is_array($group_ids))
                {
                        $group_ids = [$group_ids];
                }

                $return = 0;

                // Then insert each into the database
                foreach ($group_ids as $group_id)
                {
                        // Cast to float to support bigint data type - int is usually sufficient
                        if ($this->db->insert($this->tables['users_groups'],
                                                                  [ $this->join[
'groups'] => (int)$group_id, // Use int
                                                                        $this->j
oin['users']  => (int)$user_id  ])) // Use int
                        {
                                if (isset($this->_cache_groups[$group_id]))
                                {
                                        $group_name = $this->_cache_groups[$grou
p_id];
                                }
                                else
                                {
                                        $group_query = $this->group($group_id)->result(); // Use group_query
                                        if ($group_query && isset($group_query[0])) { // Check if group exists
                                            $group_name = $group_query[0]->name;
                                            $this->_cache_groups[$group_id] = $group_name;
                                        } else {
                                            $group_name = NULL; // Group not found
                                        }
                                }
                                if ($group_name) { // Add to cache only if group name was found
                                    $this->_cache_user_in_group[$user_id][$group_id] = $group_name;
                                }


                                // Return the number of groups added
                                $return++;
                        }
                }

                return $return;
        }

        /**
         * remove_from_group
         *
         * @param array|int|float|string|bool $group_ids
         * @param int|float|string|bool $user_id
         *
         * @return bool
         * @author Ben Edmunds
         */
        public function remove_from_group($group_ids = FALSE, $user_id = FALSE)
        {
                $this->trigger_events('remove_from_group');

                // user id is required
                if (empty($user_id))
                {
                        return FALSE;
                }

                // if group id(s) are passed remove user from the group(s)
                if (!empty($group_ids))
                {
                        if (!is_array($group_ids))
                        {
                                $group_ids = [$group_ids];
                        }

                        foreach ($group_ids as $group_id)
                        {
                                // Cast to float to support bigint data type - int is usually sufficient
                                $this->db->delete(
                                        $this->tables['users_groups'],
                                        [$this->join['groups'] => (int)$group_id, $this->join['users'] => (int)$user_id] // Use int
                                );
                                if (isset($this->_cache_user_in_group[$user_id])
 && isset($this->_cache_user_in_group[$user_id][$group_id]))
                                {
                                        unset($this->_cache_user_in_group[$user_
id][$group_id]);
                                }
                        }

                        $return = TRUE; // Assume success even if some deletions didn't affect rows
                }
                // otherwise remove user from all groups
                else
                {
                        // Cast to float to support bigint data type - int is usually sufficient
                        if ($this->db->delete($this->tables['users_groups'], [$this->join['users'] => (int)$user_id])) // Use int
                        {
                                $this->_cache_user_in_group[$user_id] = [];
                                $return = TRUE;
                        } else {
                                $return = FALSE;
                        }
                }
                return $return;
        }

        /**
         * groups
         *
         * @return static
         * @author Ben Edmunds
         */
        public function groups()
        {
                $this->trigger_events('groups');

                // run each where that was passed
                if (isset($this->_ion_where) && !empty($this->_ion_where))
                {
                        foreach ($this->_ion_where as $where)
                        {
                                $this->db->where($where);
                        }
                        // $this->_ion_where = []; // Clearing moved to _clear_query_vars
                }

                if (isset($this->_ion_limit) && isset($this->_ion_offset))
                {
                        $this->db->limit($this->_ion_limit, $this->_ion_offset);
                        // Clearing moved
                }
                else if (isset($this->_ion_limit))
                {
                        $this->db->limit($this->_ion_limit);
                        // Clearing moved
                }

                // set the order
                if (isset($this->_ion_order_by) && isset($this->_ion_order))
                {
                        $this->db->order_by($this->_ion_order_by, $this->_ion_or
der);
                        // Clearing moved
                }

                $this->response = $this->db->get($this->tables['groups']);
                // Not clearing query vars here, done by result(), row() etc.
                return $this;
        }

        /**
         * group
         *
         * @param int|string|null $id
         *
         * @return static
         * @author Ben Edmunds
         */
        public function group($id = NULL)
        {
                $this->trigger_events('group');

                if (isset($id))
                {
                        $this->where($this->tables['groups'].'.id', $id);
                }

                $this->limit(1);
                $this->order_by('id', 'desc'); // Not strictly necessary

                return $this->groups();
        }

        /**
         * update
         *
         * @param int|string $id
         * @param array      $data
         *
         * @return bool
         * @author Phil Sturgeon
         */
        public function update($id, array $data)
        {
                $this->trigger_events('pre_update_user');

                $user = $this->user($id)->row();
                if (!$user) { // User not found
                    $this->set_error('update_unsuccessful_user_not_found');
                    $this->trigger_events(['post_update_user', 'post_update_user_unsuccessful']);
                    return FALSE;
                }


                $this->db->trans_begin();

                if (array_key_exists($this->identity_column, $data) && $this->id
entity_check($data[$this->identity_column]) && $user->{$this->identity_column} !
== $data[$this->identity_column])
                {
                        $this->db->trans_rollback();
                        $this->set_error('account_update_duplicate_identity'); // More specific error

                        $this->trigger_events(['post_update_user', 'post_update_
user_unsuccessful']);
                        // $this->set_error('update_unsuccessful'); // Already set by duplicate identity

                        return FALSE;
                }
                // Check for email duplicate if identity is not email and email is being changed
                if ($this->identity_column !== 'email' && array_key_exists('email', $data) && $this->email_check($data['email']) && $user->email !== $data['email'])
                {
                    $this->db->trans_rollback();
                    $this->set_error('account_update_duplicate_email');
                    $this->trigger_events(['post_update_user', 'post_update_user_unsuccessful']);
                    return FALSE;
                }


                // Filter the data passed
                $filtered_data = $this->_filter_data($this->tables['users'], $data); // Use a different var name

                if (array_key_exists('password', $filtered_data)) // Check filtered_data
                {
                        if( ! empty($filtered_data['password']))
                        {
                                // $user already fetched
                                $filtered_data['password'] = $this->hash_password($filtered_data['password']); // No identity for hash_password
                                if ($filtered_data['password'] === FALSE)
                                {
                                        $this->db->trans_rollback();
                                        $this->trigger_events(['post_update_user', 'post_update_user_unsuccessful']);
                                        $this->set_error('update_unsuccessful_password_hash_failed'); // Specific error
                                        return FALSE;
                                }
                                // Password changed, clear tokens
                                $filtered_data['remember_code'] = NULL;
                                $filtered_data['remember_selector'] = NULL;
                                $filtered_data['forgotten_password_code'] = NULL;
                                $filtered_data['forgotten_password_selector'] = NULL;
                                $filtered_data['forgotten_password_time'] = NULL;

                        }
                        else
                        {
                                // unset password so it doesn't effect database entry if no password passed
                                unset($filtered_data['password']);
                        }
                }

                $this->trigger_events('extra_where');
                $this->trigger_events('extra_set'); // For update data modification
                $this->db->update($this->tables['users'], $filtered_data, ['id' => $id]);

                if ($this->db->trans_status() === FALSE)
                {
                        $this->db->trans_rollback();

                        $this->trigger_events(['post_update_user', 'post_update_
user_unsuccessful']);
                        $this->set_error('update_unsuccessful');
                        return FALSE;
                }

                $this->db->trans_commit();

                $this->trigger_events(['post_update_user', 'post_update_user_suc
cessful']);
                $this->set_message('update_successful');
                return TRUE;
        }

        /**
         * delete_user
         *
         * @param int|string $id
         *
         * @return bool
         * @author Phil Sturgeon
         */
        public function delete_user($id)
        {
                $this->trigger_events('pre_delete_user');

                // Check if user is trying to delete themselves
                if ($this->session->userdata('user_id') == $id) {
                    $this->trigger_events(['post_delete_user', 'post_delete_user_unsuccessful']);
                    $this->set_error('delete_current_user_unsuccessful');
                    return FALSE;
                }


                $this->db->trans_begin();

                // remove user from groups
                $this->remove_from_group(NULL, $id);

                // delete user from users table should be placed after remove fr
om group
                $this->db->delete($this->tables['users'], ['id' => $id]);

                // Delete login attempts
                $this->db->delete($this->tables['login_attempts'], [$this->identity_column => $id]);


                if ($this->db->trans_status() === FALSE)
                {
                        $this->db->trans_rollback();
                        $this->trigger_events(['post_delete_user', 'post_delete_
user_unsuccessful']);
                        $this->set_error('delete_unsuccessful');
                        return FALSE;
                }

                $this->db->trans_commit();

                $this->trigger_events(['post_delete_user', 'post_delete_user_suc
cessful']);
                $this->set_message('delete_successful');
                return TRUE;
        }

        /**
         * update_last_login
         *
         * @param int|string $id
         *
         * @return bool
         * @author Ben Edmunds
         */
        public function update_last_login($id)
        {
                $this->trigger_events('update_last_login');

                // $this->load->helper('date'); // Already loaded in constructor

                $this->trigger_events('extra_where');

                $this->db->update($this->tables['users'], ['last_login' => time(
)], ['id' => $id]);

                return $this->db->affected_rows() == 1;
        }

        /**
         * set_lang
         *
         * @param string $lang
         *
         * @return bool
         * @author Ben Edmunds
         */
        public function set_lang($lang = 'en')
        {
                $this->trigger_events('set_lang');

                // if the user_expire is set to zero we'll set the expiration tw
o years from now.
                if($this->config->item('user_expire', 'ion_auth') === 0)
                {
                        $expire = self::MAX_COOKIE_LIFETIME;
                }
                // otherwise use what is set
                else
                {
                        $expire = $this->config->item('user_expire', 'ion_auth')
;
                }

                set_cookie([
                        'name'   => 'lang_code', // Should this be $this->config->item('cookie_prefix', 'ion_auth').'lang_code' ?
                        'value'  => $lang,
                        'expire' => $expire,
                        'path'   => $this->config->item('cookie_path', 'ion_auth'),
                        'domain' => $this->config->item('cookie_domain', 'ion_auth'),
                        'secure' => $this->config->item('cookie_secure', 'ion_auth'),
                        'httponly' => $this->config->item('cookie_httponly', 'ion_auth'), // Added httponly from CI config
                ]);

                return TRUE;
        }

        /**
         * set_session
         *
         * @param object $user
         *
         * @return bool
         * @author jrmadsen67
         */
        public function set_session($user)
        {
                $this->trigger_events('pre_set_session');

                $session_data = [
                    'identity'                 => $user->{$this->identity_column}, // This is the value of the identity column
                    $this->identity_column     => $user->{$this->identity_column}, // Use configured identity column as key
                    'email'                    => $user->email,
                    'user_id'                  => $user->id, //everyone likes to overwrite id so we'll use user_id
                    'old_last_login'           => $user->last_login,
                    'last_check'               => time(),
                    'ion_auth_session_hash'    => $this->config->item('session_h
ash', 'ion_auth'),
                ];

                $this->session->set_userdata($session_data);

                $this->trigger_events('post_set_session');

                return TRUE;
        }

        /**
         * Set a user to be remembered
         *
         * Implemented as described in
         * https://paragonie.com/blog/2015/04/secure-authentication-php-with-lon
g-term-persistence
         *
         * @param string $identity
         *
         * @return bool
         * @author Ben Edmunds
         */
        public function remember_user($identity)
        {
                $this->trigger_events('pre_remember_user');

                if (!$identity)
                {
                        return FALSE;
                }

                // Generate random tokens
                $token = $this->_generate_selector_validator_couple();

                if ($token->validator_hashed)
                {
                        $this->trigger_events('extra_where'); // For remember_user updates
                        $this->db->update($this->tables['users'],
                                                                [ 'remember_sele
ctor' => $token->selector,
                                                                  'remember_code
' => $token->validator_hashed ], // Store hashed validator
                                                                [ $this->identit
y_column => $identity ]);

                        if ($this->db->affected_rows() > -1) // Can be 0 if code is the same
                        {
                                // if the user_expire is set to zero we'll set t
he expiration two years from now.
                                if($this->config->item('user_expire', 'ion_auth'
) === 0)
                                {
                                        $expire = self::MAX_COOKIE_LIFETIME;
                                }
                                // otherwise use what is set
                                else
                                {
                                        $expire = $this->config->item('user_expi
re', 'ion_auth');
                                }

                                set_cookie([
                                        'name'   => $this->config->item('remembe
r_cookie_name', 'ion_auth'),
                                        'value'  => $token->user_code, // selector.validator
                                        'expire' => $expire,
                                        'path'   => $this->config->item('cookie_path', 'ion_auth'), // Use CI config for cookies
                                        'domain' => $this->config->item('cookie_domain', 'ion_auth'),
                                        'secure' => $this->config->item('cookie_secure', 'ion_auth'),
                                        'httponly' => TRUE, // Remember me cookie should be HttpOnly
                                ]);

                                $this->trigger_events(['post_remember_user', 're
member_user_successful']);
                                return TRUE;
                        }
                }

                $this->trigger_events(['post_remember_user', 'remember_user_unsu
ccessful']);
                return FALSE;
        }

        /**
         * Login automatically a user with the "Remember me" feature
         * Implemented as described in
         * https://paragonie.com/blog/2015/04/secure-authentication-php-with-lon
g-term-persistence
         *
         * @return bool
         * @author Ben Edmunds
         */
        public function login_remembered_user()
        {
                $this->trigger_events('pre_login_remembered_user');

                // Retrieve token from cookie
                $remember_cookie = get_cookie($this->config->item('remember_cook
ie_name', 'ion_auth'));
                $token = $this->_retrieve_selector_validator_couple($remember_co
okie);

                if ($token === FALSE)
                {
                        $this->trigger_events(['post_login_remembered_user', 'po
st_login_remembered_user_unsuccessful']);
                        delete_cookie($this->config->item('remember_cookie_name', 'ion_auth')); // Delete invalid cookie
                        return FALSE;
                }

                // get the user with the selector
                $this->trigger_events('extra_where');
                $query = $this->db->select($this->identity_column . ', id, email, password, active, remember_code, last_login') // Added password and active for full login process
                                                  ->where('remember_selector', $
token->selector)
                                                  ->where('active', 1) // Ensure user is active
                                                  ->limit(1)
                                                  ->get($this->tables['users']);

                // Check that we got the user
                if ($query->num_rows() === 1)
                {
                        // Retrieve the information
                        $user = $query->row();

                        // Check the code against the validator
                        $identity = $user->{$this->identity_column};
                        // For remember_code, the DB stores the hashed validator. $token->validator is the plain one.
                        if ($this->verify_password($token->validator, $user->rem
ember_code)) // No identity needed for verify_password with bcrypt/argon2
                        {
                                $this->update_last_login($user->id);

                                $this->set_session($user);

                                $this->clear_forgotten_password_code($identity); // Clear forgotten password on successful login

                                // extend the users cookies if the option is ena
bled
                                if ($this->config->item('user_extend_on_login',
'ion_auth'))
                                {
                                        $this->remember_user($identity); // This will generate new tokens and set new cookie
                                }

                                // Regenerate the session (for security purpose:
 to avoid session fixation)
                                if (empty($this->session->userdata('ion_auth_session_hash')) || $this->session->userdata('ion_auth_session_hash') !== $this->config->item('session_hash', 'ion_auth')) {
                                     $this->session->sess_regenerate(FALSE);
                                }


                                $this->trigger_events(['post_login_remembered_us
er', 'post_login_remembered_user_successful']);
                                return TRUE;
                        }
                }
                // If token is invalid or user not found, clear the cookie
                delete_cookie($this->config->item('remember_cookie_name', 'ion_a
uth'));

                $this->trigger_events(['post_login_remembered_user', 'post_login
_remembered_user_unsuccessful']);
                return FALSE;
        }


        /**
         * create_group
         *
         * @param string|bool $group_name
         * @param string      $group_description
         * @param array       $additional_data
         *
         * @return int|bool The ID of the inserted group, or FALSE on failure
         * @author aditya menon
         */
        public function create_group($group_name = FALSE, $group_description = '
', $additional_data = [])
        {
                // bail if the group name was not passed
                if(!$group_name)
                {
                        $this->set_error('group_name_required');
                        return FALSE;
                }

                // bail if the group name already exists
                $existing_group = $this->db->get_where($this->tables['groups'],
['name' => $group_name])->num_rows();
                if($existing_group !== 0)
                {
                        $this->set_error('group_already_exists');
                        return FALSE;
                }

                $data = ['name'=>$group_name,'description'=>$group_description];

                // filter out any data passed that doesnt have a matching column
 in the groups table
                // and merge the set group data and the additional data
                if (!empty($additional_data)) $data = array_merge($this->_filter
_data($this->tables['groups'], $additional_data), $data);

                $this->trigger_events('extra_group_set');

                // insert the new group
                $this->db->insert($this->tables['groups'], $data);
                $group_id = $this->db->insert_id($this->tables['groups'] . '_id_
seq'); // For PostgreSQL

                if (!$group_id) { // Check if insert was successful
                    $this->set_error('group_creation_unsuccessful');
                    return FALSE;
                }

                // report success
                $this->set_message('group_creation_successful');
                // return the brand new group id
                return $group_id;
        }

        /**
         * update_group
         *
         * @param int|string|bool $group_id
         * @param string|bool     $group_name
         * @param array    $additional_data including 'description'
         *
         * @return bool
         * @author aditya menon
         */
        public function update_group($group_id = FALSE, $group_name = FALSE, $ad
ditional_data = []) // additional_data can include description
        {
                if (empty($group_id))
                {
                        return FALSE;
                }

                $data = []; // Initialize data array

                // If description is in additional_data, move it to $data
                if (isset($additional_data['description'])) {
                    $data['description'] = $additional_data['description'];
                    unset($additional_data['description']); // Remove from additional_data to avoid conflict
                }


                if (!empty($group_name))
                {
                        // we are changing the name, so do some checks

                        // bail if the group name already exists for a different group
                        $existing_group = $this->db->get_where($this->tables['gr
oups'], ['name' => $group_name])->row();
                        if (isset($existing_group->id) && $existing_group->id !=
 $group_id)
                        {
                                $this->set_error('group_already_exists');
                                return FALSE;
                        }

                        $data['name'] = $group_name;
                }

                // restrict change of name of the admin group
                $group = $this->db->get_where($this->tables['groups'], ['id' =>
$group_id])->row();

                if (!$group) { // Group not found
                    $this->set_error('group_update_unsuccessful_not_found');
                    return FALSE;
                }

                if ($this->config->item('admin_group', 'ion_auth') === $group->n
ame && isset($data['name']) && $data['name'] !== $group->name) // Check if name is actually being changed
                {
                        $this->set_error('group_name_admin_not_alter');
                        return FALSE;
                }

                // filter out any data passed that doesnt have a matching column
 in the groups table
                // and merge the set group data and the additional data
                if (!empty($additional_data))
                {
                        $data = array_merge($this->_filter_data($this->tables['g
roups'], $additional_data), $data);
                }

                if (empty($data)) { // No actual data to update
                    $this->set_message('group_update_no_data'); // Or some other relevant message
                    return TRUE; // Or FALSE if this is considered an error
                }

                $this->trigger_events('extra_group_set'); // For update data modification
                $this->db->update($this->tables['groups'], $data, ['id' => $grou
p_id]);

                if ($this->db->affected_rows() >= 0) { // 0 affected rows is ok if data is same
                    $this->set_message('group_update_successful');
                    return TRUE;
                }

                $this->set_error('group_update_unsuccessful'); // Should not happen if group exists and data is valid
                return FALSE;

        }

        /**
         * delete_group
         *
         * @param int|string|bool $group_id
         *
         * @return bool
         * @author aditya menon
         */
        public function delete_group($group_id = FALSE)
        {
                // bail if mandatory param not set
                if(!$group_id || empty($group_id))
                {
                        $this->set_error('group_delete_missing_id'); // More specific error
                        return FALSE;
                }
                $group = $this->group($group_id)->row();

                if (!$group) { // Group not found
                    $this->trigger_events(['post_delete_group', 'post_delete_group_unsuccessful']);
                    $this->set_error('group_delete_unsuccessful_not_found');
                    return FALSE;
                }


                if($group->name == $this->config->item('admin_group', 'ion_auth'
))
                {
                        $this->trigger_events(['post_delete_group', 'post_delete
_group_notallowed']);
                        $this->set_error('group_delete_notallowed');
                        return FALSE;
                }

                $this->trigger_events('pre_delete_group');

                $this->db->trans_begin();

                // remove all users from this group
                $this->db->delete($this->tables['users_groups'], [$this->join['g
roups'] => $group_id]);
                // remove the group itself
                $this->db->delete($this->tables['groups'], ['id' => $group_id]);

                if ($this->db->trans_status() === FALSE)
                {
                        $this->db->trans_rollback();
                        $this->trigger_events(['post_delete_group', 'post_delete
_group_unsuccessful']);
                        $this->set_error('group_delete_unsuccessful');
                        return FALSE;
                }

                $this->db->trans_commit();

                $this->trigger_events(['post_delete_group', 'post_delete_group_s
uccessful']);
                $this->set_message('group_delete_successful');
                return TRUE;
        }

        /**
         * @param string $event
         * @param string $name
         * @param string $class
         * @param string $method
         * @param array $arguments
         */
        public function set_hook($event, $name, $class, $method, $arguments)
        {
                $this->_ion_hooks->{$event}[$name] = new stdClass;
                $this->_ion_hooks->{$event}[$name]->class     = $class;
                $this->_ion_hooks->{$event}[$name]->method    = $method;
                $this->_ion_hooks->{$event}[$name]->arguments = $arguments;
        }

        /**
         * @param string $event
         * @param string $name
         */
        public function remove_hook($event, $name)
        {
                if (isset($this->_ion_hooks->{$event}[$name]))
                {
                        unset($this->_ion_hooks->{$event}[$name]);
                }
        }

        /**
         * @param string $event
         */
        public function remove_hooks($event)
        {
                if (isset($this->_ion_hooks->$event))
                {
                        unset($this->_ion_hooks->$event);
                }
        }

        /**
         * @param string $event
         * @param string $name
         *
         * @return bool|mixed
         */
        protected function _call_hook($event, $name)
        {
                if (isset($this->_ion_hooks->{$event}[$name]) &&
                    is_object($this->_ion_hooks->{$event}[$name]) && // Ensure it's an object
                    property_exists($this->_ion_hooks->{$event}[$name], 'class') && // Ensure properties exist
                    property_exists($this->_ion_hooks->{$event}[$name], 'method') &&
                    method_exists($this->_ion_hooks->{$event}[$name]->class, $this->_ion_hooks->{$event}[$name]->method))
                {
                        $hook = $this->_ion_hooks->{$event}[$name];
                        $class_instance = is_string($hook->class) ? new $hook->class : $hook->class; // Instantiate if string

                        return call_user_func_array([$class_instance, $hook->method], (array)$hook->arguments); // Ensure args is array
                }

                return FALSE;
        }

        /**
         * @param string|array $events
         */
        public function trigger_events($events)
        {
                if (is_array($events) && !empty($events))
                {
                        foreach ($events as $event)
                        {
                                $this->trigger_events($event);
                        }
                }
                else
                {
                        if (isset($this->_ion_hooks->$events) && !empty($this->_
ion_hooks->$events))
                        {
                                foreach ($this->_ion_hooks->$events as $name =>
$hook)
                                {
                                        $this->_call_hook($events, $name);
                                }
                        }
                }
        }

        /**
         * set_message_delimiters
         *
         * Set the message delimiters
         *
         * @param string $start_delimiter
         * @param string $end_delimiter
         *
         * @return true
         * @author Ben Edmunds
         */
        public function set_message_delimiters($start_delimiter, $end_delimiter)
        {
                $this->message_start_delimiter = $start_delimiter;
                $this->message_end_delimiter   = $end_delimiter;
                // Also update session flashdata delimiters if they are used by Ion Auth messages
                // $this->session->set_flashdata('message_start_delimiter', $start_delimiter);
                // $this->session->set_flashdata('message_end_delimiter', $end_delimiter);
                return TRUE;
        }

        /**
         * set_error_delimiters
         *
         * Set the error delimiters
         *
         * @param string $start_delimiter
         * @param string $end_delimiter
         *
         * @return true
         * @author Ben Edmunds
         */
        public function set_error_delimiters($start_delimiter, $end_delimiter)
        {
                $this->error_start_delimiter = $start_delimiter;
                $this->error_end_delimiter   = $end_delimiter;
                // Also update session flashdata delimiters for errors
                // $this->session->set_flashdata('error_start_delimiter', $start_delimiter);
                // $this->session->set_flashdata('error_end_delimiter', $end_delimiter);
                return TRUE;
        }

        /**
         * set_message
         *
         * Set a message
         *
         * @param string $message The message (already langified or the lang key)
         *
         * @return string The given message
         * @author Ben Edmunds
         */
        public function set_message($message)
        {
                // Check if the message is a lang key
                $lang_message = $this->lang->line($message);
                $this->messages[] = ($lang_message) ? $lang_message : $message; // Store the langified message or the message itself

                return $message; // Return original key/message for consistency or further use
        }

        /**
         * messages
         *
         * Get the messages
         *
         * @return string
         * @author Ben Edmunds
         */
        public function messages()
        {
                $_output = '';
                foreach ($this->messages as $message)
                {
                        // Messages are already langified by set_message
                        // $messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
                        $_output .= $this->message_start_delimiter . $message . $this->message_end_delimiter;
                }
                $this->clear_messages(); // Clear messages after retrieving them
                return $_output;
        }

        /**
         * messages as array
         *
         * Get the messages as an array
         *
         * @param bool $langify Deprecated, messages are now pre-langified by set_message
         *
         * @return array
         * @author Raul Baldner Junior
         */
        public function messages_array($langify = TRUE) // Langify param is now less relevant
        {
            $output_array = [];
            foreach ($this->messages as $message) {
                $output_array[] = $this->message_start_delimiter . $message . $this->message_end_delimiter;
            }
            $this->clear_messages(); // Clear messages after retrieving
            return $output_array;
        }

        /**
         * clear_messages
         *
         * Clear messages
         *
         * @return true
         * @author Ben Edmunds
         */
        public function clear_messages()
        {
                $this->messages = [];

                return TRUE;
        }

        /**
         * set_error
         *
         * Set an error message
         *
         * @param string $error The error (already langified or the lang key)
         *
         * @return string The given error
         * @author Ben Edmunds
         */
        public function set_error($error)
        {
                // Check if the error is a lang key
                $lang_error = $this->lang->line($error);
                $this->errors[] = ($lang_error) ? $lang_error : $error; // Store the langified error or the error itself


                return $error; // Return original key/error
        }

        /**
         * errors
         *
         * Get the error message
         *
         * @return string
         * @author Ben Edmunds
         */
        public function errors()
        {
                $_output = '';
                foreach ($this->errors as $error)
                {
                        // Errors are already langified by set_error
                        // $errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
                        $_output .= $this->error_start_delimiter . $error . $this->error_end_delimiter;
                }
                $this->clear_errors(); // Clear errors after retrieving
                return $_output;
        }

        /**
         * errors as array
         *
         * Get the error messages as an array
         *
         * @param bool $langify Deprecated, errors are now pre-langified by set_error
         *
         * @return array
         * @author Raul Baldner Junior
         */
        public function errors_array($langify = TRUE) // Langify param is now less relevant
        {
            $output_array = [];
            foreach ($this->errors as $error) {
                $output_array[] = $this->error_start_delimiter . $error . $this->error_end_delimiter;
            }
            $this->clear_errors(); // Clear errors after retrieving
            return $output_array;
        }

        /**
         * clear_errors
         *
         * Clear Errors
         *
         * @return true
         * @author Ben Edmunds
         */
        public function clear_errors()
        {
                $this->errors = [];

                return TRUE;
        }

        /**
         * Internal function to set a password in the database
         *
         * @param string $identity
         * @param string $password
         *
         * @return bool
         */
        protected function _set_password_db($identity, $password)
        {
                $hash = $this->hash_password($password); // No identity for hash_password

                if ($hash === FALSE)
                {
                        return FALSE;
                }

                // When setting a new password, invalidate any other token
                $data = [
                        'password' => $hash,
                        'remember_code' => NULL,
                        'remember_selector' => NULL, // Also clear selector
                        'forgotten_password_code' => NULL,
                        'forgotten_password_selector' => NULL, // Also clear selector
                        'forgotten_password_time' => NULL,
                        'activation_code' => NULL, // Also clear activation code if user is already active
                        'activation_selector' => NULL,
                ];

                $this->trigger_events('extra_where');

                $this->db->update($this->tables['users'], $data, [$this->identit
y_column => $identity]);

                return $this->db->affected_rows() == 1;
        }

        /**
         * @param string $table
         * @param array  $data
         *
         * @return array
         */
        protected function _filter_data($table, $data)
        {
                $filtered_data = [];
                $columns = $this->db->list_fields($table);

                if (is_array($data))
                {
                        foreach ($columns as $column)
                        {
                                if (array_key_exists($column, $data))
                                        $filtered_data[$column] = $data[$column]
;
                        }
                }

                return $filtered_data;
        }


        /** Generate a random token
         * Inspired from http://php.net/manual/en/function.random-bytes.php#1189
32
         *
         * @param int $result_length
         * @return string|false
         */
        protected function _random_token($result_length = 32)
        {
                if(!isset($result_length) || intval($result_length) <= 8 ){
                        $result_length = 32;
                }

                // Ensure $result_length is even for bin2hex
                if ($result_length % 2 !== 0) {
                    $result_length++;
                }


                // Try random_bytes: PHP 7
                if (function_exists('random_bytes')) {
                        return bin2hex(random_bytes($result_length / 2));
                }

                // Try mcrypt
                if (function_exists('mcrypt_create_iv')) {
                        return bin2hex(mcrypt_create_iv($result_length / 2, MCRY
PT_DEV_URANDOM));
                }

                // Try openssl
                if (function_exists('openssl_random_pseudo_bytes')) {
                        return bin2hex(openssl_random_pseudo_bytes($result_lengt
h / 2));
                }

                // No luck!
                return FALSE;
        }

        /** Retrieve hash parameter according to options
         *
         * @param string $identity Deprecated, identity is no longer used when hashing pas
swords
         *
         * @return array|bool
         */
        protected function _get_hash_parameters($identity = NULL)
        {
                $params = FALSE; // Initialize as false
                switch ($this->hash_method)
                {
                        case 'bcrypt':
                                $params = [
                                        'cost' => $this->config->item('bcrypt_de
fault_cost', 'ion_auth')
                                ];
                                break;

                        case 'argon2':
                        case 'argon2id': // Added argon2id
                                $params = $this->config->item('argon2_default_pa
rams', 'ion_auth');
                                break;

                        default:
                                // Do nothing, params remains FALSE
                }

                return $params;
        }

        /** Retrieve hash algorithm according to options
         *
         * @return int|bool Constant for password_hash or FALSE on error
         */
        protected function _get_hash_algo()
        {
                $algo = FALSE; // Initialize as false
                switch ($this->hash_method)
                {
                        case 'bcrypt':
                                $algo = PASSWORD_BCRYPT;
                                break;

                        case 'argon2':
                                $algo = defined('PASSWORD_ARGON2I') ? PASSWORD_ARGON2I : FALSE; // Check if defined
                                break;

                        case 'argon2id':
                                $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : FALSE; // Check if defined
                                break;

                        default:
                                // Do nothing, algo remains FALSE
                }
                if ($algo === FALSE && $this->hash_method !== 'sha1' && $this->hash_method !== 'md5') { // Don't error for legacy if used
                    log_message('error', 'Ion_auth_model: Invalid hash_method configured or missing required PHP constants for Argon2.');
                }

                return $algo;
        }

        /**
         * Generate a random selector/validator couple
         * This is a user code
         *
         * @param int $selector_size    size of the selector token (recommend min 12 for 96 bits of entropy)
         * @param int $validator_size   size of the validator token (recommend min 32 for 256 bits of entropy)
         *
         * @return object|false
         *                      ->selector                      simple token to
retrieve the user (to store in DB)
         *                      ->validator_hashed      token (hashed) to valida
te the user (to store in DB)
         *                      ->user_code                     code to be used
user-side (in cookie or URL) "selector.validator"
         */
        protected function _generate_selector_validator_couple($selector_size =
40, $validator_size = 128) // Default sizes from IonAuth v3
        {
                // The selector is a simple token to retrieve the user
                $selector = $this->_random_token($selector_size);

                // The validator will strictly validate the user and should be m
ore complex
                $validator = $this->_random_token($validator_size);

                if ($selector === FALSE || $validator === FALSE) {
                    log_message('error', 'Ion_auth_model: Failed to generate random tokens for selector/validator couple.');
                    return FALSE;
                }

                // The validator is hashed for storing in DB (avoid session stea
ling in case of DB leaked)
                $validator_hashed = $this->hash_password($validator); // No identity needed for hash_password

                if ($validator_hashed === FALSE) {
                    log_message('error', 'Ion_auth_model: Failed to hash validator token.');
                    return FALSE;
                }


                // The code to be used user-side
                $user_code = "$selector.$validator";

                return (object) [
                        'selector' => $selector,
                        'validator_hashed' => $validator_hashed,
                        'user_code' => $user_code,
                ];
        }

        /**
         * Retrieve remember cookie info (selector.validator)
         *
         * @param string|null $user_code     A user code of the form "selector.validator"
         *
         * @return object|false
         *                      ->selector              simple token to retrieve
 the user in DB
         *                      ->validator             token to validate the us
er (check against hashed value in DB)
         */
        protected function _retrieve_selector_validator_couple($user_code = NULL) // Allow null for safety
        {
                // Check code
                if ($user_code)
                {
                        $tokens = explode('.', $user_code);

                        // Check tokens
                        if (count($tokens) === 2)
                        {
                                return (object) [
                                        'selector' => $tokens[0],
                                        'validator' => $tokens[1]
                                ];
                        }
                }

                return FALSE;
        }

        /**
         * Handle legacy sha1 password
         *
         * We expect the configuration to still have:
         *              store_salt
         *              salt_length
         *
         * @TODO to be removed in later version
         *
         * @param string        $identity
         * @param string        $password
         * @param string        $hashed_password_db
         *
         * @return bool
         **/
        protected function _password_verify_sha1_legacy($identity, $password, $h
ashed_password_db)
        {
                $this->trigger_events('pre_sha1_password_migration');

                // Ensure legacy config items are available
                $store_salt = $this->config->item('store_salt', 'ion_auth');
                $salt_length = $this->config->item('salt_length', 'ion_auth');


                if ($store_salt)
                {
                        // Salt is store at the side, retrieve it
                        $this->trigger_events('extra_where'); // For SHA1 salt query
                        $query = $this->db->select('salt')
                                                          ->where($this->identit
y_column, $identity)
                                                          ->limit(1)
                                                          ->get($this->tables['u
sers']);

                        if ($query->num_rows() !== 1) // User not found or salt missing
                        {
                                $this->trigger_events(['post_sha1_password_migra
tion', 'post_sha1_password_migration_unsuccessful']);
                                return FALSE;
                        }
                        $salt_db_val = $query->row()->salt; // Renamed var

                        $hashed_password = sha1($password . $salt_db_val);
                }
                else
                {
                        // Salt is stored along with password

                        if (!$salt_length) // salt_length must be configured
                        {
                                $this->trigger_events(['post_sha1_password_migra
tion', 'post_sha1_password_migration_unsuccessful']);
                                log_message('error', 'Ion_auth_model: salt_length is not configured for legacy SHA1 password verification.');
                                return FALSE;
                        }

                        $salt = substr($hashed_password_db, 0, $salt_length);

                        $hashed_password =  $salt . substr(sha1($salt . $passwor
d), 0, -$salt_length); // Corrected: was -$salt_length
                }

                // Now we can compare them using hash_equals for timing attack resistance
                if(hash_equals($hashed_password_db, $hashed_password)) // Order of args matters for hash_equals
                {
                        // Password is good, migrate it to latest hashing method
                        $result = $this->_set_password_db($identity, $password);

                        if ($result)
                        {
                                $this->trigger_events(['post_sha1_password_migra
tion', 'post_sha1_password_migration_successful']);
                        }
                        else
                        {
                                $this->trigger_events(['post_sha1_password_migra
tion', 'post_sha1_password_migration_unsuccessful']);
                                log_message('error', "Ion_auth_model: SHA1 password migration failed for identity: {$identity}");
                        }

                        return $result; // Return true if migration was successful or if original password was correct
                }
                else
                {
                        // Password mismatch, we cannot migrate...
                        $this->trigger_events(['post_sha1_password_migration', '
post_sha1_password_migration_unsuccessful']);
                        return FALSE;
                }
        }

        /**
         * Clear previous query building properties.
         */
        protected function _clear_query_vars()
        {
            $this->_ion_select = [];
            $this->_ion_where = [];
            $this->_ion_like = [];
            $this->_ion_limit = NULL;
            $this->_ion_offset = NULL;
            $this->_ion_order_by = NULL;
            $this->_ion_order = NULL;
        }
}
