<?php

class UnreadMessages  extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $login_type = $this->session->userdata('userType');
        if ($login_type != 'user') {
            redirect('login');
        }
    }

    public function index() {
        $userId = $this->session->userdata("cid");
        $data = array('unreadMessages' => $this->getUnreadMessages($userId));
        $this->load->view('header');
        $this->load->view('user/unread_messages', $data);
        $this->load->view('footer');
    }

    public function getUnreadMessages()
    {
        $from_lounge = $this->getUnreadLoungeMessages();
        $from_sponsors = $this->getUnreadSponsorMessages();

        $all_unread = array_merge( $from_lounge, $from_sponsors );

        echo json_encode($all_unread);
        return;
    }

    public function getUnreadLoungeMessages()
    {
        $userId = $this->session->userdata("cid");
        $this->db->select("loc.*, concat(cm.first_name, ' ', cm.last_name) as from_name");
        $this->db->from('lounge_oto_chat loc');
        $this->db->join('customer_master cm', 'cm.cust_id = loc.from_id');
        $this->db->where(array('loc.to_id'=>$userId, 'loc.marked_read'=>0));
        $this->db->group_by('loc.from_id');
        $sessions = $this->db->get();
        if ($sessions->num_rows() > 0) {
            foreach ($sessions->result() as $row)
            {
                $row->from_room_type = 'lounge';
            }
            return $sessions->result();
        } else {
            return array();
        }
    }

    public function getUnreadSponsorMessages()
    {
        $userId = $this->session->userdata("cid");
        $this->db->select('soc.*, s.company_name');
        $this->db->from('sponsor_oto_chat soc');
        $this->db->join('sponsors s', 's.sponsors_id = soc.sponsor_id');
        $this->db->where(array('soc.to_id'=>$userId, 'soc.marked_read'=>0));
        $this->db->group_by('soc.sponsor_id');
        $sessions = $this->db->get();
        if ($sessions->num_rows() > 0) {
            foreach ($sessions->result() as $row)
            {
                $row->from_room_type = 'sponsor';
            }
            return $sessions->result();
        } else {
            return array();
        }
    }

    public function markAllAsRead($sponsorId)
    {
        $userId = $this->session->userdata("cid");
        $this->db->set('marked_read', '1');
        $this->db->where(array('to_id'=>$userId, 'sponsor_id'=>$sponsorId));
        $this->db->update('sponsor_oto_chat');
        return;
    }
}
