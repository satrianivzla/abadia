<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agente_model extends CI_Model {

    /**
     * @var CI_DB_query_builder The Query Builder instance for DataTables
     */
    public $builder;

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Ensure database is loaded
        $this->set_builder_for_datatables(); // Initialize the builder
    }

    /**
     * Initializes the Query Builder object ($this->builder) for use with TablesIgniter.
     * It includes the necessary SELECT columns and JOINs.
     */
    public function set_builder_for_datatables() {
        $this->builder = $this->db
                              ->select('agentes.id, agentes.nombres, agentes.apellidos, agentes.cedula, agentes.rif, agentes.correo_electronico, agentes.telefono_celular, cargos.cargo as cargo_nombre, agentes.foto_perfil')
                              ->join('cargos', 'agentes.id_cargo = cargos.id_cargo', 'left')
                              ->where('agentes.deleted_at', NULL); // Only show active agents
    }

    // ------------------------------------------------------------------------
    // AGENTES CRUD Methods (for create/edit/delete forms)
    // ------------------------------------------------------------------------

    /**
     * Get a single ACTIVE agent by ID
     * @param int $id
     * @param bool $include_deleted Set to true to fetch even if soft-deleted
     * @return object or NULL
     */
    public function get_agent_by_id($id, $include_deleted = false) {
        $this->db->select('a.*, c.cargo as cargo_nombre, est.estado as estado_nombre, ciu.ciudad as ciudad_nombre, mun.municipio as municipio_nombre, par.parroquia as parroquia_nombre');
        $this->db->from('agentes a');
        $this->db->join('cargos c', 'a.id_cargo = c.id_cargo', 'left');
        $this->db->join('estados est', 'a.id_estado = est.id_estado', 'left');
        $this->db->join('ciudades ciu', 'a.id_ciudad = ciu.id_ciudad', 'left');
        $this->db->join('municipios mun', 'a.id_municipio = mun.id_municipio', 'left');
        $this->db->join('parroquias par', 'a.id_parroquia = par.id_parroquia', 'left');
        $this->db->where('a.id', $id);

        if (!$include_deleted) {
            $this->db->where('a.deleted_at', NULL);
        }

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Insert a new agent
     * @param array $data
     * @return int (insert ID) or false
     */
    public function insert_agent($data) {
        return $this->db->insert('agentes', $data);
    }

    /**
     * Update an existing agent
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_agent($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('agentes', $data);
    }

    /**
     * Soft delete an agent
     * @param int $id
     * @param int $user_id The ID of the user performing the deletion
     * @return bool
     */
    public function delete_agent($id, $user_id) {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $user_id
        ];
        $this->db->where('id', $id);
        return $this->db->update('agentes', $data);
    }

    /**
     * Restore a soft-deleted agent
     * @param int $id
     * @return bool
     */
    public function restore_agent($id) {
        $data = [
            'deleted_at' => NULL,
            'deleted_by' => NULL
        ];
        $this->db->where('id', $id);
        return $this->db->update('agentes', $data);
    }

    /**
     * Provides a query builder object for fetching DELETED agents for DataTables.
     * Includes joins to 'users' to show who deleted the agent.
     */
    public function get_deleted_agents_builder() {
        return $this->db
                     ->select('agentes.id, agentes.nombres, agentes.apellidos, agentes.cedula, agentes.deleted_at, u.first_name as deleted_by_firstname, u.last_name as deleted_by_lastname')
                     ->join('users u', 'agentes.deleted_by = u.id', 'left')
                     ->where('agentes.deleted_at IS NOT NULL');
    }

    // ------------------------------------------------------------------------
    // LOOKUP TABLE Methods (for forms)
    // ------------------------------------------------------------------------

    public function get_estados() {
        $this->db->order_by('estado', 'ASC');
        $query = $this->db->get('estados');
        return $query->result();
    }

    public function get_cargos() {
        $this->db->order_by('cargo', 'ASC');
        $query = $this->db->get('cargos');
        return $query->result();
    }

    public function get_ciudades_by_estado($estado_id) {
        $this->db->where('id_estado', $estado_id);
        $this->db->order_by('ciudad', 'ASC');
        $query = $this->db->get('ciudades');
        return $query->result();
    }

    public function get_municipios_by_estado($estado_id) {
        $this->db->where('id_estado', $estado_id);
        $this->db->order_by('municipio', 'ASC');
        $query = $this->db->get('municipios');
        return $query->result();
    }

    public function get_parroquias_by_municipio($municipio_id) {
        $this->db->where('id_municipio', $municipio_id);
        $this->db->order_by('parroquia', 'ASC');
        $query = $this->db->get('parroquias');
        return $query->result();
    }
}
/* End of file Agente_model.php */
/* Location: ./application/models/Agente_model.php */
