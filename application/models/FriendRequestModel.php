<?php
class FriendRequestModel extends CI_Model {

    // Send Friend Request
    public function __construct(){ 
        parent::__construct(); 
        $this->load->database();  
    } 
    public function sendRequest($data) {
        return $this->db->insert('friend_requests', $data);
    }

    // Get all Friend Requests for a user (pending or accepted)
    public function getRequests($userId, $type = 'pending') {
        $this->db->where('receiver_id', $userId);
        $this->db->where('status', $type);
        return $this->db->get('friend_requests')->result_array();
    }
    public function checkExistingRequest($userId, $receiverId){
        $this->db->where('user_id', userId);
        $this->db->where('receiver_id', $recieverId);

        return $this->db->get('friend_requests')->result_array();
    }

    // Respond to Friend Request (accept/reject)
    public function respondRequest($requestId, $status) {
        $data = ['status' => $status];
        $this->db->where('id', $requestId);
        return $this->db->update('friend_requests', $data);
    }

    // Get the Friends list of a user
    public function getFriendsList($userId) {
        $this->db->where('user_id', $userId);
        return $this->db->get('friends')->result_array();
    }

    // Add a user to the Friends table
    // public function addFriend($data) {
    //     return $this->db->insert('friends', $data);
    // }
}
?>
