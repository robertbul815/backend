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
$config['user_new_password_length'] = 7;

$config['limit_posts_per_user'] = 50;

$config['limit_query_featured_page'] = 50;
$config['limit_hours_featured_page'] = 72; // hours

$config['limit_query_search_default'] = 50;
$config['http_timeout_default'] = 15;

$config['feedback_receiver_email'] = 'georgepdimopoulos@gmail.com';
//$config['feedback_receiver_email'] = 'davidboot222@yahoo.com';

date_default_timezone_set('Europe/London');