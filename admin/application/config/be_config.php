<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// API Auth
$config['api_key'] = 'f1e3c46751ecd6e4cff77ffd948794cc';

// Messages
$config['msg_fill_form'] = 'Please fill the form correctly';

// Paths
$config['path_media_users'] = 'assets/media/users/';
$config['path_media_posts'] = 'assets/media/posts/';
$config['path_media_post_thumbs'] = 'assets/media/post_thumbs/';

$config['media_user_self_domain_prefix'] = 'tr_media_user_';
$config['media_post_self_domain_prefix'] = 'tr_media_post_';
$config['media_post_thumb_self_domain_prefix'] = 'tr_media_post_thumb_';

// Settings
$config['user_auth_salt'] = 'f1e3c46751ecd6e4cff77ffd948794cc';
$config['limit_posts_per_user'] = 50;

$config['limit_query_featured_page'] = 50;
$config['limit_hours_featured_page'] = 72; // hours

$config['limit_query_search_default'] = 50;
$config['http_timeout_default'] = 15;

//$config['feedback_receiver_email'] = 'georgepdimopoulos@gmail.com';
$config['feedback_receiver_email'] = 'davidboot222@yahoo.com';



$config['domain_sub_directory'] = 'thetrainapp.com';
//$config['domain_sub_directory'] = '';

$config['path_uploads_temp'] = 'assets/file_upload/uploads/';
$config['file_upload_imagename_separator'] = '*.*';

$config['msg_success'] = 'Transaction Success!';
$config['msg_failed'] = 'Transaction Failed!';

$config['user_role'] = array(
	'1' => array('name' => 'subscriber', 'val' => 1, 'title' => 'Subscriber'),
	'5' => array('name' => 'member', 'val' => 5, 'title' => 'Member'),
	'50' => array('name' => 'admin', 'val' => 50, 'title' => 'Administrator'),
	'100' => array('name' => 'superadmin', 'val' => 100, 'title' => 'Super Administrator')
);
