<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agente_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Ensure database is loaded
    }

    // ------------------------------------------------------------------------
    // AGENTES CRUD Methods
    // ------------------------------------------------------------------------

    /**
     * Get all agents (basic version, Datatables logic is in controller for now)
     * @return array
     */
    public function get_all_agentes() {
        $this->db->select('a.*, c.cargo as cargo_nombre, e.estado as estado_nombre');
        $this->db->from('agentes a');
        $this->db->join('cargos c', 'a.id_cargo = c.id_cargo', 'left');
        $this->db->join('estados e', 'a.id_estado = e.id_estado', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get a single agent by ID
     * @param int $id
     * @return object or NULL
     */
    public function get_agent_by_id($id) {
        $this->db->select('a.*, c.cargo as cargo_nombre, est.estado as estado_nombre, ciu.ciudad as ciudad_nombre, mun.municipio as municipio_nombre, par.parroquia as parroquia_nombre');
        $this->db->from('agentes a');
        $this->db->join('cargos c', 'a.id_cargo = c.id_cargo', 'left');
        $this->db->join('estados est', 'a.id_estado = est.id_estado', 'left');
        $this->db->join('ciudades ciu', 'a.id_ciudad = ciu.id_ciudad', 'left');
        $this->db->join('municipios mun', 'a.id_municipio = mun.id_municipio', 'left');
        $this->db->join('parroquias par', 'a.id_parroquia = par.id_parroquia', 'left');
        $this->db->where('a.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Insert a new agent
     * @param array $data
     * @return int (insert ID) or false
     */
    public function insert_agent($data) {
        $this->db->insert('agentes', $data);
        return $this->db->insert_id();
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
     * Delete an agent
     * @param int $id
     * @return bool
     */
    public function delete_agent($id) {
        $this->db->where('id', $id);
        return $this->db->delete('agentes');
    }

    // ------------------------------------------------------------------------
    // LOOKUP TABLE Methods (Estados, Ciudades, Municipios, Parroquias, Cargos)
    // ------------------------------------------------------------------------

    /**
     * Get all estados
     * @return array
     */
    public function get_estados() {
        $this->db->order_by('estado', 'ASC');
        $query = $this->db->get('estados');
        return $query->result();
    }

    /**
     * Get all cargos
     * @return array
     */
    public function get_cargos() {
        $this->db->order_by('cargo', 'ASC');
        $query = $this->db->get('cargos');
        return $query->result();
    }

    /**
     * Get ciudades by estado_id
     * @param int $estado_id
     * @return array
     */
    public function get_ciudades_by_estado($estado_id) {
        $this->db->where('id_estado', $estado_id);
        $this->db->order_by('ciudad', 'ASC');
        $query = $this->db->get('ciudades');
        return $query->result();
    }

    /**
     * Get municipios by estado_id
     * (As per controller/JS logic, municipios are often fetched by estado_id directly)
     * @param int $estado_id
     * @return array
     */
    public function get_municipios_by_estado($estado_id) {
        $this->db->where('id_estado', $estado_id);
        $this->db->order_by('municipio', 'ASC');
        $query = $this->db->get('municipios');
        return $query->result();
    }

    /**
     * Get municipios by ciudad_id (More normalized approach, might not be used by current JS)
     * This would require knowing the estado_id of the ciudad first.
     * For simplicity, sticking to get_municipios_by_estado for now as per existing structure.
     */
    // public function get_municipios_by_ciudad($ciudad_id) {
    //     // First get estado_id from ciudad
    //     $this->db->select('id_estado');
    //     $this->db->where('id_ciudad', $ciudad_id);
    //     $ciudad_query = $this->db->get('ciudades')->row();
    //     if($ciudad_query){
    //         return $this->get_municipios_by_estado($ciudad_query->id_estado);
    //     }
    //     return [];
    // }


    /**
     * Get parroquias by municipio_id
     * @param int $municipio_id
     * @return array
     */
    public function get_parroquias_by_municipio($municipio_id) {
        $this->db->where('id_municipio', $municipio_id);
        $this->db->order_by('parroquia', 'ASC');
        $query = $this->db->get('parroquias');
        return $query->result();
    }

    // ------------------------------------------------------------------------
    // Validation helper methods (optional, can also be done in controller)
    // ------------------------------------------------------------------------

    /**
     * Check if cedula is unique (excluding a specific agent ID for updates)
     * @param string $cedula
     * @param int|null $exclude_id
     * @return bool
     */
    public function is_cedula_unique($cedula, $exclude_id = null) {
        $this->db->where('cedula', $cedula);
        if ($exclude_id !== null) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results('agentes') == 0;
    }

    /**
     * Check if RIF is unique (excluding a specific agent ID for updates)
     * @param string $rif
     * @param int|null $exclude_id
     * @return bool
     */
    public function is_rif_unique($rif, $exclude_id = null) {
        $this->db->where('rif', $rif);
        if ($exclude_id !== null) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results('agentes') == 0;
    }

    /**
     * Check if correo_electronico is unique (excluding a specific agent ID for updates)
     * @param string $correo
     * @param int|null $exclude_id
     * @return bool
     */
    public function is_correo_unique($correo, $exclude_id = null) {
        $this->db->where('correo_electronico', $correo);
        if ($exclude_id !== null) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results('agentes') == 0;
    }
}
