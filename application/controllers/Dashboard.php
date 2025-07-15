<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['ion_auth']);
        $this->load->helper(['url']);

        // All methods in this controller require a login
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
    }

    /**
     * Main dashboard entry point.
     * Routes the user to the correct dashboard based on their group.
     */
    public function index() {
        $data['title'] = 'Dashboard';
        $data['breadcrumbs'] = [['label' => 'Dashboard', 'url' => '']];

        $user = $this->ion_auth->user()->row();

        if ($this->ion_auth->is_admin()) {
            $this->_admin_dashboard($data);
        } elseif ($this->ion_auth->in_group('Gerente General')) {
            // Assuming Gerente General has a view similar to admin for now
            $this->_admin_dashboard($data);
        } elseif ($this->ion_auth->in_group('Gerente de Zona')) {
            $this->_gerente_zona_dashboard($data, $user->id);
        } elseif ($this->ion_auth->in_group('Supervisor')) {
            $this->_supervisor_dashboard($data, $user->id);
        } elseif ($this->ion_auth->in_group('Agente')) {
            $this->_agente_dashboard($data, $user->id);
        } else {
            // Default 'members' group, considered a client
            $this->_client_dashboard($data, $user->id);
        }
    }

    /**
     * Prepares data for the Administrator / Gerente General dashboard.
     */
    private function _admin_dashboard($data) {
        // Placeholder for admin-specific data
        // Example: $data['total_agents'] = $this->db->count_all('agentes');
        // Example: $data['total_afiliaciones'] = $this->db->count_all('afiliaciones');

        $data['main_content'] = 'dashboard/admin_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }

    /**
     * Prepares data for the Gerente de Zona dashboard.
     */
    private function _gerente_zona_dashboard($data, $user_id) {
        // Placeholder for Gerente de Zona specific data
        // This will require model methods to get supervisors and their teams based on zona_id

        $data['main_content'] = 'dashboard/gerente_zona_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }

    /**
     * Prepares data for the Supervisor dashboard.
     */
    private function _supervisor_dashboard($data, $user_id) {
        // Placeholder for Supervisor specific data
        // This will require a model method to find the agent record associated with this user_id
        // Then another model method to get all agents where supervisor_id matches the agent's ID.

        $data['main_content'] = 'dashboard/supervisor_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }

    /**
     * Prepares data for the Agente (Sales Agent) dashboard.
     */
    private function _agente_dashboard($data, $user_id) {
        // Placeholder for Agente specific data
        // This will require model methods to:
        // 1. Get the agent's own profile data.
        // 2. Get all affiliations linked to this agent.
        // 3. Calculate stats (counts, commissions).

        $data['main_content'] = 'dashboard/agente_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }

    /**
     * Prepares data for the Client (standard user) dashboard.
     */
    private function _client_dashboard($data, $user_id) {
        // Placeholder for Client specific data
        // This will require a model method to:
        // 1. Find the 'persona' record linked to this user_id.
        // 2. Find the 'afiliacion' where this persona is the titular.
        // 3. Fetch all details of that affiliation.

        $data['main_content'] = 'dashboard/client_dashboard';
        $this->load->view('templates/adminlte_layout', $data);
    }
}
/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */
