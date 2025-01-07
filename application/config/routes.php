<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = 'welcome';

$route['translate_uri_dashes'] = FALSE;
$route['404_override'] = '';
// Friend Requests
$route['send-friend-request'] = 'FriendRequestController/sendRequest';
$route['get-requests/(:num)'] = 'FriendRequestController/getRequests/$1';
// $route['respond-friend-request'] = 'FriendRequestController/respondRequest';
$route['rfr'] = 'FriendRequestController/respondRequest';
$route['get-friends/(:num)'] = 'FriendRequestController/getFriends/$1';

// Stories
$route['upload-story'] = 'StoriesController/uploadStory';
$route['get-stories/(:num)'] = 'StoriesController/getStoriesofUser/$1';
$route['mark-story-viewed/(:num)'] = 'StoriesController/markStoryAsViewed/$1';
$route['react-to-story/ (:num)'] = 'StoriesController/reactToStory/$1';
$route['getFriendsStories/(:num)'] = 'StoriesController/getFriendsStories/$1';
$route['delete-expired-stories'] = 'StoriesController/deleteExpiredStories';


// Posts-related routes
$route['posts/create'] = 'PostController/createPost';  // POST: Create a new post
$route['posts/delete/(:num)'] = 'PostController/deletePost/$1';  // POST: Delete a post by post ID
$route['posts/feed'] = 'PostController/getFeed';  // GET: Get paginated feed
$route['posts/like/(:num)'] = 'PostController/likePost/$1';  // POST: Like a post by post ID
$route['posts/comment/(:num)'] = 'PostController/addComment/$1';  // POST: Add a comment to a post by post ID
$route['posts/getcomments/(:num)'] = 'PostController/getComments/$1'; // Get all comments of a post by postIdś
 
$route['get-notifications/(:num)'] = 'NotificationController/getNotificationofUser/$1';
$route['api/employees'] = 'DemoController/get_all_employees';
// $route['404_override'] = '';
// Add more routes as needed
/* End of file routes.php */
/* Location: ./application/config/routes.php */