<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['ion_auth']);
        $this->load->helper(['url']);
        $this->load->model('agente_model');
        $this->load->model('afiliacion_model');

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
    }

    public function index() {
        $data['title'] = 'Dashboard';
        $data['breadcrumbs'] = [['label' => 'Dashboard', 'url' => '']];
        $user = $this->ion_auth->user()->row();

        if ($this->ion_auth->is_admin() || $this->ion_auth->in_group('Gerente General')) {
            $this->_admin_dashboard($data);
        } elseif ($this->ion_auth->in_group('Gerente de Zona')) {
            $this->_gerente_zona_dashboard($data, $user->id);
        } elseif ($this->ion_auth->in_group('Supervisor')) {
            $this->_supervisor_dashboard($data, $user->id);
        } elseif ($this->ion_auth->in_group('Agente')) {
            $this->_agente_dashboard($data, $user->id);
        } else { // Default to 'Cliente'
            $this->_client_dashboard($data, $user->id);
        }
    }

    private function _admin_dashboard($data) {
        $this->db->from('agentes')->where('deleted_at', NULL);
        $data['total_agentes'] = $this->db->count_all_results();

        $this->db->from('afiliaciones');
        $data['total_afiliaciones'] = $this->db->count_all_results();

        $data['total_users'] = $this->db->count_all('users');

        $data['main_content'] = 'dashboard/admin_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }

    private function _gerente_zona_dashboard($data, $user_id) {
        // This is a placeholder. To implement fully, we need to:
        // 1. Get the 'zona_id' for the manager from their 'agentes' profile.
        // 2. Get all supervisors in that zone.
        // 3. Get all agents for those supervisors.
        // 4. Calculate stats based on those agents.
        $data['main_content'] = 'dashboard/gerente_zona_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }

    private function _supervisor_dashboard($data, $user_id) {
        $supervisor = $this->agente_model->get_agent_by_user_id($user_id);
        if ($supervisor) {
            $data['team_agents'] = $this->agente_model->get_agents_by_supervisor_id($supervisor->id);
        } else {
            $data['team_agents'] = [];
        }

        $data['main_content'] = 'dashboard/supervisor_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }

    private function _agente_dashboard($data, $user_id) {
        $agente = $this->agente_model->get_agent_by_user_id($user_id);
        if ($agente) {
            $data['agente'] = $agente;
            $stats = $this->afiliacion_model->count_by_status_for_asesor($agente->id);
            $data['stats'] = array_column($stats, 'count', 'status');
            $data['total_commission'] = 0; // Placeholder for commission logic
        } else {
            $data['agente'] = null;
            $data['stats'] = [];
            $data['total_commission'] = 0;
        }

        $data['main_content'] = 'dashboard/agente_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }

    private function _client_dashboard($data, $user_id) {
        // We need to find the persona associated with this user_id first
        $persona = $this->db->get_where('personas', ['user_id' => $user_id])->row();

        if ($persona) {
            // Now find an affiliation where this persona is the titular
            $data['afiliacion'] = $this->afiliacion_model->get_afiliacion_by_titular_persona_id($persona->id);
        } else {
            $data['afiliacion'] = null;
        }

        $data['main_content'] = 'dashboard/client_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }
}
