<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Auth
 * @property Ion_auth|Ion_auth_model $ion_auth        The Ion-Auth library
 * @property CI_Form_validation      $form_validation The form validation library
 */
class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language', 'string']);
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
	}

	public function index()
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}
		else
		{
            // If logged in, redirect to the main dashboard
			redirect('dashboard', 'refresh');
		}
	}

	public function login()
	{
		if ($this->ion_auth->logged_in())
		{
			redirect('dashboard', 'refresh');
		}

		$this->data['title'] = $this->lang->line('login_heading');

		$this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
		$this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

		if ($this->form_validation->run() === TRUE)
		{
			$remember = (bool)$this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('dashboard', 'refresh');
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/login', 'refresh');
			}
		}
		else
		{
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->data['identity'] = [
				'name' => 'identity', 'id' => 'identity', 'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
                'class' => 'form-control', 'placeholder' => 'Correo Electrónico'
			];
			$this->data['password'] = [
				'name' => 'password', 'id' => 'password', 'type' => 'password',
                'class' => 'form-control', 'placeholder' => 'Contraseña'
			];
			$this->load->view('auth/login', $this->data);
		}
	}

	public function logout()
	{
		$this->ion_auth->logout();
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		redirect('auth/login', 'refresh');
	}

	public function change_password()
	{
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		}

		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() === FALSE)
		{
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            $this->data['title'] = $this->lang->line('change_password_heading');
            $this->data['breadcrumbs'] = [
                ['label' => 'Dashboard', 'url' => 'dashboard'],
                ['label' => 'Cambiar Contraseña', 'url' => '']
            ];
			$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
			$this->data['old_password'] = ['name' => 'old', 'id' => 'old', 'type' => 'password', 'class' => 'form-control'];
			$this->data['new_password'] = ['name' => 'new', 'id' => 'new', 'type' => 'password', 'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$', 'class' => 'form-control'];
			$this->data['new_password_confirm'] = ['name' => 'new_confirm', 'id' => 'new_confirm', 'type' => 'password', 'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$', 'class' => 'form-control'];
			$this->data['user_id'] = ['name' => 'user_id', 'id' => 'user_id', 'type' => 'hidden', 'value' => $user->id];
            $this->data['csrf'] = $this->_get_csrf_nonce();

            $this->data['main_content'] = 'auth/change_password';
			$this->load->view('templates/adminlte_layout', $this->data);
		}
		else
		{
			$identity = $this->session->userdata('identity');
			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change)
			{
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/change_password', 'refresh');
			}
		}
	}

	public function forgot_password()
	{
        $this->data['title'] = $this->lang->line('forgot_password_heading');

		if ($this->config->item('identity', 'ion_auth') != 'email') {
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
		} else {
			$this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
		}

		if ($this->form_validation->run() === FALSE) {
			$this->data['type'] = $this->config->item('identity', 'ion_auth');
			$this->data['identity'] = ['name' => 'identity', 'id' => 'identity', 'class' => 'form-control'];
			$this->data['identity_label'] = ($this->config->item('identity', 'ion_auth') != 'email') ? $this->lang->line('forgot_password_identity_label') : $this->lang->line('forgot_password_email_identity_label');
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->load->view('auth/forgot_password', $this->data);
		} else {
			$identity_column = $this->config->item('identity', 'ion_auth');
			$identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

			if (empty($identity)) {
                $this->ion_auth->set_error('forgot_password_email_not_found');
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}

			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten) {
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth/login", 'refresh');
			} else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}
		}
	}

	public function reset_password($code = NULL)
	{
		if (!$code) show_404();

        $this->data['title'] = $this->lang->line('reset_password_heading');
		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user) {
			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() === FALSE) {
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = ['name' => 'new', 'id' => 'new', 'type' => 'password', 'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$', 'class' => 'form-control'];
				$this->data['new_password_confirm'] = ['name' => 'new_confirm', 'id' => 'new_confirm', 'type' => 'password', 'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$', 'class' => 'form-control'];
				$this->data['user_id'] = ['name' => 'user_id', 'id' => 'user_id', 'type' => 'hidden', 'value' => $user->id];
				$this->data['code'] = $code;
                $this->data['csrf'] = $this->_get_csrf_nonce();
				$this->load->view('auth/reset_password', $this->data);
			} else {
				$identity = $user->{$this->config->item('identity', 'ion_auth')};
				if ($user->id != $this->input->post('user_id')) {
					$this->ion_auth->clear_forgotten_password_code($identity);
					show_error($this->lang->line('error_csrf'));
				} else {
					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));
					if ($change) {
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						redirect("auth/login", 'refresh');
					} else {
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/reset_password/' . $code, 'refresh');
					}
				}
			}
		} else {
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	public function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);
		return [$key => $value];
	}

	public function _valid_csrf_nonce(){
		$csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
		if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')){
			return TRUE;
		}
		return FALSE;
	}
}
