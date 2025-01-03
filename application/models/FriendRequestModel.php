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

    // Get all Friend Requests for a user (pending )
    public function getRequests($userId, $type = 'pending') {
        $this->db->where('receiver_id', $userId);
        $this->db->where('status', $type);
        return $this->db->get('friend_requests')->result_array();
    }
    public function checkExistingRequest($userId, $recieverId){
        $this->db->where('sender_id', $userId);
        $this->db->where('receiver_id', $recieverId);

        return $this->db->get('friend_requests')->result_array();
    }

    // Respond to Friend Request (accept/reject)
    public function respondRequest($requestId, $status) {
        $data = ['status' => $status];
        $this->db->where('id', $requestId);
        return $this->db->update('friend_requests', $data);
    }

    public function getRequestbyId($requestId){
        $this->db->where('id', $requestId);
        return $this->db->get('friend_requests')->result_array();
    }
    // Get the Friends list of a user
    public function getFriendsList($userId) {
       // $this->db->group_start(); // Start a grouping for OR condition
        $this->db->where('sender_id', $userId);
        $this->db->or_where('receiver_id', $userId);
        //$this->db->group_end(); // End the OR grouping
        
        // Apply the AND condition for the status
        $this->db->where('status', "accepted");
        
        // Run the query and return the results
        return $this->db->get('friend_requests')->result_array();
    }

    // Add a user to the Friends table
    // public function addFriend($data) {
    //     return $this->db->insert('friends', $data);
    // }
}
?>
