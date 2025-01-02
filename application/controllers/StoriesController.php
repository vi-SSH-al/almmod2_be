<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StoriesController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('StoriesModel');
        $this->load->model('NotificationModel');
    }

    public function uploadStory() {
        // Get POST data from the request
        $data = $this->input->post();
    
        // Call model method to upload the story
        $response = $this->StoriesModel->uploadStory($data);
    
        // Set the response content type to JSON
        $this->output->set_content_type('application/json');
    
        // Check if the response is successful
        if ($response) {
            // Return success response
            $this->output->set_output(json_encode(['status' => 'success']));
        } else {
            // Return error response
            $this->output->set_output(json_encode(['status' => 'error']));
        }
    }
    

    // Get Stories of a User
    public function getStories($userId) {
        // Get stories for the user
        $stories = $this->StoriesModel->getStories($userId);
    
        // Set the response content type to JSON
        $this->output->set_content_type('application/json');
    
        // Return the stories as a JSON response
        $this->output->set_output(json_encode($stories));
    }
    

    // Mark Story as Viewed
    public function markStoryAsViewed($storyId) {
        // Get viewer ID from POST data
        $viewerId = $this->input->post('viewer_id');
    
        // Mark the story as viewed in the model
        $this->StoriesModel->markAsViewed($storyId, $viewerId);
    
        // Set the response content type to JSON
        $this->output->set_content_type('application/json');
    
        // Return success response
        $this->output->set_output(json_encode(['status' => 'success']));
    }
    

    // React to Story
    public function reactToStory($storyId) {
        // Get reaction data from POST
        $reactionData = $this->input->post();
        $reactionData['story_id'] = $storyId;
    
        // Call model method to record the reaction
        $response = $this->StoriesModel->reactToStory($reactionData);
    
        // Set the response content type to JSON
        $this->output->set_content_type('application/json');
    
        // Return the response based on the outcome
        if ($response) {
            $this->output->set_output(json_encode(['status' => 'success']));
        } else {
            $this->output->set_output(json_encode(['status' => 'error']));
        }
    }
    

    // Delete Expired Stories
    public function deleteExpiredStories() {
        // Call model method to delete expired stories
        $this->StoriesModel->deleteExpiredStories();
    
        // Set the response content type to JSON
        $this->output->set_content_type('application/json');
    
        // Return success response
        $this->output->set_output(json_encode(['status' => 'success']));
    }
    
}
?>
