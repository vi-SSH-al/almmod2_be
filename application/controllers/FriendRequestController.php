
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
    // send data i form-data
    //new one send as json
    public function sendRequest() {

        // $data = $this->input->post();
        $data = json_decode(file_get_contents('php://input'), true); // Get raw JSON data
        //$request_id = $data['receiver_id']; // Access the 'receiver_id' value from the associative array
        //$sender_id = $data['sender_id']; // Access the 'sender_id' value from the associative array

        // Validate input
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('sender_id', 'Sender ID', 'required|numeric');
        $this->form_validation->set_rules('receiver_id', 'Receiver ID', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'There has been an error in the input: ' . validation_errors()]));
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

    // send data in query params
    public function getRequests() {
        $type = $this->input->get('type') ?: 'pending';
        
//        $data = $this->input->get() || json_decode(file_get_contents('php://input'), true);
        if ($this->input->get()) {
            // If query parameters exist, use them
            $data = $this->input->get();
        } else {
            // If no query parameters, check the body (JSON).
            $data = json_decode(file_get_contents('php://input'), true);
        }
        $userId = $data['user_id'];
        if (!$userId) {

            return $this->output->set_status_header(400)
                                ->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid user ID.','data'=>$data]));
        }

        $requests = $this->FriendRequestModel->getRequests($userId, $type);

        return $this->output->set_status_header(200)
                            ->set_content_type('application/json')
                            ->set_output(json_encode(['status' => 'success', 'data' => $requests,'user id '=> $userId]));
    }

    /**
     * Respond to Friend Request
     */
    //params required: requestid, 
    public function respondRequest() {
        $status = $this->input->post('status'); // accepted/rejected
        $requestId = $this->input->post('requestid'); // request id 
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
                                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid user ID.',"data is "=>$data2]));
        }

        $friends = $this->FriendRequestModel->getFriendsList($userId);

        return $this->output->set_status_header(200)
                            ->set_content_type('application/json')
                            ->set_output(json_encode(['status' => 'success', 'data' => $friends,"data2 is "=>$data2]));
    }
}
