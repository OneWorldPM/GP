<?php

/**
 * @author Athul AK <athullive@gmail.com>
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Class Breakout_room
 */
class Breakout_room extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->common->set_timezone();
        $login_type = $this->session->userdata('userType');
        if ($login_type != 'user') {
            redirect('login');
        }
        $get_user_token_details = $this->common->get_user_details($this->session->userdata('cid'));
        if($this->session->userdata('token') != $get_user_token_details->token){
           redirect('login');
        }
        $this->load->model('user/m_sessions', 'sessions');
        $this->load->model('user/m_private_sessions', 'psessions');
    }

    public function meeting($sessions_id)
    {
        $data["sessions"] = $this->sessions->viewSessionsData($sessions_id);

        $data['participants'] = $this->getParticipantsList($sessions_id);

        $admittedParticipants = array();
        $rejectedParticipants = array();
        foreach ($data['participants'] as $participant)
        {
            if ($participant->action == 'admitted')
                $admittedParticipants[] = $participant->attendee_id;
            elseif ($participant->action == 'rejected')
                $rejectedParticipants[] = $participant->attendee_id;
            else
                $rejectedParticipants[] = $participant->attendee_id;
        }

        $data['admittedParticipants'] = $admittedParticipants;
        $data['rejectedParticipants'] = $rejectedParticipants;

        $this->load->view('header');
        $this->load->view('breakout/breakout_main_room', $data);
        $this->load->view('footer');
    }

    public function room($roomId)
    {
        $user = $this->session->userdata('cid');

        if (!$this->userInOpenRoom($user, $roomId))
            redirect($this->meetingLinkByRoomId($roomId));

        $session_id = $this->meetingIdByRoomId($roomId);
        $data["sessions"] = $this->sessions->viewSessionsData($session_id);

        $data["room_id"] = $roomId;
        $data["room_data"] = $this->getRoomData($roomId);

        $this->load->view('header');
        $this->load->view('breakout/breakout_additional_room', $data);
        $this->load->view('footer');
    }

    public function room_sharescreen($room_id)
    {
        $data["sessions"] = $this->sessions->viewSessionsData($sessions_id);
        $data["session_resource"] = $this->sessions->get_session_resource($sessions_id);

        $startTime = strtotime($data["sessions"]->sessions_date." ".$data["sessions"]->time_slot);
        $endTime = strtotime($data["sessions"]->sessions_date." ".$data["sessions"]->end_time);

        $this->load->view('header');
        $this->load->view('roundtable-sharescreen', $data);
        $this->load->view('footer');
    }

    public function sharescreen($sessions_id)
    {
        $data["sessions"] = $this->sessions->viewSessionsData($sessions_id);
        $data["session_resource"] = $this->sessions->get_session_resource($sessions_id);

        $startTime = strtotime($data["sessions"]->sessions_date." ".$data["sessions"]->time_slot);
        $endTime = strtotime($data["sessions"]->sessions_date." ".$data["sessions"]->end_time);

        $this->load->view('header');
        $this->load->view('roundtable-sharescreen', $data);
        $this->load->view('footer');
    }

    private function getParticipantsList($sessionId)
    {
        $this->db->select('*');
        $this->db->from('breakout_room_participants');
        $this->db->where('session_id', $sessionId);
        $participants = $this->db->get();
        if ($participants->num_rows() > 0) {
            return $participants->result();
        } else {
            return array();
        }
    }

    public function admitAUser()
    {
        $post = $this->input->post();

        $admission_details = array(
            'session_id' => $post['session_id'],
            'attendee_id' => $post['attendee_id'],
            'action' => 'admitted',
            'action_by' => $this->session->userdata('cid'),
            'action_datetime' => date('Y-m-d H:i:s')
        );

        $this->db->insert("breakout_room_participants", $admission_details);

        echo json_encode(array('status'=>'success'));
        return;
    }

    public function rejectAUser()
    {
        $post = $this->input->post();

        $admission_details = array(
            'session_id' => $post['session_id'],
            'attendee_id' => $post['attendee_id'],
            'action' => 'rejected',
            'action_by' => $this->session->userdata('cid'),
            'action_datetime' => date('Y-m-d H:i:s')
        );

        $this->db->insert("breakout_room_participants", $admission_details);

        echo json_encode(array('status'=>'success'));
    }

    public function removeAUser()
    {
        $post = $this->input->post();

        $rejection_details = array(
            'action' => 'rejected',
            'action_by' => $this->session->userdata('cid'),
            'action_datetime' => date('Y-m-d H:i:s')
        );

        $this->db->set($rejection_details);
        $this->db->where('session_id', $post['session_id']);
        $this->db->where('attendee_id', $post['attendee_id']);
        $this->db->update('breakout_room_participants');

        echo json_encode(array('status'=>'success'));
    }


    /**
     * @return void
     */
    public function createRoomsAutomatic()
    {
        $post = $this->input->post();

        $this->db->trans_begin();

        $total_rooms = $post['rooms'];
        $created_rooms = array();
        $room_data = array();
        for ($room = 1; $room <= $total_rooms; $room++)
        {
            $room_details = array(
                'session_id' => $post['session_id'],
                'name' => "Breakout Room {$room}",
                'created_by' => $this->session->userdata('cid'),
                'created_on' => date('Y-m-d H:i:s')
            );

            $this->db->insert("breakout_additional_rooms", $room_details);
            $created_rooms[] = $this->db->insert_id();
            $room_data[$this->db->insert_id()] = $room_details;
        }

        $participantIds = array();
        foreach ($post['participants'] as $socket => $participant)
            if ($participant['id'] != $post['hostId'])
                $participantIds[$socket] = $participant['id'];

        $participantIds = array_unique($participantIds);

        if (sizeof($participantIds) < 2)
        {
            echo json_encode(array('status'=>'error', 'msg'=>'You need at least 2 <u>unique</u> participants other than the host to create rooms.'));
            return;
        }

        $participantsSplit = $this->participants_split($participantIds, $total_rooms);

        if (count($participantsSplit) != count($created_rooms))
        {
            log_message('error', 'Participants allocation error');
            echo json_encode(array('status'=>'error', 'msg'=>'Participants allocation error'));
            return;
        }

        $room_number_to_allocate = 0;
        $allocation_data = array();
        foreach ($participantsSplit as $participantsToAllocate)
        {
            $room_id = $created_rooms[$room_number_to_allocate];
            $allocation_data[$room_id] = array();
            foreach ($participantsToAllocate as $socketId => $participantId)
            {
                $allocation_details = array(
                    'room_id' => $room_id,
                    'user_id' => $participantId,
                    'action_by' => $this->session->userdata('cid'),
                    'action_on' => date('Y-m-d H:i:s')
                );

                $this->db->insert("breakout_additional_rooms_participants", $allocation_details);
                $allocation_data[$room_id][] = array('socketId'=>$socketId, 'participantId'=>$participantId);
            }
            $room_number_to_allocate++;
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            log_message('error', $this->db->error());
            echo json_encode(array('status'=>'error', 'msg'=>'Database error', 'dbError'=>$this->db->error()));
        }
        else
        {
            $this->db->trans_commit();
            echo json_encode(array('status'=>'success', 'msg'=>'Rooms created', 'room_data'=>$room_data, 'allocation_data'=>$allocation_data));
        }
    }



    /**
     *
     * @param Array $participants
     * @param int $into
     * @return multitype
     * @link http://www.php.net/manual/en/function.array-chunk.php#75022
     */
    private function participants_split(Array $participants, $into) {
        $listLen = count($participants);
        $partLen = floor($listLen / $into);
        $partRem = $listLen % $into;
        $partition = array();
        $mark = 0;
        for($px = 0; $px < $into; $px ++) {
            $incr = ($px < $partRem) ? $partLen + 1 : $partLen;
            $partition[$px] = array_slice($participants, $mark, $incr);
            $mark += $incr;
        }
        return $partition;
    }


    private function userInOpenRoom($userid, $roomId)
    {
        $this->db->select('*');
        $this->db->from('breakout_additional_rooms as room');
        $this->db->join('breakout_additional_rooms_participants as participant', 'participant.room_id = room.id');
        $this->db->where('participant.user_id', $userid);
        $this->db->where('room.id', $roomId);
        $this->db->where('room.isOpen', 1);
        $room = $this->db->get();
        if ($room->num_rows() > 0)
            return true;
        return false;
    }

    private function meetingLinkByRoomId($roomId)
    {
        $this->db->select('session_id');
        $this->db->from('breakout_additional_rooms');
        $this->db->where('id', $roomId);
        $session = $this->db->get();
        if ($session->num_rows() > 0) {
            return base_url().'breakout_room/meeting/'.$session->row()->session_id;
        } else {
            return base_url().'sessions';
        }
    }

    private function meetingIdByRoomId($roomId)
    {
        $this->db->select('session_id');
        $this->db->from('breakout_additional_rooms');
        $this->db->where('id', $roomId);
        $session = $this->db->get();
        if ($session->num_rows() > 0) {
            return $session->row()->session_id;
        } else {
            return 0;
        }
    }

    public function createdRoomDetails($sessionId)
    {
        $this->db->select('*');
        $this->db->from('breakout_additional_rooms');
        $this->db->where('session_id', $sessionId);
        $rooms = $this->db->get();
        if ($rooms->num_rows() > 0)
        {
            foreach ($rooms->result() as $room)
                $room->participants = $this->participantsByRoomId($room->id);

            echo json_encode($rooms->result());
        }else{
            echo json_encode(array());
        }
    }

    private function participantsByRoomId($roomId)
    {
        $this->db->select("CONCAT(profile.first_name, ' ', profile.last_name) as name, participant.*");
        $this->db->from('breakout_additional_rooms_participants participant');
        $this->db->join('customer_master profile', "profile.cust_id = participant.user_id");
        $this->db->where('room_id', $roomId);
        $participant = $this->db->get();
        if ($participant->num_rows() > 0)
        {
            return $participant->result();
        }else{
            return array();
        }
    }


    public function changeParticipantRoom()
    {
        $post = $this->input->post();

        $participant_details = array(
            'room_id' => $post['to_room_id'],
            'action_by' => $this->session->userdata('cid'),
            'action_on' => date('Y-m-d H:i:s')
        );

        $this->db->set($participant_details);
        $this->db->where('id', $post['participant_id']);
        $this->db->update('breakout_additional_rooms_participants');

        if($this->db->affected_rows() > 0)
        {
            echo json_encode(array('status'=>'success'));
        }else{
            echo json_encode(array('status'=>'failed'));
        }
    }

    public function removeParticipantFromRoom()
    {
        $post = $this->input->post();

        $this->db->where('id', $post['participant_id']);
        $this->db->delete('breakout_additional_rooms_participants');

        if($this->db->affected_rows() > 0)
        {
            echo json_encode(array('status'=>'success'));
        }else{
            echo json_encode(array('status'=>'failed'));
        }
    }

    public function addParticipantToRoom()
    {
        $post = $this->input->post();

        $participant_details = array(
            'room_id' => $post['to_room_id'],
            'user_id' => $post['user_id'],
            'action_by' => $this->session->userdata('cid'),
            'action_on' => date('Y-m-d H:i:s')
        );

        $this->db->insert("breakout_additional_rooms_participants", $participant_details);

        if($this->db->affected_rows() > 0)
        {
            echo json_encode(array('status'=>'success', 'participant_id'=>$this->db->insert_id()));
        }else{
            echo json_encode(array('status'=>'failed'));
        }
    }

    private function getRoomData($roomId)
    {
        $this->db->select('*');
        $this->db->from('breakout_additional_rooms');
        $this->db->where('id', $roomId);
        $room = $this->db->get();
        if ($room->num_rows() > 0)
        {
            return $room->row();
        }else{
            return false;
        }
    }

    private function getOpenedRoomDetails($meetingId)
    {
        $this->db->select('*');
        $this->db->from('breakout_additional_rooms');
        $this->db->where('isOpen', 1);
        $this->db->where('session_id', $meetingId);
        $rooms = $this->db->get();
        if ($rooms->num_rows() > 0)
        {
            foreach ($rooms->result() as $room)
                $room->participants = $this->participantsByRoomId($room->id);

            return $rooms->result();
        }else{
            return array();
        }
    }

    private function getAllRoomDetails($meetingId)
    {
        $this->db->select('*');
        $this->db->from('breakout_additional_rooms');
        $this->db->where('session_id', $meetingId);
        $rooms = $this->db->get();
        if ($rooms->num_rows() > 0)
        {
            foreach ($rooms->result() as $room)
                $room->participants = $this->participantsByRoomId($room->id);

            return $rooms->result();
        }else{
            return array();
        }
    }

    public function getOpenedRoomStatus($meetingId)
    {
        if (!empty($this->getOpenedRoomDetails($meetingId)))
            echo json_encode(array('status'=>'1'));
        else
            echo json_encode(array('status'=>'0'));
    }

    public function getCreatedRoomStatus($meetingId)
    {
        if (!empty($this->getAllRoomDetails($meetingId)))
            echo json_encode(array('status'=>'1'));
        else
            echo json_encode(array('status'=>'0'));
    }

    public function openAllRooms($meetingId)
    {
        $this->db->set('isOpen', 1);
        $this->db->where('session_id', $meetingId);
        $this->db->update('breakout_additional_rooms');

        if($this->db->affected_rows() > 0)
        {
            $rooms = $this->getOpenedRoomDetails($meetingId);
            echo json_encode(array('status'=>'success', 'rooms'=>$rooms));
        }else{
            echo json_encode(array('status'=>'failed'));
        }
    }


    public function closeAllRooms($meetingId)
    {
        $rooms = $this->getAllRoomDetails($meetingId);

        $this->db->trans_begin();
        foreach ($rooms as $room)
        {
            foreach ($room->participants as $participant)
            {
                $this->db->where('id', $participant->id);
                $this->db->delete('breakout_additional_rooms_participants');
            }

            $this->db->where('id', $room->id);
            $this->db->delete('breakout_additional_rooms');
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            log_message('error', $this->db->error());
            echo json_encode(array('status'=>'error', 'msg'=>'Database error', 'dbError'=>$this->db->error()));
        }
        else
        {
            $this->db->trans_commit();
            echo json_encode(array('status'=>'success'));
        }
    }
}
