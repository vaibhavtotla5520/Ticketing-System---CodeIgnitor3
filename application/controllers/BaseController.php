<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaseController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('AuthModel', 'Auth');
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    public function index()
    {
        $this->load->view('login');
    }

    public function loginAction()
    {
        $this->form_validation->set_rules('user_email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('user_password', 'Password', 'required|min_length[8]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('login');
        } else {
            $user_email = $this->input->post('user_email');
            $user_password = $this->input->post('user_password');

            $authCheck = $this->Auth->authenticateUser($user_email, hash("sha256", $user_password));
            // print_r($authCheck);
            if ($authCheck == 1) {
                // print_r($this->session->userdata);
                $this->session->set_flashdata('message', 'Action was successful!');
                $this->goTo('dashboard');
            } else {
                $this->session->set_flashdata('message', 'An error occurred.');
                $this->load->view('login');
            }
        }
    }

    public function logoutUser()
    {
        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'You have been logged out successfully.');
        $this->goTo('login');
    }

    public function changeUserRole()
    {
        $role_id = $this->input->post('role_id');
        $role_name = $this->input->post('role_name');

        if (!empty($role_id) && !empty($role_name)) {
            $this->session->set_userdata('role_id', $role_id);
            $this->session->set_userdata('role_name', $role_name);
        }
        echo json_encode(['status' => 'Success']);
    }

    public function addUser()
    {
        $result = $this->db->select('*')
            ->from('master_roles')
            ->get();

        if ($result->num_rows() > 0) {
            $master_roles = $result->result_array();
            $this->load->view('add_user', ['master_roles' => $master_roles]);
        }
    }

    public function addUserAction()
    {
        if ($this->session->userdata('role_id') != 9) {
            echo json_encode(['status' => 0, 'error' => 'Not Authorised For The Action']);
            return;
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('roles', 'Role', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 0, 'error' => 'All Fields Are Required']);
            return;
        } else {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $role = $this->input->post('roles');
            $user_id = '';

            $existing_user = $this->db->get_where('users', ['email' => $email, 'is_deleted' => 0])->row_array();
            if (!empty($existing_user)) {
                echo json_encode(['status' => 0, 'error' => 'User with this email already exists.']);
                return;
            }

            $insert_data_users = [
                'name' => $name,
                'email' => $email,
                'password' => hash("sha256", $password),
                'created_by' => $this->session->userdata('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
                'is_deleted' => 0,
                'created_by' => $this->session->userdata('id'),
                'updated_by' => $this->session->userdata('id')
            ];

            if ($this->db->insert('users', $insert_data_users)) {
                $user_id = $this->db->insert_id();
            }

            if (!empty($user_id)) {
                $insert_data_user_role = [
                    'role_id' => $role,
                    'user_id' => $user_id,
                    'role_added_by' => $this->session->userdata('id'),
                    'role_add_datetime' => date('Y-m-d H:i:s'),
                ];
                if ($this->db->insert('user_roles', $insert_data_user_role)) {
                    echo json_encode(['status' => 1, 'message' => 'User Created, Ready To Login']);
                    return;
                }
            } else {
                echo json_encode(['status' => 0, 'error' => 'Some Error Occured, Try Again']);
                return;
            }
        }
    }

    public function editUser($page = 0)
    {
        $this->load->library('pagination');
        $config['base_url'] = base_url('edit-user');
        $config['total_rows'] = $this->db->count_all('users');
        $config['per_page'] = 10;
        $config['uri_segment'] = 2;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $data['users'] = $this->db->distinct()->select('U.id, U.name, U.email, U.is_active, MR.name AS role_name, U2.name AS created_by_name, U.created_at')
            ->from('users U')
            ->join('user_roles UR', 'UR.user_id = U.id', 'left')
            ->join('master_roles MR', 'MR.id = UR.role_id', 'left')
            ->join('users U2', 'U2.id = U.created_by', 'left')
            ->limit($config['per_page'], $page)
            ->get()
            ->result_array();
        $data['pagination'] = $this->pagination->create_links();
        $this->load->view('edit_user', $data);
    }

    public function getUserDetails()
    {
        $user_id = $this->input->post('user_id');
        if (empty($user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'User ID not found.']);
        }
        $user = $this->db->select('U.id, U.name, U.email, U.is_active, UR.role_id')
            ->from('users U')
            ->join('user_roles UR', 'UR.user_id = U.id')
            ->where('U.id', $user_id)
            ->get()
            ->row_array();

        if (!empty($user)) {
            echo json_encode(['status' => 'success', 'data' => $user]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        }
    }
}
