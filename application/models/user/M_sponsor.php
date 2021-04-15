<?php

class M_sponsor extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getSponsorData() {
        $this->db->select('*');
        $this->db->from('sponsors');
        $sponsors = $this->db->get();
        if ($sponsors->num_rows() > 0) {
            return $sponsors->result();
        } else {
            return '';
        }
    }

    function viewSponsorData($sponsors_id) {
        $this->db->select('*');
        $this->db->from('sponsors');
        $this->db->where("sponsors_id", $sponsors_id);
        $sponsors = $this->db->get();
        if ($sponsors->num_rows() > 0) {
            return $sponsors->row();
        } else {
            return '';
        }
    }
    public function FishbowlDataUpdate($data){
        $sql="CREATE TABLE IF NOT EXISTS `fishbowl` ( `id` int(255) NOT NULL UNIQUE AUTO_INCREMENT, `sponsor_id` int(255) NOT NULL, `attendee_id` int(255) NOT NULL, `datetime` datetime NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $this->db->query($sql);
        $this->db->insert('fishbowl',$data);
      
    }

    public function validateLogin($login_data) {
        $result = $this->db->select('*')->get_where('sponsors', $login_data);
        if ($result->num_rows() > 0) {
            return $result->row_array();
        } else {
            return false;
        }
    }

}