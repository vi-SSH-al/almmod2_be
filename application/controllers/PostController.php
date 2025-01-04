<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');  // Allow all origins
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');  // Allow these methods
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
class PostsController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('PostsModel');
        $this->load->model('NotificationModel');
        $this->load->model('FriendRequestModel');
        $this->load->helper('url');
        $this->load->library('upload');
    }

    // Create a new post

    // In case of multiple mb_encoding_aliases
    //$_FILES['userfiles'] = array(
    //     'name'     => array('file1.jpg', 'file2.mp4'),   // Array of original file names
    //     'type'     => array('image/jpeg', 'video/mp4'),   // Array of MIME types
    //     'tmp_name' => array('/tmp/phpYzdqk1', '/tmp/phpYzdqk2'),  // Array of temporary file paths
    //     'error'    => array(0, 0),                        // Array of error codes (0 means no error)
    //     'size'     => array(123456, 789012),              // Array of file sizes in bytes
    // );
    
    public function createPost() {

        // getting user Id from session
        //$user_id = $this->session->userdata('user_id');

        $userId = $this->input->post('user_id');
        $content = $this->input->post('content');
        $mediaUrls = [];

        if (!$userId) {
            return $this->output->set_content_type('application/json')
                                ->set_output(json_encode(['status' => 'error', 'message' => 'User ID is required']));
        }

        // Handle media upload
        if (!empty($_FILES['media'])) {
            $files = $_FILES['media'];
           

            for ($i = 0; $i < count($files['name']); $i++) {
                $_FILES['file']['name']     = $files['name'][$i];
                $_FILES['file']['type']     = $files['type'][$i];
                $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['file']['error']    = $files['error'][$i];
                $_FILES['file']['size']     = $files['size'][$i];

                $config['upload_path']   = './assets/posts/';
                $config['allowed_types'] = 'jpg|jpeg|png|mp4|pdf|docx';
                $config['max_size']      = 20480; // 20MB
                $config['file_name']     = uniqid() . '_' . $_FILES['file']['name'];

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('file')) {
                    return $this->output->set_content_type('application/json')
                                        ->set_output(json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]));
                }

                $mediaData = $this->upload->data();
                $mediaUrls[] = 'assets/posts/' . $mediaData['file_name'];
            }
        }

        // Save post and media
        $response = $this->PostsModel->createPost($userId, $content, $mediaUrls);

        $frd = $this->FriendRequestModel->getFriendsList($userId);
        if($response){
            $notification = [
                'user_id' => $frd,
                'message' => "You have a new friend request.",
             
            ];
            $this->NotificationModel->addNotificationforPost($notification);

            return $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
        }
     
    }

    // Delete a post
    public function deletePost($postId) {
        $userId = $this->input->post('user_id');
        $response = $this->PostsModel->deletePost($postId, $userId);

        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
    }

    // Get paginated feed
    public function getFeed() {
        $offset = $this->input->get('offset') ?: 0;
        $sort = $this->input->get('sort') ?: 'recent'; // recent/oldest
        $response = $this->PostsModel->getFeed($offset, $sort);

        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // Like a post
    public function likePost($postId) {
        // take useriD from session
         //$userId = $this->session->userdata('user_id');
        $userId = $this->input->post('user_id');
        $response = $this->PostsModel->likePost($postId, $userId);

        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    // Add a comment
    public function addComment($postId) {

         // getting user Id from session
        //$user_id = $this->session->userdata('user_id');
        $user_id = $this->input->post('user_id');

        $commentData = [
            'post_id' => $postId,
            'user_id' => $user_id,
            'parent_comment_id' => $this->input->post('parent_comment_id'),
            'content' => $this->input->post('content')
        ];

        $response = $this->PostsModel->addComment($commentData);

        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
    }

    // get comments of a postid
    public function getComments($postId){
        $response = $this->postModel->getComments($postId);
        return $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
    }
    // // Get notifications
    // public function getNotifications() {
    //     $userId = $this->input->get('user_id');
    //     $response = $this->PostsModel->getNotifications($userId);

    //     return $this->output->set_content_type('application/json')
    //                         ->set_output(json_encode($response));
    // }
}
?>
