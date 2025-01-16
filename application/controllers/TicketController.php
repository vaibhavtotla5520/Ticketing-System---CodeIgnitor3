<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TicketController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('TicketModel', 'Tickets');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('upload');
    }

    public function createTicket()
    {
        $this->load->view('create_ticket');
    }

    public function getTickets()
    {
        $page = $this->input->get('page') ?? 1;
        $per_page = $this->input->get('per_page') ?? 10;

        $offset = ($page - 1) * $per_page;

        $tickets = $this->Tickets->show($per_page, $offset);
        $total_tickets = $this->Tickets->count_tickets();

        if (!empty($tickets)) {
            echo json_encode([
                'status' => 'success',
                'data' => $tickets,
                'total' => $total_tickets,
                'current_page' => $page,
                'total_pages' => ceil($total_tickets / $per_page),
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No tickets found.',
            ]);
        }
    }

    public function dashboard($page = 0)
    {
        $this->load->library('pagination');
        $config['base_url'] = base_url('dashboard');
        $config['total_rows'] = $this->Tickets->count_tickets();
        $config['per_page'] = 10;
        $config['uri_segment'] = 2;

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;


        $data = [
            // "data" => $this->Tickets->show($config['per_page'], $page),
            "user_roles" => $this->Tickets->getUserRoles(),
            // "pagination_links" => $this->pagination->create_links()
        ];

        $this->load->view('ticket_dashboard', $data);
    }

    public function ticketDeatil()
    {
        $t_id = $this->input->get('t_id');

        if (empty($t_id)) {
            echo json_encode(['status' => 0, 'message' => 'No Ticket ID Found']);
            return;
        }
        $data = [
            "data" => $this->Tickets->replyShow($t_id),
            "replies" => $this->Tickets->repliesShow($t_id)
        ];
        $this->load->view('ticket_reply', $data);
    }

    public function replyTicket()
    {
        $this->load->library('form_validation');

        // Set validation rules
        $this->form_validation->set_rules('t_id', 'ID', 'required|trim');
        $this->form_validation->set_rules('description2', 'Description', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            // Validation failed, return errors
            echo json_encode([
                'error' => validation_errors()
            ]);
            return;
        }

        // Process valid input
        $t_id = $this->input->post('t_id');
        $description = $this->input->post('description2');

        // Handle uploaded files
        $attachments = [];
        if (!empty($_FILES['attachments']['name'][0])) {
            $files = $_FILES;

            foreach ($files['attachments']['name'] as $key => $file_name) {
                $_FILES['file']['name'] = $files['attachments']['name'][$key];
                $_FILES['file']['type'] = $files['attachments']['type'][$key];
                $_FILES['file']['tmp_name'] = $files['attachments']['tmp_name'][$key];
                $_FILES['file']['error'] = $files['attachments']['error'][$key];
                $_FILES['file']['size'] = $files['attachments']['size'][$key];

                $config['upload_path'] = FCPATH . 'public/uploads';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|docx|txt';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                $this->upload->initialize($config);

                if ($this->upload->do_upload('file')) {
                    $attachments[] = $this->upload->data('file_name');
                } else {
                    echo json_encode([
                        'error' => $this->upload->display_errors(),
                        'path' => $config['upload_path']
                    ]);
                    return;
                }
            }
        }

        $status = $this->Tickets->addReply($t_id, $description, $attachments);

        if ($status == 1) {
            echo json_encode(['success' => "Ticket Replied Successfuly"]);
        }
    }

    public function generateTicket()
    {
        // die(1);
        // Load form validation library
        $this->load->library('form_validation');

        // Set validation rules
        $this->form_validation->set_rules('title', 'Title', 'required|trim|min_length[5]');
        $this->form_validation->set_rules('description', 'Description', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            // Validation failed, return errors
            echo json_encode([
                'error' => validation_errors()
            ]);
            return;
        }

        // Process valid input
        $title = $this->input->post('title');
        $description = $this->input->post('description');

        // Handle uploaded files
        $attachments = [];
        if (!empty($_FILES['attachments']['name'][0])) {
            $files = $_FILES;

            foreach ($files['attachments']['name'] as $key => $file_name) {
                $_FILES['file']['name'] = $files['attachments']['name'][$key];
                $_FILES['file']['type'] = $files['attachments']['type'][$key];
                $_FILES['file']['tmp_name'] = $files['attachments']['tmp_name'][$key];
                $_FILES['file']['error'] = $files['attachments']['error'][$key];
                $_FILES['file']['size'] = $files['attachments']['size'][$key];

                $config['upload_path'] = FCPATH . 'public/uploads';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|docx|txt';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                $this->upload->initialize($config);

                if ($this->upload->do_upload('file')) {
                    $attachments[] = $this->upload->data('file_name');
                } else {
                    echo json_encode([
                        'error' => $this->upload->display_errors(),
                        'path' => $config['upload_path']
                    ]);
                    return;
                }
            }
        }

        $status = $this->Tickets->create($title, $description, $attachments);

        if ($status == 1) {
            echo json_encode(['success' => "Ticket Created Successfuly"]);
        }
    }

    public function updateStatus()
    {
        $status = $this->input->post('status');
        $t_id = $this->input->post('t_id');

        if ($t_id == 0) {
            echo json_encode(['error' => 'Ticket ID Can Not Be Empty']);
            return;
        }

        $update_data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->session->userdata('id'),
        ];
        $status = $this->db->where('id', $t_id)->update('tickets', $update_data);
        if ($status) {
            echo json_encode(['success' => 'Ticket Status Updated!']);
        }
    }
}
