
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FriendRequestController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('FriendRequestModel');
        $this->load->model('NotificationModel');
        $this->load->library('form_validation'); // For input validation
    }

    /**
     * Send a Friend Request
     */
    // fields required: sender_id, receiver_id
    public function sendRequest() {


       
        $data = $this->input->post();

        // Validate input
    
        $this->form_validation->set_rules('sender_id', 'Sender ID', 'required|numeric');
        $this->form_validation->set_rules('receiver_id', 'Receiver ID', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'data'=>$data,'message' => validation_errors()]));
        }

        // Check if a request already exists
        if ($this->FriendRequestModel->checkExistingRequest($data['sender_id'], $data['receiver_id'])) {
            return $this->output->set_status_header(409)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Friend request already exists.']));
        }

        // Send friend request
        $response = $this->FriendRequestModel->sendRequest($data);

        if ($response) {
            // Add Notification
            $notification = [
                'user_id' => $data['receiver_id'],
                'message' => "You have a new friend request.",
                'link' => base_url('profile/' . $data['sender_id']),
            ];
            $this->NotificationModel->addNotification($notification);

            return $this->output->set_status_header(200)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'success', 'message' => 'Friend request sent successfully.']));
        } else {
            return $this->output->set_status_header(500)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to send friend request.']));
        }
    }


    /**
     * Get all Friend Requests for a user
     */ 
    // params required: receiver_id  (current userid), status(optional) bydefault: pending
    public function getRequests() {
        $type = $this->input->get('type') ?: 'pending';
        
        $userId = $this->input->get('userId');
        if ($userId) {

            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid user ID.']));
        }

        $requests = $this->FriendRequestModel->getRequests($userId, $type);

        return $this->output->set_status_header(200)
                            ->set_content_type('application/json')
                            ->set_output(json_encode(['status' => 'success', 'data' => $requests]));
    }

    /**
     * Respond to Friend Request
     */
    //params required: requestid, 
    public function respondRequest($requestId) {
        $status = $this->input->post('status'); // accepted/rejected
      
        if (!in_array($status, ['accepted', 'rejected'])) {
            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid status value.']));
        }

        $response = $this->FriendRequestModel->respondRequest($requestId, $status);

        if ($response) {
            if ($status === 'accepted') {
                $notification = [
                    'user_id' => $this->input->post('receiver_id'),
                    'message' => "You are now friends with " . $this->input->post('sender_name'),
                    'link' => base_url('profile/' . $this->input->post('sender_id')),
                ];
                $this->NotificationModel->addNotification($notification);
    
                return $this->output->set_status_header(200)
                                    ->set_content_type('application/json')
                                    ->set_output(json_encode(['status' => 'success', 'message' => 'Request processed successfully.']));
            }
        } else {
            return $this->output->set_status_header(500)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to process request.']));
        }
    }

    /**
     * Get Friend List of User
     */
    public function getFriends($userId) {
        if (!is_numeric($userId)) {
            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid user ID.']));
        }

        $friends = $this->FriendRequestModel->getFriendsList($userId);

        return $this->output->set_status_header(200)
                            ->set_content_type('application/json')
                            ->set_output(json_encode(['status' => 'success', 'data' => $friends]));
    }
}
