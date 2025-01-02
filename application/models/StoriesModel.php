<?php
class StoriesModel extends CI_Model {

    public function __construct(){ 
        parent::__construct(); 
        $this->load->database();  
    } 
    // Upload a Story
    public function uploadStory($data) {
        return $this->db->insert('stories', $data);
    }

    // Get visible Stories for a user
    public function getStories($userId) {
        $this->db->where('user_id', $userId);
        $this->db->where('expires_at >', date('Y-m-d H:i:s'));
        return $this->db->get('stories')->result_array();
    }

    // Mark a story as viewed
    public function markAsViewed($storyId, $viewerId) {
        $data = [
            'story_id' => $storyId,
            'viewer_id' => $viewerId,
            'viewed_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('story_views', $data);
    }

    // Add or update reaction to a Story
    public function reactToStory($data) {
        // Check if the user already reacted to the story
        $this->db->where('story_id', $data['story_id']);
        $this->db->where('user_id', $data['user_id']);
        $existingReaction = $this->db->get('story_reactions')->row_array();

        if ($existingReaction) {
            // Update reaction
            $this->db->where('id', $existingReaction['id']);
            return $this->db->update('story_reactions', $data);
        } else {
            // Insert new reaction
            return $this->db->insert('story_reactions', $data);
        }
    }

    // Delete expired stories
    public function deleteExpiredStories() {
        $this->db->where('expires_at <', date('Y-m-d H:i:s'));
        return $this->db->delete('stories');
    }
}
?>
