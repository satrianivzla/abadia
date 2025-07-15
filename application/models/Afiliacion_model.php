<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Afiliacion_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Saves an entire affiliation application within a database transaction.
     *
     * This method will:
     * 1. Find or create the titular in the 'personas' table.
     * 2. Find or create each family member in the 'personas' table.
     * 3. Insert the main contract details into the 'afiliaciones' table.
     * 4. Link the family members to the affiliation in the 'afiliacion_familiares' table.
     *
     * @param array $afiliacion_data Main affiliation data.
     * @param array $titular_data Data for the titular.
     * @param array $familiares_data Array of data for family members.
     * @return int|bool The ID of the new affiliation on success, or false on failure.
     */
    public function save_afiliacion($afiliacion_data, $titular_data, $familiares_data) {

        $this->db->trans_begin();

        // Step 1: Find or Create Titular
        $titular_id = $this->_find_or_create_persona($titular_data);
        if (!$titular_id) {
            $this->db->trans_rollback();
            log_message('error', 'Failed to find or create titular.');
            return false;
        }

        // Step 2: Insert main affiliation record
        $afiliacion_data['titular_id'] = $titular_id;
        $this->db->insert('afiliaciones', $afiliacion_data);
        $afiliacion_id = $this->db->insert_id();

        if (!$afiliacion_id) {
            $this->db->trans_rollback();
            log_message('error', 'Failed to insert into afiliaciones table.');
            return false;
        }

        // Step 3: Process and link family members
        if (!empty($familiares_data)) {
            foreach ($familiares_data as $familiar) {
                // Separate personal data from relationship data
                $persona_data = [
                    'nombres'   => $familiar['nombres'],
                    'apellidos' => $familiar['apellidos'],
                    'cedula'    => $familiar['cedula'],
                    'birthdate' => $familiar['birthdate'],
                ];

                $familiar_id = $this->_find_or_create_persona($persona_data);
                if (!$familiar_id) {
                    $this->db->trans_rollback();
                    log_message('error', 'Failed to find or create familiar with cedula: ' . $persona_data['cedula']);
                    return false;
                }

                // Link familiar to the affiliation
                $link_data = [
                    'afiliacion_id' => $afiliacion_id,
                    'persona_id'    => $familiar_id,
                    'parentesco'    => $familiar['parentesco']
                ];
                $this->db->insert('afiliacion_familiares', $link_data);
            }
        }

        // Step 4: Commit transaction if everything was successful
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            log_message('error', 'Affiliation transaction failed.');
            return false;
        } else {
            $this->db->trans_commit();
            return $afiliacion_id;
        }
    }

    /**
     * Helper method to find a person by their 'cedula'. If not found, create them.
     * This prevents duplicate entries in the 'personas' table.
     *
     * @param array $persona_data Contains data for the person.
     * @return int The ID of the found or newly created person.
     */
    private function _find_or_create_persona($persona_data) {
        if (empty($persona_data['cedula'])) {
            return false;
        }

        $this->db->where('cedula', $persona_data['cedula']);
        $query = $this->db->get('personas');

        if ($query->num_rows() > 0) {
            // Person found, return their ID
            $row = $query->row();
            return $row->id;
        } else {
            // Person not found, create them
            $this->db->insert('personas', $persona_data);
            return $this->db->insert_id();
        }
    }

    /**
     * Get a full affiliation record by its ID.
     *
     * @param int $id The affiliation ID.
     * @return object|null The affiliation data including titular and familiares.
     */
    public function get_afiliacion_by_id($id) {
        // Fetch main affiliation data
        $this->db->select('af.*, p_titular.nombres as titular_nombres, p_titular.apellidos as titular_apellidos, p_titular.cedula as titular_cedula, ag.nombres as asesor_nombres, ag.apellidos as asesor_apellidos');
        $this->db->from('afiliaciones af');
        $this->db->join('personas p_titular', 'af.titular_id = p_titular.id');
        $this->db->join('agentes ag', 'af.asesor_id = ag.id');
        $this->db->where('af.id', $id);
        $afiliacion = $this->db->get()->row();

        if (!$afiliacion) {
            return null;
        }

        // Fetch family members
        $this->db->select('p.*, af_fam.parentesco');
        $this->db->from('afiliacion_familiares af_fam');
        $this->db->join('personas p', 'af_fam.persona_id = p.id');
        $this->db->where('af_fam.afiliacion_id', $id);
        $afiliacion->familiares = $this->db->get()->result();

        return $afiliacion;
    }

    // --- Methods for Dashboards ---

    /**
     * Get affiliations for a specific sales agent.
     * @param int $asesor_id The ID of the agent (from 'agentes' table)
     * @return array
     */
    public function get_afiliaciones_by_asesor_id($asesor_id) {
        $this->db->select('af.*, p.nombres as titular_nombres, p.apellidos as titular_apellidos');
        $this->db->from('afiliaciones af');
        $this->db->join('personas p', 'af.titular_id = p.id');
        $this->db->where('af.asesor_id', $asesor_id);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get an affiliation for a specific client based on their user_id.
     * This assumes a client is a 'persona' and that the 'personas' table has a 'user_id' column.
     * We need to add 'user_id' to the 'personas' table first.
     * @param int $user_id The client's user ID from Ion Auth
     * @return object|null
     */
    public function get_afiliacion_by_client_user_id($user_id) {
        // First, find the persona_id from the user_id
        $persona = $this->db->get_where('personas', ['user_id' => $user_id])->row();
        if (!$persona) {
            return null;
        }

        // Now find the affiliation where this persona is the titular
        return $this->get_afiliacion_by_titular_persona_id($persona->id);
    }

    /**
     * Get an affiliation by the titular's persona ID.
     * @param int $titular_persona_id
     * @return object|null
     */
    public function get_afiliacion_by_titular_persona_id($titular_persona_id) {
        $this->db->where('titular_id', $titular_persona_id);
        $query = $this->db->get('afiliaciones', 1); // Get the first one found

        if($query->num_rows() > 0) {
            return $this->get_afiliacion_by_id($query->row()->id);
        }
        return null;
    }


    /**
     * Count affiliations for a specific agent, grouped by a status column.
     * (Requires adding a 'status' column to the 'afiliaciones' table).
     * @param int $asesor_id
     * @return array
     */
    public function count_by_status_for_asesor($asesor_id) {
        $this->db->select('status, COUNT(id) as count');
        $this->db->from('afiliaciones');
        $this->db->where('asesor_id', $asesor_id);
        $this->db->group_by('status');
        $query = $this->db->get();
        return $query->result();
    }
}
/* End of file Afiliacion_model.php */
/* Location: ./application/models/Afiliacion_model.php */
