<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->dbutil();
        $this->load->database();
        $this->load->helper('url');
    }

    /**
     * Display the installer view or run the installation.
     */
    public function index() {
        // Prevent running if installation seems complete
        if ($this->db->table_exists('users') && $this->db->table_exists('agentes') && $this->db->table_exists('afiliaciones')) {
            $data['error'] = 'La aplicación ya parece estar instalada. Por seguridad, el instalador ha sido desactivado. Para reinstalar, por favor borre las tablas de la base de datos manualmente.';
            $this->load->view('install/index', $data);
            return;
        }

        if ($this->input->post('install')) {
            $result = $this->_run_sql_from_file(FCPATH . 'install.sql');
            if ($result === TRUE) {
                $data['success'] = '¡Instalación completada exitosamente! Por favor, elimine el archivo <strong>application/controllers/Install.php</strong> por razones de seguridad.';
                $data['success'] .= '<br><a href="' . site_url('auth/login') . '" class="btn btn-primary mt-3">Ir al Login</a>';
            } else {
                $data['error'] = 'Ocurrió un error durante la instalación: <pre>' . html_escape($result) . '</pre>';
            }
            $this->load->view('install/index', $data);
        } else {
            $data['title'] = 'Instalador de la Aplicación';
            $this->load->view('install/index', $data);
        }
    }

    /**
     * Reads an SQL file and executes the queries.
     *
     * @param string $filepath The full server path to the SQL file.
     * @return bool|string TRUE on success, error message string on failure.
     */
    private function _run_sql_from_file($filepath) {
        if (!file_exists($filepath)) {
            return "El archivo de instalación no fue encontrado en: " . $filepath;
        }

        // Read the file content
        $sql_commands = file_get_contents($filepath);

        // Remove comments and split into individual queries
        $sql_commands = preg_replace('/--.*/', '', $sql_commands); // Remove SQL comments
        $queries = explode(';', $sql_commands);

        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                if (!$this->db->query($query)) {
                    $error = $this->db->error();
                    return "Error: " . $error['message'] . " - Query: " . $query;
                }
            }
        }

        return TRUE;
    }
}
/* End of file Install.php */
/* Location: ./application/controllers/Install.php */
