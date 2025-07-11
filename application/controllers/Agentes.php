<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agentes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Libraries, helpers, and models are mostly autoloaded.
        // Explicitly load Agente_model here if not autoloaded globally.
        $this->load->model('agente_model');
        $this->load->model('ion_auth_model'); // Ion auth model for user interaction if needed directly

        // Basic security check: ensure user is logged in for most methods.
        // Specific methods like 'get_ciudades' might be public if needed without login,
        // but CRUD operations should be protected.
        if (!$this->ion_auth->logged_in() &&
            !in_array($this->router->fetch_method(), ['get_ciudades', 'get_municipios', 'get_parroquias'])) {
            redirect('auth/login', 'refresh');
        }
        // You might want to add role/group checks here too, e.g., only 'admin' can access.
        // if (!$this->ion_auth->is_admin() && $this->router->fetch_method() !== 'index') {
        //     $this->session->set_flashdata('error', 'You must be an administrator to perform this action.');
        //     redirect('/', 'refresh');
        // }
    }

    /**
     * List all agents - will use Datatables
     */
    public function index() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        // Data for the view, like title
        $data['title'] = 'Listado de Agentes';
        // Load view (we'll create this later)
        // For now, a placeholder to indicate it's for Datatables
        $this->load->view('templates/header', $data); // Assuming a header template
        $this->load->view('agentes/index', $data);    // Main content for agents list
        $this->load->view('templates/footer');       // Assuming a footer template
    }

    /**
     * Show the form to create a new agent
     */
    public function create() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['title'] = 'Nuevo Agente de Ventas';
        $data['estados'] = $this->agente_model->get_estados();
        $data['cargos'] = $this->agente_model->get_cargos();
        // $data['ciudades'] = []; // Will be populated by AJAX
        // $data['municipios'] = []; // Will be populated by AJAX
        // $data['parroquias'] = []; // Will be populated by AJAX

        $this->load->view('templates/header', $data);
        $this->load->view('agentes/create', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Store a newly created agent in storage.
     */
    public function store() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        // Set validation rules
        $this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('cedula', 'Cédula', 'trim|required|max_length[20]|is_unique[agentes.cedula]');
        $this->form_validation->set_rules('rif', 'RIF', 'trim|required|max_length[20]|is_unique[agentes.rif]');
        $this->form_validation->set_rules('sexo', 'Sexo', 'required|in_list[Masculino,Femenino]');
        $this->form_validation->set_rules('telefono_celular', 'Teléfono Celular', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('telefono_local', 'Teléfono Local', 'trim|max_length[20]');
        $this->form_validation->set_rules('correo_electronico', 'Correo Electrónico', 'trim|required|valid_email|max_length[100]|is_unique[agentes.correo_electronico]');
        $this->form_validation->set_rules('direccion_habitacion', 'Dirección de Habitación', 'trim|required');
        $this->form_validation->set_rules('id_estado', 'Estado', 'required|integer');
        $this->form_validation->set_rules('id_ciudad', 'Ciudad', 'required|integer');
        $this->form_validation->set_rules('id_municipio', 'Municipio', 'required|integer');
        $this->form_validation->set_rules('id_parroquia', 'Parroquia', 'required|integer');
        $this->form_validation->set_rules('fecha_ingreso', 'Fecha de Ingreso', 'required');
        $this->form_validation->set_rules('id_cargo', 'Cargo', 'required|integer');
        // 'foto_perfil' will be handled separately for upload

        if ($this->form_validation->run() == FALSE) {
            // Validation failed, reload the create form with errors
            $this->session->set_flashdata('error', validation_errors());
            $this->create(); // Reload the form view
        } else {
            // Validation passed, process the data
            $data = [
                'nombres' => $this->input->post('nombres'),
                'apellidos' => $this->input->post('apellidos'),
                'cedula' => $this->input->post('cedula'),
                'rif' => $this->input->post('rif'),
                'sexo' => $this->input->post('sexo'),
                'telefono_celular' => $this->input->post('telefono_celular'),
                'telefono_local' => $this->input->post('telefono_local'),
                'correo_electronico' => $this->input->post('correo_electronico'),
                'direccion_habitacion' => $this->input->post('direccion_habitacion'),
                'id_estado' => $this->input->post('id_estado'),
                'id_ciudad' => $this->input->post('id_ciudad'),
                'id_municipio' => $this->input->post('id_municipio'),
                'id_parroquia' => $this->input->post('id_parroquia'),
                'fecha_ingreso' => $this->input->post('fecha_ingreso'),
                'id_cargo' => $this->input->post('id_cargo'),
                // 'user_id' => $this->ion_auth->user()->row()->id // If agent is also an Ion Auth user
            ];

            // Handle file upload for 'foto_perfil'
            $upload_path = './uploads/agentes_fotos/';
            if (!is_dir($upload_path)) {
                // Create with more secure permissions if possible, though web server user needs write access.
                // 0755 is a common starting point.
                mkdir($upload_path, 0755, TRUE);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = '2048'; // 2MB
            $config['encrypt_name'] = TRUE; // Encrypt filename

            $this->load->library('upload', $config);

            if (!empty($_FILES['foto_perfil']['name'])) {
                if ($this->upload->do_upload('foto_perfil')) {
                    $upload_data = $this->upload->data();
                    $data['foto_perfil'] = $upload_path . $upload_data['file_name'];
                } else {
                    // File upload failed
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    $this->create();
                    return; // Stop execution
                }
            }

            // Insert data into database via model
            $insert_id = $this->agente_model->insert_agent($data);

            if ($insert_id) {
                $this->session->set_flashdata('message', 'Agente registrado exitosamente.');
                redirect('agentes', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error al registrar el agente. Intente nuevamente.');
                $this->create();
            }
        }
    }

    /**
     * Show the form for editing the specified agent.
     */
    public function edit($id) {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['title'] = 'Editar Agente';
        $data['agente'] = $this->agente_model->get_agent_by_id($id);

        if (empty($data['agente'])) {
            show_404();
            return;
        }

        $data['estados'] = $this->agente_model->get_estados();
        $data['cargos'] = $this->agente_model->get_cargos();
        // For dependent dropdowns, we might need to pre-fetch them based on the agent's current values
        $data['ciudades'] = $this->agente_model->get_ciudades_by_estado($data['agente']->id_estado);
        $data['municipios'] = $this->agente_model->get_municipios_by_estado($data['agente']->id_estado); // Or by city's state if logic differs
        $data['parroquias'] = $this->agente_model->get_parroquias_by_municipio($data['agente']->id_municipio);

        $this->load->view('templates/header', $data);
        $this->load->view('agentes/edit', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Update the specified agent in storage.
     */
    public function update($id) {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $agente = $this->agente_model->get_agent_by_id($id);
        if (empty($agente)) {
            show_404();
            return;
        }

        // Set validation rules
        $this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|max_length[100]');
        // For unique fields, we need to check if the value is being changed and if the new value is unique
        $original_cedula = $agente->cedula;
        if ($this->input->post('cedula') != $original_cedula) {
            $this->form_validation->set_rules('cedula', 'Cédula', 'trim|required|max_length[20]|is_unique[agentes.cedula]');
        } else {
            $this->form_validation->set_rules('cedula', 'Cédula', 'trim|required|max_length[20]');
        }

        $original_rif = $agente->rif;
        if ($this->input->post('rif') != $original_rif) {
            $this->form_validation->set_rules('rif', 'RIF', 'trim|required|max_length[20]|is_unique[agentes.rif]');
        } else {
            $this->form_validation->set_rules('rif', 'RIF', 'trim|required|max_length[20]');
        }

        $this->form_validation->set_rules('sexo', 'Sexo', 'required|in_list[Masculino,Femenino]');
        $this->form_validation->set_rules('telefono_celular', 'Teléfono Celular', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('telefono_local', 'Teléfono Local', 'trim|max_length[20]');

        $original_correo = $agente->correo_electronico;
        if ($this->input->post('correo_electronico') != $original_correo) {
            $this->form_validation->set_rules('correo_electronico', 'Correo Electrónico', 'trim|required|valid_email|max_length[100]|is_unique[agentes.correo_electronico]');
        } else {
            $this->form_validation->set_rules('correo_electronico', 'Correo Electrónico', 'trim|required|valid_email|max_length[100]');
        }

        $this->form_validation->set_rules('direccion_habitacion', 'Dirección de Habitación', 'trim|required');
        $this->form_validation->set_rules('id_estado', 'Estado', 'required|integer');
        $this->form_validation->set_rules('id_ciudad', 'Ciudad', 'required|integer');
        $this->form_validation->set_rules('id_municipio', 'Municipio', 'required|integer');
        $this->form_validation->set_rules('id_parroquia', 'Parroquia', 'required|integer');
        $this->form_validation->set_rules('fecha_ingreso', 'Fecha de Ingreso', 'required');
        $this->form_validation->set_rules('id_cargo', 'Cargo', 'required|integer');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            $this->edit($id); // Reload edit form with errors
        } else {
            $data = [
                'nombres' => $this->input->post('nombres'),
                'apellidos' => $this->input->post('apellidos'),
                'cedula' => $this->input->post('cedula'),
                'rif' => $this->input->post('rif'),
                'sexo' => $this->input->post('sexo'),
                'telefono_celular' => $this->input->post('telefono_celular'),
                'telefono_local' => $this->input->post('telefono_local'),
                'correo_electronico' => $this->input->post('correo_electronico'),
                'direccion_habitacion' => $this->input->post('direccion_habitacion'),
                'id_estado' => $this->input->post('id_estado'),
                'id_ciudad' => $this->input->post('id_ciudad'),
                'id_municipio' => $this->input->post('id_municipio'),
                'id_parroquia' => $this->input->post('id_parroquia'),
                'fecha_ingreso' => $this->input->post('fecha_ingreso'),
                'id_cargo' => $this->input->post('id_cargo'),
            ];

            // Handle file upload for 'foto_perfil' if a new one is provided
            if (!empty($_FILES['foto_perfil']['name'])) {
                $upload_path = './uploads/agentes_fotos/';
                 if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, TRUE); // More secure permissions
                }
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '2048'; // 2MB
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);
                $this->upload->initialize($config); // Re-initialize for safety

                if ($this->upload->do_upload('foto_perfil')) {
                    $upload_data = $this->upload->data();
                    $data['foto_perfil'] = $upload_path . $upload_data['file_name'];
                    // Optionally, delete old photo if it exists and is different
                    if (!empty($agente->foto_perfil) && file_exists($agente->foto_perfil) && ($upload_path . $upload_data['file_name']) != $agente->foto_perfil) {
                        unlink($agente->foto_perfil);
                    }
                } else {
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    $this->edit($id);
                    return;
                }
            }

            if ($this->agente_model->update_agent($id, $data)) {
                $this->session->set_flashdata('message', 'Agente actualizado exitosamente.');
                redirect('agentes', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar el agente. Intente nuevamente.');
                $this->edit($id);
            }
        }
    }

    /**
     * Remove the specified agent from storage.
     */
    public function delete($id) {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        // Add admin check here if needed
        // if (!$this->ion_auth->is_admin()) {
        //     $this->session->set_flashdata('error', 'You must be an administrator to delete agents.');
        //     redirect('agentes', 'refresh');
        //     return;
        // }

        $agente = $this->agente_model->get_agent_by_id($id);
        if (empty($agente)) {
            show_404();
            return;
        }

        // Optionally, delete associated profile picture
        if (!empty($agente->foto_perfil) && file_exists($agente->foto_perfil)) {
            unlink($agente->foto_perfil);
        }

        if ($this->agente_model->delete_agent($id)) {
            $this->session->set_flashdata('message', 'Agente eliminado exitosamente.');
        } else {
            $this->session->set_flashdata('error', 'Error al eliminar el agente.');
        }
        redirect('agentes', 'refresh');
    }

    // --------------------------------------------------------------------
    // AJAX Methods for Dependent Dropdowns
    // --------------------------------------------------------------------

    /**
     * Get Ciudades based on Estado ID
     */
    public function get_ciudades() {
        // This method might be public or require a different auth check if used on a public form part
        $estado_id = $this->input->post('estado_id');
        $ciudades = [];
        if ($estado_id) {
            $ciudades = $this->agente_model->get_ciudades_by_estado($estado_id);
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($ciudades));
    }

    /**
     * Get Municipios based on Estado ID (as per existing JS logic)
     */
    public function get_municipios() {
        $estado_id = $this->input->post('estado_id'); // Existing JS sends estado_id
        $municipios = [];
        if ($estado_id) {
            $municipios = $this->agente_model->get_municipios_by_estado($estado_id);
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($municipios));
    }

    /**
     * Get Parroquias based on Municipio ID
     */
    public function get_parroquias() {
        $municipio_id = $this->input->post('municipio_id');
        $parroquias = [];
        if ($municipio_id) {
            $parroquias = $this->agente_model->get_parroquias_by_municipio($municipio_id);
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($parroquias));
    }

    /**
     * Server-side processing for Datatables (Manual Implementation)
     */
    public function get_agentes_list_ss() {
        if (!$this->ion_auth->logged_in()) {
            $this->output->set_status_header(401)->set_output(json_encode(['error' => 'Unauthorized']));
            return;
        }

        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $searchValue = $this->input->post('search')['value'];
        $orderColumnIndex = $this->input->post('order')[0]['column'];
        $orderDirection = $this->input->post('order')[0]['dir'];

        // Define columns. Must match the order in JS and include DB field names.
        // foto_perfil is at index 1, actions at index 9 - these are not DB sortable/searchable directly here.
        $columns = array(
            0 => 'agentes.id',
            1 => 'agentes.foto_perfil', // Placeholder, not directly sortable/searchable this way
            2 => 'agentes.nombres',
            3 => 'agentes.apellidos',
            4 => 'agentes.cedula',
            5 => 'agentes.rif',
            6 => 'agentes.correo_electronico',
            7 => 'agentes.telefono_celular',
            8 => 'cargos.cargo', // cargo_nombre alias
            9 => null // Actions column
        );

        // Total records
        $this->db->select('COUNT(agentes.id) as total');
        $this->db->from('agentes');
        $this->db->join('cargos', 'agentes.id_cargo = cargos.id_cargo', 'left');
        $totalRecords = $this->db->get()->row()->total;

        // Build query for filtered and paginated data
        $this->db->select('agentes.id, agentes.nombres, agentes.apellidos, agentes.cedula, agentes.rif, agentes.correo_electronico, agentes.telefono_celular, cargos.cargo as cargo_nombre, agentes.foto_perfil');
        $this->db->from('agentes');
        $this->db->join('cargos', 'agentes.id_cargo = cargos.id_cargo', 'left');

        // Search
        if (!empty($searchValue)) {
            $this->db->group_start();
            $this->db->like('agentes.nombres', $searchValue);
            $this->db->or_like('agentes.apellidos', $searchValue);
            $this->db->or_like('agentes.cedula', $searchValue);
            $this->db->or_like('agentes.rif', $searchValue);
            $this->db->or_like('agentes.correo_electronico', $searchValue);
            $this->db->or_like('agentes.telefono_celular', $searchValue);
            $this->db->or_like('cargos.cargo', $searchValue);
            // Note: Searching by ID might not be user-friendly unless they know IDs.
            // $this->db->or_like('agentes.id', $searchValue);
            $this->db->group_end();
        }

        // Total filtered records (count after search)
        $this->db->select('COUNT(agentes.id) as total_filtered');
        $this->db->from('agentes'); // Need to re-specify from and joins for this count
        $this->db->join('cargos', 'agentes.id_cargo = cargos.id_cargo', 'left');
        if (!empty($searchValue)) { // Apply search again for accurate filtered count
            $this->db->group_start();
            $this->db->like('agentes.nombres', $searchValue);
            $this->db->or_like('agentes.apellidos', $searchValue);
            $this->db->or_like('agentes.cedula', $searchValue);
            $this->db->or_like('agentes.rif', $searchValue);
            $this->db->or_like('agentes.correo_electronico', $searchValue);
            $this->db->or_like('agentes.telefono_celular', $searchValue);
            $this->db->or_like('cargos.cargo', $searchValue);
            $this->db->group_end();
        }
        $totalFiltered = $this->db->get()->row()->total_filtered;

        // Re-build the main query for actual data fetching with ordering and limit
        $this->db->select('agentes.id, agentes.nombres, agentes.apellidos, agentes.cedula, agentes.rif, agentes.correo_electronico, agentes.telefono_celular, cargos.cargo as cargo_nombre, agentes.foto_perfil');
        $this->db->from('agentes');
        $this->db->join('cargos', 'agentes.id_cargo = cargos.id_cargo', 'left');

        if (!empty($searchValue)) {
            $this->db->group_start();
            $this->db->like('agentes.nombres', $searchValue);
            $this->db->or_like('agentes.apellidos', $searchValue);
            $this->db->or_like('agentes.cedula', $searchValue);
            $this->db->or_like('agentes.rif', $searchValue);
            $this->db->or_like('agentes.correo_electronico', $searchValue);
            $this->db->or_like('agentes.telefono_celular', $searchValue);
            $this->db->or_like('cargos.cargo', $searchValue);
            $this->db->group_end();
        }

        // Ordering
        if (isset($columns[$orderColumnIndex]) && $columns[$orderColumnIndex] != null) {
            $this->db->order_by($columns[$orderColumnIndex], $orderDirection);
        } else {
            // Default order
            $this->db->order_by('agentes.id', 'DESC');
        }

        // Limit (pagination)
        if ($length != -1) { // -1 means show all records
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $agentes_data = $query->result_array();

        $data = [];
        foreach ($agentes_data as $agente) {
            $rowData = [];
            $rowData['id'] = $agente['id'];

            // Foto Perfil (path construction logic as in view)
            $foto_url = base_url('uploads/default_avatar.png'); // Default
            if (!empty($agente['foto_perfil'])) {
                $relative_path = str_starts_with($agente['foto_perfil'], './') ? substr($agente['foto_perfil'], 2) : $agente['foto_perfil'];
                // Basic check if file might exist, ideally use a more robust check or ensure path is always web accessible
                if (file_exists(FCPATH . $relative_path)) {
                     $foto_url = base_url($relative_path);
                } else if (filter_var($agente['foto_perfil'], FILTER_VALIDATE_URL)) { // If it's already a full URL
                    $foto_url = $agente['foto_perfil'];
                }
            }
            $rowData['foto_perfil'] = '<img src="' . $foto_url . '" alt="Foto Perfil" class="img-thumbnail" style="width:50px; height:50px; object-fit:cover;">';

            $rowData['nombres'] = html_escape($agente['nombres']);
            $rowData['apellidos'] = html_escape($agente['apellidos']);
            $rowData['cedula'] = html_escape($agente['cedula']);
            $rowData['rif'] = html_escape($agente['rif']);
            $rowData['correo_electronico'] = html_escape($agente['correo_electronico']);
            $rowData['telefono_celular'] = html_escape($agente['telefono_celular']);
            $rowData['cargo_nombre'] = html_escape($agente['cargo_nombre']);
            $rowData['actions'] = '<a href="'.site_url('agentes/edit/'.$agente['id']).'" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Editar</a> '.
                                  '<a href="'.site_url('agentes/delete/'.$agente['id']).'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Está seguro de que desea eliminar este agente?\');"><i class="fas fa-trash"></i> Eliminar</a>';
            $data[] = $rowData;
        }

        $output = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }
}
