<?php

class TicketModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }

    public function create($title, $description, $attachments)
    {
        $ticket_data = [
            'user_id' => $this->session->userdata('id'),
            'title' => $title,
            'description' => $description,
            'status' => 'open',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->session->userdata('id'),
            'attachments' => json_encode($attachments)
        ];
        if ($this->db->insert('tickets', $ticket_data)) {
            return 1;
        }
    }

    public function addReply($t_id, $description, $attachments)
    {
        $reply_data = [
            'ticket_id' => $t_id,
            'user_id' => $this->session->userdata('id'),
            'message' => $description,
            'created_at' => date('Y-m-d H:i:s'),
            'attachments' => json_encode($attachments)
        ];
        $update_data = [
            'status' => 'resolved',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->session->userdata('id'),
        ];

        $this->db->where('id', $t_id);
        $this->db->update('tickets', $update_data);

        if ($this->db->insert('replies', $reply_data)) {
            return 1;
        }
    }

    public function show($limit = 10, $offset = 0)
    {
        $result = $this->db->select('T.id, T.title, T.status, T.created_at, T.description, T.attachments, U.name')
            ->from('tickets T')
            ->join('users U', 'U.id = T.user_id', 'left')
            ->order_by('T.updated_at', 'DESC')
            ->limit($limit, $offset)
            ->get();
        if ($result->num_rows() > 0) {
            return $result->result_array();
        }
        
        return 0;
    }

    public function count_tickets()
    {
        return $this->db->count_all('tickets');
    }

    public function replyShow($id)
    {
        $result = $this->db->select('T.id, T.title, T.status, T.created_at, T.description, T.attachments, U.name')
            ->from('tickets T')
            ->join('users U', 'U.id = T.user_id', 'left')
            ->where(['T.id' => $id])
            ->get();

        if ($result->num_rows() > 0) {
            return $result->result_array();
        }

        return 0;
    }

    public function repliesShow($id)
    {
        $result = $this->db->select('R.created_at, R.message, R.attachments, U.name')
            ->from('replies R')
            ->join('users U', 'U.id = R.user_id', 'left')
            ->where(['R.ticket_id' => $id])
            ->order_by("R.created_at", "ASC")
            ->get();

        if ($result->num_rows() > 0) {
            return $result->result_array();
        }
        return 0;
    }

    public function getUserRoles()
    {
        $result = $this->db->select('MR.name, UR.role_id')
            ->from('user_roles UR')
            ->join('master_roles MR', 'MR.id = UR.role_id', 'left')
            ->where(['UR.user_id' => $this->session->userdata('id')])
            ->get();

        if ($result->num_rows() > 0) {
            return $result->result_array();
        }
        return 0;
    }
}
