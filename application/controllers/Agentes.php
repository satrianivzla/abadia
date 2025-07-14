<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agentes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('agente_model');
        $this->load->model('ion_auth_model');

        if (!$this->ion_auth->logged_in() &&
            !in_array($this->router->fetch_method(), ['get_ciudades', 'get_municipios', 'get_parroquias'])) {
            redirect('auth/login', 'refresh');
        }
    }

    public function index() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['title'] = 'Listado de Agentes';
        $data['breadcrumbs'] = [
            ['label' => 'Inicio', 'url' => '/'],
            ['label' => 'Agentes', 'url' => '']
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('agentes/index', $data);
        $this->load->view('templates/footer');
    }

    public function create() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['title'] = 'Nuevo Agente de Ventas';
        $data['breadcrumbs'] = [
            ['label' => 'Inicio', 'url' => '/'],
            ['label' => 'Agentes', 'url' => 'agentes'],
            ['label' => 'Nuevo', 'url' => '']
        ];
        $data['estados'] = $this->agente_model->get_estados();
        $data['cargos'] = $this->agente_model->get_cargos();

        $this->load->view('templates/header', $data);
        $this->load->view('agentes/create', $data);
        $this->load->view('templates/footer');
    }

    public function store() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

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

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            $this->create();
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

            if (!empty($_FILES['foto_perfil']['name'])) {
                $upload_path = './uploads/agentes_fotos/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, TRUE);
                }

                $cedula = $this->input->post('cedula');
                $extension = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
                $filename = $cedula . '.' . strtolower($extension);

                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '2048';
                $config['file_name'] = $filename;
                $config['overwrite'] = TRUE;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('foto_perfil')) {
                    $upload_data = $this->upload->data();
                    $data['foto_perfil'] = $upload_path . $upload_data['file_name'];
                } else {
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    $this->create();
                    return;
                }
            }

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

        $data['breadcrumbs'] = [
            ['label' => 'Inicio', 'url' => '/'],
            ['label' => 'Agentes', 'url' => 'agentes'],
            ['label' => 'Editar', 'url' => '']
        ];
        $data['estados'] = $this->agente_model->get_estados();
        $data['cargos'] = $this->agente_model->get_cargos();
        $data['ciudades'] = $this->agente_model->get_ciudades_by_estado($data['agente']->id_estado);
        $data['municipios'] = $this->agente_model->get_municipios_by_estado($data['agente']->id_estado);
        $data['parroquias'] = $this->agente_model->get_parroquias_by_municipio($data['agente']->id_municipio);

        $this->load->view('templates/header', $data);
        $this->load->view('agentes/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update($id) {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $agente = $this->agente_model->get_agent_by_id($id);
        if (empty($agente)) {
            show_404();
            return;
        }

        $this->form_validation->set_rules('nombres', 'Nombres', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('apellidos', 'Apellidos', 'trim|required|max_length[100]');
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
            $this->edit($id);
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

            if (!empty($_FILES['foto_perfil']['name'])) {
                $upload_path = './uploads/agentes_fotos/';
                 if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, TRUE);
                }

                $cedula = $this->input->post('cedula');
                $extension = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
                $filename = $cedula . '.' . strtolower($extension);

                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '2048';
                $config['file_name'] = $filename;
                $config['overwrite'] = TRUE;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('foto_perfil')) {
                    $upload_data = $this->upload->data();
                    $data['foto_perfil'] = $upload_path . $upload_data['file_name'];
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

    public function delete($id) {
        if (!$this->ion_auth->logged_in()) {
            $this->output->set_status_header(401)->set_output(json_encode(['success' => false, 'message' => 'No autorizado.']));
            return;
        }

        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            $this->output->set_status_header(400)->set_output(json_encode(['success' => false, 'message' => 'Método de solicitud no válido.']));
            return;
        }

        $this->output->set_content_type('application/json');

        $agente = $this->agente_model->get_agent_by_id($id);
        if (empty($agente)) {
            $this->output->set_status_header(404)->set_output(json_encode(['success' => false, 'message' => 'Agente no encontrado.']));
            return;
        }

        if (!empty($agente->foto_perfil) && file_exists($agente->foto_perfil)) {
            unlink($agente->foto_perfil);
        }

        if ($this->agente_model->delete_agent($id)) {
            echo json_encode(['success' => true, 'message' => 'Agente eliminado exitosamente.']);
        } else {
            $this->output->set_status_header(500)->set_output(json_encode(['success' => false, 'message' => 'Error al eliminar el agente de la base de datos.']));
        }
    }

	public function get_ciudades() {
		$estado_id = $this->input->post('estado_id');
		$ciudades = [];
		if ($estado_id) {
			$ciudades = $this->agente_model->get_ciudades_by_estado($estado_id);
		}
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($ciudades));
	}

    public function get_municipios() {
        $estado_id = $this->input->post('estado_id');
        if ($estado_id) {
            $municipios = $this->agente_model->get_municipios_by_estado($estado_id);
            echo json_encode($municipios);
        } else {
            echo json_encode(array());
        }
    }

    public function get_parroquias() {
        $municipio_id = $this->input->post('municipio_id');
        if ($municipio_id) {
            $parroquias = $this->agente_model->get_parroquias_by_municipio($municipio_id);
            echo json_encode($parroquias);
        } else {
            echo json_encode(array());
        }
    }

	public function agentes_list()
	{
		$this->load->library('TablesIgniterCI3', NULL, 'table');
		$this->load->model('Agente_model','model');

		$this->table->setTable($this->model->builder, "agentes");

        $this->table->setSearch(['agentes.cedula', 'agentes.nombres', 'agentes.apellidos']);

        $this->table->setDefaultOrder("id", "DESC");

        $this->table->setOrder([
            0 => 'agentes.id',
            2 => 'agentes.cedula',
            3 => 'agentes.nombres',
            4 => 'agentes.apellidos',
        ]);

		$this->table->setOutput([
            'id',
            'foto_perfil' => function($row) {
                return !empty($row['foto_perfil']) ? base_url( (str_starts_with($row['foto_perfil'], './') ? substr($row['foto_perfil'], 2) : $row['foto_perfil']) ) : base_url('uploads/default_avatar.png');
            },
            'cedula',
            'nombres',
            'apellidos',
        ]);

		echo $this->table->getDatatable();
	}
}
