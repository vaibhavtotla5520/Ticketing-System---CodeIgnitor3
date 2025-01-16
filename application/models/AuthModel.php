<?php

class AuthModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }

    public function authenticateUser($user_email, $user_password)
    {
        $result = $this->db->select('*')
            ->from('users')
            ->where('email', $user_email)
            ->where('password', $user_password)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();

        if ($result->num_rows() > 0) {
            $result = $result->result_array();
            // print_r($result);
            $user_roles = $this->db->select('UR.role_id, MR.name')
                ->from('user_roles UR')
                ->join('master_roles MR', 'UR.role_id = MR.id', 'left')
                ->where('user_id', $result[0]['id'])
                ->order_by("MR.id", "DESC")
                ->get();
            $user_roles = $user_roles->result_array();

            $user_data = [
                'id' => $result[0]['id'],
                'role_id' => $user_roles[0]['role_id'],
                'role_name' => $user_roles[0]['name'],
                'name' => $result[0]['name'],
                'email' => $result[0]['email']
            ];
            $this->session->set_userdata($user_data);
            return 1;
        }
        return 0;
    }
}
