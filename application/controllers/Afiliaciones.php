<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Afiliaciones extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('afiliacion_model');
        $this->load->model('agente_model'); // To get the list of sales advisors
        $this->load->library(['ion_auth', 'form_validation']);
		$this->load->helper(['url', 'language']);

        // Redirect if not logged in
        if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
    }

    /**
     * Display the multi-step affiliation form.
     */
    public function create() {
        $data['title'] = 'Formulario de Afiliación';
        $data['breadcrumbs'] = [
            ['label' => 'Inicio', 'url' => '/'],
            ['label' => 'Nueva Afiliación', 'url' => '']
        ];

        // Fetch active agents to act as advisors
        $data['asesores'] = $this->agente_model->get_all_agentes();

        $data['main_content'] = 'afiliaciones/create';
        $this->load->view('templates/adminlte_layout', $data);
    }

    /**
     * Store a new affiliation from the multi-step form.
     */
    public function store() {
        // --- Backend Validation ---
        // Step 1: Titular and Contract
        $this->form_validation->set_rules('advisor', 'Asesor Responsable', 'required|integer');
        $this->form_validation->set_rules('titular[nombres]', 'Nombres del Titular', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('titular[apellidos]', 'Apellidos del Titular', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('titular[cedula]', 'Cédula del Titular', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('titular[birthdate]', 'Fecha de Nacimiento del Titular', 'required');

        // Step 2: Familiares (validation for arrays)
        $familiares = $this->input->post('familiares');
        if (!empty($familiares)) {
            foreach ($familiares as $key => $familiar) {
                $this->form_validation->set_rules("familiares[{$key}][nombres]", "Nombres del Familiar " . ($key + 1), 'trim|required');
                $this->form_validation->set_rules("familiares[{$key}][apellidos]", "Apellidos del Familiar " . ($key + 1), 'trim|required');
                $this->form_validation->set_rules("familiares[{$key}][cedula]", "Cédula del Familiar " . ($key + 1), 'trim|required');
                $this->form_validation->set_rules("familiares[{$key}][birthdate]", "Fecha de Nacimiento del Familiar " . ($key + 1), 'required');
                $this->form_validation->set_rules("familiares[{$key}][parentesco]", "Parentesco del Familiar " . ($key + 1), 'trim|required');
            }
        }

        // Step 3: Plan and Payment
        $this->form_validation->set_rules('planType', 'Tipo de Plan', 'required');
        $this->form_validation->set_rules('planAmount', 'Monto del Plan', 'required|decimal');
        $this->form_validation->set_rules('cuotas', 'Número de Cuotas', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('paymentType', 'Forma de Pago', 'required');
        if ($this->input->post('paymentType') == 'domiciliacion') {
            $this->form_validation->set_rules('bank_name', 'Banco', 'trim|required');
            $this->form_validation->set_rules('account_number', 'N° de Cuenta', 'trim|required');
            $this->form_validation->set_rules('account_type', 'Tipo de Cuenta', 'trim|required');
        }

        // Step 4: Confirmation
        $this->form_validation->set_rules('termsCheck', 'Términos y Condiciones', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', 'Error de validación: ' . validation_errors());
            redirect('afiliaciones/create', 'refresh');
        } else {
            // --- Data Preparation ---
            $titular_post = $this->input->post('titular');
            $titular_data = [
                'nombres'   => $titular_post['nombres'],
                'apellidos' => $titular_post['apellidos'],
                'cedula'    => $titular_post['cedula'],
                'birthdate' => $titular_post['birthdate'],
                'foto_perfil' => null // Handled below
            ];

            // Handle file upload for titular photo
            if (!empty($_FILES['titular_photo']['name'])) {
                $upload_path = './uploads/personas_fotos/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, TRUE);
                }
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '2048';
                $config['file_name'] = $titular_data['cedula'] . '.' . pathinfo($_FILES['titular_photo']['name'], PATHINFO_EXTENSION);
                $config['overwrite'] = TRUE;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('titular_photo')) {
                    $titular_data['foto_perfil'] = $upload_path . $this->upload->data('file_name');
                } else {
                    $this->session->set_flashdata('error', 'Error al subir la foto del titular: ' . $this->upload->display_errors());
                    redirect('afiliaciones/create', 'refresh');
                    return;
                }
            }

            $afiliacion_data = [
                'contract_number' => $this->input->post('contractNumber'),
                'fecha'           => $this->input->post('currentDate'),
                'asesor_id'       => $this->input->post('advisor'),
                'plan_type'       => $this->input->post('planType'),
                'plan_amount'     => $this->input->post('planAmount'),
                'cuotas'          => $this->input->post('cuotas'),
                'payment_type'    => $this->input->post('paymentType'),
                'bank_name'       => $this->input->post('bank_name'),
                'account_number'  => $this->input->post('account_number'),
                'account_type'    => $this->input->post('account_type'),
                'observaciones'   => $this->input->post('observaciones'),
                'created_by'      => $this->ion_auth->user()->row()->id,
            ];

            // Save everything via the model's transaction
            $afiliacion_id = $this->afiliacion_model->save_afiliacion($afiliacion_data, $titular_data, $familiares);

            if ($afiliacion_id) {
                $this->session->set_flashdata('message', 'Afiliación registrada exitosamente con el N° de Contrato: ' . $afiliacion_data['contract_number']);
                redirect('/', 'refresh'); // Redirect to home or an affiliations list page
            } else {
                $this->session->set_flashdata('error', 'Ocurrió un error al guardar la afiliación. La operación fue cancelada.');
                redirect('afiliaciones/create', 'refresh');
            }
        }
    }
}
/* End of file Afiliaciones.php */
/* Location: ./application/controllers/Afiliaciones.php */
