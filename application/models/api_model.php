<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends CI_Model {

    public function __construct() {
        
    }

    public function user_signup($params) {
        $result = array();
        $data = array();
        $status = 0;
        $msg = '';
        $request_field_prefix = 'user_';
        
        if((int)$params['signup_mode'] == 2) {
	        $query = $this->db->get_where('tr_users', array('user_facebook_id' => $params['user_facebook_id']));
	        if ($query->num_rows() > 0) {
	        	$current_user = $query->row_array();
	        	$request_fields_update = array('facebook_id', 'email', 'first_name', 'last_name', 'apns_id', 'gcm_id');
	        	foreach ($request_fields_update as $request_field) {
	        		$request_field_new = $request_field_prefix . $request_field;
	        		if(isset($params[$request_field_new])) {
	        			$data[$request_field_new] = $params[$request_field_new];
	        		}
	        	}
	        	$data['user_full_name'] = $data['user_first_name'];
	        	if($data['user_last_name'] != '') $data['user_full_name'] .= (' ' . $data['user_last_name']);
	        	if($data['user_full_name'] == '') $data['user_full_name'] = $data['user_name'];
	        	$update_date = date('Y-m-d H:i:s');
	        	$data['user_last_updated_date'] = $update_date;
	        	$this->db->update('tr_users', $data, array('user_id' => $current_user['user_id']));
	        	$result = $this->get_user_detail($current_user['user_id']);
	        	$status = 1;
	        } else {
	        	$request_fields = array('facebook_id', 'email', 'first_name', 'last_name', 'photo_url', 'apns_id', 'gcm_id');
	        	foreach ($request_fields as $request_field) {
	        		$request_field_new = $request_field_prefix . $request_field;
	        		if(isset($params[$request_field_new])) {
	        			$data[$request_field_new] = $params[$request_field_new];
	        		}
	        	}
	        	$data['user_full_name'] = $data['user_first_name'];
	        	if($data['user_last_name'] != '') $data['user_full_name'] .= (' ' . $data['user_last_name']);
	        	if(isset($data['user_full_name']) && $data['user_full_name'] != '') {
	        		$data['user_name'] = strtolower($this->remove_characters(array('\n', '\r', ' ', ')', '('), $data['user_full_name']));
	        	} else {
	        		$data['user_name'] = time();
	        	}
	        	$signup_date = date('Y-m-d H:i:s');
	        	$data['user_signup_date'] = $signup_date;
	        	$data['user_last_updated_date'] = $signup_date;
	        	$this->db->insert('tr_users', $data);
	        	$insert_id = $this->db->insert_id();
	        	if (isset($insert_id) && $insert_id > 0) {
	        		// Check if settled username already exists in the table and change it.
	        		$query = $this->db->get_where('tr_users', array('user_name' => $data['user_name']));
	        		if($query->num_rows() > 1) {
	        			$data['user_name'] .= $insert_id;
	        			if($data['user_full_name'] == '') $data['user_full_name'] = $data['user_name'];
	        			$this->db->update('tr_users', array('user_name' => $data['user_name'], 'user_full_name' => $data['user_full_name']), array('user_id' => $insert_id));
	        		}
	        		$this->db->insert('tr_follows', array('follow_user_id' => $insert_id));
	        		$this->db->insert('tr_likes', array('like_user_id' => $insert_id));
	        		$this->db->insert('tr_blocks', array('block_user_id' => $insert_id));
	        		$result = $this->get_user_detail($insert_id);
	        		$status = 1;
	        	} else {
	        		$status = 3;
	        		$msg = 'User sign up Failed';
	        	}
	        }
        } else if((int)$params['signup_mode'] == 1) {
        	// Manual Sign Up
        	$request_fields = array('email', 'password', 'first_name', 'last_name', 'apns_id', 'gcm_id', 'photo_url');
        	foreach ($request_fields as $request_field) {
        		$request_field_new = $request_field_prefix . $request_field;
        		if(isset($params[$request_field_new])) {
        			$data[$request_field_new] = $params[$request_field_new];
        		}
        	}
        	 
        	$query_user_email = $this->db->get_where('tr_users', array('user_email' => $data['user_email']));
        	if($query_user_email->num_rows() > 0) {
        		$status = 3;
        		$msg = 'This email is already registered';
        	} else {
        		 
        		$data['user_full_name'] = $data['user_first_name'];
        		if($data['user_last_name'] != '') $data['user_full_name'] .= (' ' . $data['user_last_name']);
        		 
        		if(isset($data['user_full_name']) && $data['user_full_name'] != '') {
        			$data['user_name'] = strtolower($this->remove_characters(array('\n', '\r', ' ', ')', '('), $data['user_full_name']));
        		} else {
        			$data['user_name'] = time();
        		}
        		
        		$data['user_password'] = $this->get_user_auth_salt($data['user_password']);
        		
        		$data['user_photo_url'] = '';

        		$signup_date = date('Y-m-d h:i:s');
        		$data['user_signup_date'] = $signup_date;
        		$data['user_last_updated_date'] = $signup_date;
        		 
        		$this->db->insert('tr_users', $data);
        		$insert_id = $this->db->insert_id();
        		
        		if (isset($insert_id) && $insert_id > 0) {
        			 
        			// Check if settled username already exists in the table and change it.
        			$query = $this->db->get_where('tr_users', array('user_name' => $data['user_name']));
        			if($query->num_rows() > 1) {
        				$data['user_name'] .= $insert_id;
        				$this->db->update('tr_users', array('user_name' => $data['user_name']), array('user_id' => $insert_id));
        			}
        			$this->db->insert('tr_follows', array('follow_user_id' => $insert_id));
        			$this->db->insert('tr_likes', array('like_user_id' => $insert_id));
        			$this->db->insert('tr_blocks', array('block_user_id' => $insert_id));
        			$result = $this->get_user_detail($insert_id);
        			$status = 1;
        		} else {
        			$status = 2;
        			$msg = 'User sign up Failed';
        		}
        	}
        }
        $result['status'] = $status;
        $result['msg'] = $msg;
        return $result;
    }
    
    public function get_user_auth_salt($password) {
    	return sha1(config_item('user_auth_salt') . md5($password));
    }
    
    public function user_login($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	$query = $this->db->select('user_id')->get_where('tr_users', array('user_email' => $params['user_email'], 'user_password' => $this->get_user_auth_salt($params['user_password'])));
    	if($query->num_rows() > 0) {
    		$result = $this->get_user_detail(element('user_id', $query->row_array()));
    		if(isset($params['user_gcm_id']) && $params['user_gcm_id'] != '') {
    			$this->db->update('tr_users', array('user_gcm_id' => $params['user_gcm_id']), array('user_email' => $params['user_email']));
    		}
    		if(isset($params['user_apns_id']) && $params['user_apns_id'] != '') {
    			$this->db->update('tr_users', array('user_apns_id' => $params['user_apns_id']), array('user_email' => $params['user_email']));
    		}
    		$status = 1;
    	} else {
    		$status = 2;
    		$msg = 'Email and password do not match';
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }

    public function user_retrieve_password($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	$query = $this->db->get_where('tr_users', array('user_email' => $params['user_email']));
    	if($query->num_rows() > 0) {
    
    		$current_user = $query->row_array();
    
    		$digits = config_item('user_new_password_length');
    		$new_password = rand(pow(10, $digits-1), pow(10, $digits)-1);
    			
    		// Send Mail
    		$to = $params['user_email'];
    
    		$subject = 'Train - Password Reset';
    			
    		$message = '
    		Thanks for trying Train ' . (($current_user['user_first_name'] != '') ? $current_user['user_first_name'] : $current_user['user_name']) . '!<br>
    		<br>
    		You can now login using the credentials below:<br>
    		<br>
    		<b>Email Address:</b><br>
    		' . $current_user['user_email'] . '<br>
    		<br>
    		<b>Password:</b><br>
    		' . $new_password . '<br>
    		<br><br>
    		Enjoy Train!<br>
    		';
    
    		// Always set content-type when sending HTML email
    		$headers = "MIME-Version: 1.0" . "\r\n";
    		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    			
    		// More headers
    		$headers .= 'From: <no-reply@thetrainapp.com>' . "\r\n";
    		//$headers .= 'Cc: cc@example.com' . "\r\n";
    			
    		if(mail($to, $subject, $message, $headers)) {
    			$status = 1;
    		} else {
    			$status = 3;
    			$msg = 'An error occurred while resetting a new password.';
    		}
    			
    		if($status == 1) {
    			$this->db->update('tr_users', array('user_password' => $this->get_user_auth_salt(md5($new_password))), array('user_email' => $params['user_email']));
    		}
    
    	} else {
    		$status = 2;
    		$msg = 'Your requested email is not registered to our system.';
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function user_profile_update($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
    	$param_empty = true;
    	$request_fields = array();
    	$request_field_prefix = 'user_';
    	$query = $this->db->get_where('tr_users', array('user_id' => $params['user_id']));
    	if($query->num_rows() > 0) {
    		$query_user_name = $this->db->select('user_name')->get_where('tr_users', array('LOWER(user_name)' => strtolower($params['user_name']), 'user_id !=' => $params['user_id']));
    		if($query_user_name->num_rows() > 0) {
    			$status = 2;
    			$msg = 'That user name is already taken. Try with different user name.';
    		} else {
    			$request_fields_update = array('name', 'full_name', 'bio');
    			
    			foreach($request_fields_update as $request_field) {
    				$request_field_new = $request_field_prefix . $request_field;
    				if(isset($params[$request_field_new])) {
    					$data[$request_field_new] = $params[$request_field_new];
    					$param_empty = false;
    				}
    			}
    			if(!$param_empty) {
    				$status = 1;
    			}
    			// User Photo Update
    			if(isset($params['user_photo_data']) && $params['user_photo_data'] != '') {
    				$image_path = config_item('path_media_users');
    				$created_time = time();
    				$image_name = $params['user_id'] . '_' . $created_time . '.jpg';
    				$image_url = $image_path . $image_name;
    				$binary = base64_decode($params['user_photo_data']);
    				header('Content-Type: bitmap; charset=utf-8');
    				$file = fopen($image_url, 'w');
    				if($file) {
    					fwrite($file, $binary);
    				} else {
    					$status = 3;
    					$msg = 'File Upload failed';
    				}
    				fclose($file);
    				if($status < 2) {
    					$query_photo_exist = $this->db->select('user_photo_url')->get_where('tr_users', array('user_id' => $params['user_id']));
    					if($query_photo_exist->num_rows() > 0 && element('user_photo_url', $query_photo_exist->row_array()) != '') {
    						$user_pic_media = config_item('path_media_users') . $this->get_user_prefix_removed_media_name(element('user_photo_url', $query_photo_exist->row_array()));
    						if(file_exists($user_pic_media)) unlink($user_pic_media);
    					}
    					$status = 1;
    					$data['user_photo_url'] = config_item('media_user_self_domain_prefix') . $image_name;
    				}
    			}
    		}
    		if($status == 1) {
    			$update_date = date('Y-m-d H:i:s');
    			$data['user_last_updated_date'] = $update_date;
    			$this->db->update('tr_users', $data, array('user_id' => $params['user_id']));
    			
    			$this->db->update('tr_posts', array('post_user_name' => $data['user_name']), array('post_user_id' => $params['user_id']));
    			$this->db->update('tr_comments', array('comment_user_name' => $data['user_name']), array('comment_user_id' => $params['user_id']));
    			
    			if(isset($data['user_photo_url']) && $data['user_photo_url'] != '') {
    				$this->db->update('tr_posts', array('post_user_photo_url' => $data['user_photo_url']), array('post_user_id' => $params['user_id']));
    				$this->db->update('tr_comments', array('comment_user_photo_url' => $data['user_photo_url']), array('comment_user_id' => $params['user_id']));
    			}
    			
    			$result = $this->get_user_detail($params['user_id']);
    			$status = 1;
    		}
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function user_change_password($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';

    	$query = $this->db->select('user_password')->get_where('tr_users', array('user_id' => $params['user_id']));
    	if($query->num_rows() > 0) {
    		if(element('user_password', $query->row_array()) != $this->get_user_auth_salt($params['user_password_old'])) {
    			$status = 2;
    			$msg = 'Your old password is wrong.';
    		} else {
    			$data['user_password'] = $this->get_user_auth_salt($params['user_password_new']);
    			$update_date = date('Y-m-d H:i:s');
    			$data['user_last_updated_date'] = $update_date;
    			$this->db->update('tr_users', $data, array('user_id' => $params['user_id']));
    			$result = $this->get_user_detail($params['user_id']);
    			$status = 1;
    		}
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function user_logout($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	
    	if((int)$params['device_type'] == 1) {
    		$this->db->update('tr_users', array('user_apns_id' => ''), array('user_id' => $params['user_id']));
    	} else if((int)$params['device_type'] == 2) {
    		$this->db->update('tr_users', array('user_gcm_id' => ''), array('user_id' => $params['user_id']));
    	}
    	
    	$status = 1;
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function reference_user_detail($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	
    	$block_ref_ids = $this->get_block_ref_ids($params['user_id']);
    	if(in_array($params['ref_user_id'], $block_ref_ids)) {
    		$msg = 'This user is unavailable';
    		$status = 2;
    	} else {
	    	$result = $this->get_user_detail($params['user_id']);
	    	$ref_user = $this->get_user_info($params['ref_user_id']);
	    	$query_is_follow = $this->db->like('follow_ids', ',' . $ref_user['user_id'] . ';')->get_where('tr_follows', array('follow_user_id' => $params['user_id']));
	    	$ref_user['is_following'] = ($query_is_follow->num_rows() > 0) ? '1' : '0';
	    	$result['ref_user'] = $ref_user;
	    	$status = 1;
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function home($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    	$result = $this->get_user_detail($params['user_id']);
    	
    	$status = 1;
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function activities($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
    	 
    	$result = $this->get_user_detail($params['user_id']);
    	
    	$activities = array();
    	
    	// Activity Type 1 : "xxx is following you"
    	$query = $this->db->select(array('follow_id', 'user_id', 'user_name', 'user_photo_url', 'follow_ids'))
	    	->like('tr_follows.follow_ids', ',' . $params['user_id'] . ';0;')
	    	->join('tr_users', 'tr_users.user_id = tr_follows.follow_user_id')
	    	->get('tr_follows');
    	foreach($query->result_array() as $row) {
    		$activity = array();
    		$activity['activity_type'] = 1;
    		$activity['activity_ref_id'] = $row['follow_id'];
    		$activity['activity_user_id'] = $row['user_id'];
    		$activity['activity_user_name'] = $row['user_name'];
    		$activity['activity_user_photo_url'] = $row['user_photo_url'];
    		$activity_time = date('Y-m-d H:i:s');
    		foreach(explode(',', $this->get_ids_str_trimmed($row['follow_ids'])) as $follow_item) {
    			$follow_item_arr = explode(';', $follow_item);
    			if($follow_item_arr[0] == $params['user_id']) {
    				$activity_time = $follow_item_arr[2];
    				break;
    			}
    		}
    		$activity['activity_time_diff'] = strtotime(date('Y-m-d H:i:s')) - strtotime($activity_time);
    		$activities[] = $activity;
    	}
    	
    	// Activity Type 2 : "xxx liked your workout"
    	foreach($result['my_posts'] as $my_post) {
	    	$query = $this->db->select(array('like_id', 'user_id', 'user_name', 'user_photo_url', 'like_ids'))
	    		->like('tr_likes.like_ids', ',' . $my_post['post_id'] . ';0;')
	    		->join('tr_users', 'tr_users.user_id = tr_likes.like_user_id')
	    		->get('tr_likes');
	    	foreach($query->result_array() as $row) {
	    		$activity = array();
	    		$activity['activity_type'] = 2;
	    		$activity['activity_ref_id'] = $row['like_id'];
	    		$activity['activity_post_id'] = $my_post['post_id'];
	    		$activity['activity_user_id'] = $row['user_id'];
	    		$activity['activity_user_name'] = $row['user_name'];
	    		$activity['activity_user_photo_url'] = $row['user_photo_url'];
	    		$activity_time = date('Y-m-d H:i:s');
	    		foreach(explode(',', $this->get_ids_str_trimmed($row['like_ids'])) as $like_item) {
	    			$like_item_arr = explode(';', $like_item);
	    			if($like_item_arr[0] == $params['user_id']) {
	    				$activity_time = $like_item_arr[2];
	    				break;
	    			}
	    		}
	    		$activity['activity_time_diff'] = strtotime(date('Y-m-d H:i:s')) - strtotime($activity_time);
	    		$activities[] = $activity;
	    	}
    	}
    	
    	// Activity Type 3 : "xxx commented on your workout"
    	foreach($result['my_posts'] as $my_post) {
	    	$query = $this->db->select(array('comment_id', 'user_id', 'user_name', 'user_photo_url', 'comment_date'))
	    		->where(array('comment_post_id' => $my_post['post_id'], 'comment_activity_done' => 0))
	    		->join('tr_users', 'tr_users.user_id = tr_comments.comment_user_id')
	    		->get('tr_comments');
	    	foreach($query->result_array() as $row) {
	    		$activity = array();
	    		$activity['activity_type'] = 3;
	    		$activity['activity_ref_id'] = $row['comment_id'];
	    		$activity['activity_user_id'] = $row['user_id'];
	    		$activity['activity_user_name'] = $row['user_name'];
	    		$activity['activity_user_photo_url'] = $row['user_photo_url'];
	    		$activity_time = $row['comment_date'];
	    		$activity['activity_time_diff'] = strtotime(date('Y-m-d H:i:s')) - strtotime($activity_time);
	    		$activities[] = $activity;
	    	}
    	}
    	
    	// Activity Type 4 : "xxx mentioned you in a comment"
    	$query = $this->db->select(array('comment_id', 'user_id', 'user_name', 'user_photo_url', 'comment_date'))
	    	->like('tr_comments.comment_tag_user_ids', ',' . $params['user_id'] . ';0,')
	    	->join('tr_users', 'tr_users.user_id = tr_comments.comment_user_id')
	    	->get('tr_comments');
    	foreach($query->result_array() as $row) {
    		$activity = array();
    		$activity['activity_type'] = 4;
    		$activity['activity_ref_id'] = $row['comment_id'];
    		$activity['activity_user_id'] = $row['user_id'];
    		$activity['activity_user_name'] = $row['user_name'];
    		$activity['activity_user_photo_url'] = $row['user_photo_url'];
    		$activity_time = $row['comment_date'];
    		$activity['activity_time_diff'] = strtotime(date('Y-m-d H:i:s')) - strtotime($activity_time);
    		$activities[] = $activity;
    	}
    	
    	function sortByTimeDiff($a, $b) {
    		return $b['activity_time_diff'] - $a['activity_time_diff'];
    	}
    	usort($activities, 'sortByTimeDiff');
    	
    	
    	for($i = 0; $i < count($activities); $i++) {
    		$activities[$i]['activity_id'] = $i + 1;
    	}
    	
    	$result['activities'] = $activities;
    	$status = 1;
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function activity_done($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
    
    	switch((int)$params['activity_type']) {
    		case 1:
    			$query = $this->db->get_where('tr_follows', array('follow_id' => $params['activity_ref_id']));
    			$ids_str = element('follow_ids', $query->row_array());
    			$new_ids_str = '';
    			if($ids_str != '') {
	    			foreach(explode(',', $this->get_ids_str_trimmed($ids_str)) as $id_item) {
	    				$id_item_array = explode(';', $id_item);
	    				if($new_ids_str == '') $new_ids_str = ',';
	    				if($id_item_array[0] == $params['user_id']) {
	    					$new_ids_str .= ($id_item_array[0] . ';1;' . $id_item_array[2] . ',');
	    				} else {
	    					$new_ids_str .= ($id_item . ',');
	    				}
	    			}
    			}
    			$this->db->update('tr_follows', array('follow_ids' => $new_ids_str), array('follow_id' => $params['activity_ref_id']));
    			$status = 1;
    			break;
    		case 2:
    			$query = $this->db->get_where('tr_likes', array('like_id' => $params['activity_ref_id']));
    			$ids_str = element('like_ids', $query->row_array());
    			$new_ids_str = '';
    			if($ids_str != '') {
    				foreach(explode(',', $this->get_ids_str_trimmed($ids_str)) as $id_item) {
    					$id_item_array = explode(';', $id_item);
    					if($new_ids_str == '') $new_ids_str = ',';
    					if($id_item_array[0] == $params['activity_post_id']) {
    						$new_ids_str .= ($id_item_array[0] . ';1;' . $id_item_array[2] . ',');
    					} else {
    						$new_ids_str .= ($id_item . ',');
    					}
    				}
    			}
    			$this->db->update('tr_likes', array('like_ids' => $new_ids_str), array('like_id' => $params['activity_ref_id']));
    			$status = 1;
    			break;
    		case 3:
    			$this->db->update('tr_comments', array('comment_activity_done' => 1), array('comment_id' => $params['activity_ref_id']));
    			$status = 1;
    			break;
    		case 4:
    			$query = $this->db->get_where('tr_comments', array('comment_id' => $params['activity_ref_id']));
    			$ids_str = element('comment_tag_user_ids', $query->row_array());
    			$new_ids_str = '';
    			if($ids_str != '') {
    				foreach(explode(',', $ids_str) as $id_item) {
    					$id_item_array = explode(';', $id_item);
    					if($new_ids_str == '') $new_ids_str = ',';
    					if($id_item_array[0] == $params['user_id']) {
    						$new_ids_str .= ($id_item_array[0] . ';1,');
    					} else {
    						$new_ids_str .= ($id_item . ',');
    					}
    				}
    			}
    			$this->db->update('tr_comments', array('comment_tag_user_ids' => $new_ids_str), array('comment_id' => $params['activity_ref_id']));
    			$status = 1;
    			break;
    		default:
    			$status = 0;
    			break;
    	}
    	
    	if($status == 1) {
    		$result = $this->activities($params);
    	}
    
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function discover($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
		$keyword_search_results = array();
		$search_type = (isset($params['search_type']) && $params['search_type'] != '' && (int)$params['search_type'] > 0) ? (int)$params['search_type'] : 0;
		$query_offset = (isset($params['search_offset']) && $params['search_offset'] != '' && (int)$params['search_offset'] > 0) ? (int)$params['search_offset'] : 0;
		$query_limit = (isset($params['search_limit']) && $params['search_limit'] != '' && (int)$params['search_limit'] > 0) ? (int)$params['search_limit'] : config_item('limit_query_search_default');
		$result = $this->get_user_detail($params['user_id']);

		// Get Blocked ids
		$block_ref_ids = $this->get_block_ref_ids($params['user_id']);
	
		$param_posts_array = array('post_id', 'post_thumb_url', 'post_video_url', 'post_caption', 'post_date', 'post_category_id',
				'post_user_id', 'post_user_name', 'post_user_photo_url',
				'post_liked_count', 'post_commented_count');
		
		/* $my_post_ids = array();
		foreach($result['user_posts'] as $row) {
			$my_post_ids[] = $row['post_id'];
		} */
		//$param_array = array();
		//$this->db->select($param_array);
		/* if(count($my_post_ids)) {
		 $this->db->where('post_id NOT IN (' . implode(',', $my_post_ids) . ')');
		} */
		
		if($search_type == 1) {
			
			if(!(isset($params['category_id']) && $params['category_id'] != '' && $params['category_id'] != '0')) {
				$status = 0;
			} else {
				$category_id = (int)$params['category_id'];
				
				$recent_posts = array();
				$this->db->select($param_posts_array);
				$this->db->where(array('post_category_id' => $category_id));
				if(count($block_ref_ids) > 0) {
					$this->db->where_not_in('post_user_id', $block_ref_ids);
				}
				$this->db->order_by('post_date', 'desc');
				$this->db->limit($query_limit, $query_offset);
				$query_recent_posts = $this->db->get('tr_posts');
				foreach($query_recent_posts->result_array() as $post) {
					$post_new = $post;
					$query_is_liked = $this->db->like('like_ids', ',' . $post['post_id'] . ';')->get_where('tr_likes', array('like_user_id' => $params['user_id']));
					$post_new['is_liked'] = ($query_is_liked->num_rows() > 0) ? 1 : 0;
					$post_new['post_likes'] = $this->get_post_likes($post['post_id'], $params['user_id']);
					$post_new['post_comments'] = $this->get_post_comments($post['post_id'], $params['user_id']);
					$post_new['post_time_diff'] = time() - strtotime($post['post_date']);
					$recent_posts[] = $post_new;
				}
				$result['recent_posts'] = $recent_posts;
				
				$featured_posts = array();
				$this->db->select($param_posts_array);
				$this->db->where('post_category_id = ' . $category_id . ' AND (TIMESTAMPDIFF(HOUR, post_featured_date, NOW()) <= ' . config_item('limit_hours_featured_page') . ' OR post_featured_date = \'1970-01-01 00:00:00\')');
				if(count($block_ref_ids) > 0) {
					$this->db->where_not_in('post_user_id', $block_ref_ids);
				}
				$this->db->order_by('post_liked_count + post_commented_count * 5', 'desc');
				$this->db->limit(config_item('limit_query_featured_page'), 0);
				$query_featured_posts = $this->db->get('tr_posts');
				foreach($query_featured_posts->result_array() as $post) {
					$post_new = $post;
					$query_is_liked = $this->db->like('like_ids', ',' . $post['post_id'] . ';')->get_where('tr_likes', array('like_user_id' => $params['user_id']));
					$post_new['is_liked'] = ($query_is_liked->num_rows() > 0) ? 1 : 0;
					$post_new['post_likes'] = $this->get_post_likes($post['post_id'], $params['user_id']);
					$post_new['post_comments'] = $this->get_post_comments($post['post_id'], $params['user_id']);
					$post_new['post_time_diff'] = time() - strtotime($post['post_date']);
					$featured_posts[] = $post_new;
				}
				$result['featured_posts'] = $featured_posts;
				
				$featured_posts_ids = array();
				foreach($result['featured_posts'] as $featured_post) {
					$featured_posts_ids[] = $featured_post['post_id'];
				}
				if(count($featured_posts_ids)) {
					$featured_date = date('Y-m-d H:i:s');
					$this->db->where('post_featured_date', '1970-01-01 00:00:00');
					$this->db->where_in('post_id', $featured_posts_ids);
					$this->db->update('tr_posts', array('post_featured_date' => $featured_date));
				}
			}
		} else {
			
			$followers_user_ids = array();
			$query_followers = $this->db->get_where('tr_follows', array('follow_user_id' => $params['user_id']));
			foreach(explode(',', $this->get_ids_str_trimmed(element('follow_ids', $query_followers->row_array()))) as $item) {
				$item_arr = explode(';', $item);
				$followers_user_ids[] = $item_arr[0];
			}
			$followers_user_ids[] = $params['user_id'];
			
			$followers_posts = array();
			if(count($followers_user_ids)) {
				$this->db->select($param_posts_array)->where_in('post_user_id', $followers_user_ids);
				if(count($block_ref_ids) > 0) {
					$this->db->where_not_in('post_user_id', $block_ref_ids);
				}
				$this->db->order_by('post_date', 'desc');
				$this->db->limit($query_limit, $query_offset);
				$query_followers_posts = $this->db->get('tr_posts');
				foreach($query_followers_posts->result_array() as $post) {
					$post_new = $post;
					$query_is_liked = $this->db->like('like_ids', ',' . $post['post_id'] . ';')->get_where('tr_likes', array('like_user_id' => $params['user_id']));
					$post_new['is_liked'] = ($query_is_liked->num_rows() > 0) ? 1 : 0;
					$post_new['post_likes'] = $this->get_post_likes($post['post_id'], $params['user_id']);
					$post_new['post_comments'] = $this->get_post_comments($post['post_id'], $params['user_id']);
					$post_new['post_time_diff'] = time() - strtotime($post['post_date']);
					$followers_posts[] = $post_new;
				}
			}
			$result['followers_posts'] = $followers_posts;
		}
		
    	$status = 1;
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function post($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
    	
    	if($params['mode'] == 'add') {
	    	$request_fields = array('post_video_data', 'post_video_thumb_photo_data', 'post_caption', 'post_category_id');
	    	if(isset($params['post_video_data']) && $params['post_video_data'] != '') {
	    		$video_path = config_item('path_media_posts');
	    		$created_time = time();
	    		$video_name = $params['user_id'] . '_' . $created_time . '.mp4';
	    		$video_url = $video_path . $video_name;
	    		$binary = base64_decode($params['post_video_data']);
	    		header('Content-Type: bitmap; charset=utf-8');
	    		$file = fopen($video_url, 'w');
	    		if($file) {
	    			fwrite($file, $binary);
	    		} else {
	    			$status = 3;
	    			$msg = 'File upload failed';
	    		}
	    		fclose($file);
	    		if($status < 2) {
			    	if(isset($params['post_video_thumb_photo_data']) && $params['post_video_thumb_photo_data'] != '') {
			    		$image_path = config_item('path_media_post_thumbs');
			    		$created_time = time();
			    		$image_name = $params['user_id'] . '_' . $created_time . '.jpg';
			    		$image_url = $image_path . $image_name;
			    		$binary = base64_decode($params['post_video_thumb_photo_data']);
			    		header('Content-Type: bitmap; charset=utf-8');
			    		$file1 = fopen($image_url, 'w');
			    		if($file1) {
			    			fwrite($file1, $binary);
			    			$data['post_thumb_url'] = config_item('media_post_thumb_self_domain_prefix') . $image_name;
			    		} else {
			    			$status = 3;
			    			$msg = 'File upload failed';
			    		}
			    		fclose($file1);
			    	}
			    	
		    		if($status < 2) {
		    			
		    			//$data['post_video_url'] = config_item('media_post_self_domain_prefix') . $video_name;
		    			$this->my_upload->do_upload_manually($video_path, $video_name, $video_path);
		    			$data['post_video_url'] = s3_site_url($video_url);
		    			
		    			$data['post_caption'] = $params['post_caption'];
		    			$data['post_category_id'] = $params['post_category_id'];
		    			$data['post_date'] = date('Y-m-d H:i:s');
		    			$query_current_user = $this->db->get_where('tr_users', array('user_id' => $params['user_id']));
		    			$current_user = $query_current_user->row_array();
		    			$data['post_user_id'] = $current_user['user_id'];
		    			$data['post_user_name'] = $current_user['user_name'];
		    			$data['post_user_photo_url'] = $current_user['user_photo_url'];
		    			
		    			$this->db->insert('tr_posts', $data);
		    			$post_insert_id = $this->db->insert_id();
		    			if($post_insert_id > 0) {
		    				// Update User Posts Count
		    				$this->db->where('user_id', $params['user_id']);
		    				$this->db->set('user_posts_count', 'user_posts_count + 1', FALSE);
		    				$this->db->update('tr_users');
		    				
		    				$result = $this->get_user_detail($params['user_id']);
		    				$status = 1;
		    			}
		    		}
	    		}
	    	}
    	} else if($params['mode'] == 'delete') {
    		$query_post = $this->db->get_where('tr_posts', array('post_id' => $params['post_id']));
    		$post = $query_post->row_array();
    		
    		$post_media = config_item('path_media_posts') . $this->get_post_prefix_removed_media_name($post['post_video_url']);
    		if(file_exists($post_media)) unlink($post_media);
    		
    		$post_thumb_media = config_item('path_media_post_thumbs') . $this->get_post_thumb_prefix_removed_media_name($post['post_thumb_url']);
    		if(file_exists($post_thumb_media)) unlink($post_thumb_media);
    		
    		$this->db->delete('tr_posts', array('post_id' => $params['post_id']));
    		$this->db->delete('tr_comments', array('comment_post_id' => $params['post_id']));
    		$this->db->query('update tr_users set user_posts_count = user_posts_count - 1 where user_id = ' . $params['user_id']);
    		
    		$this->db->like('like_ids', ',' . $params['post_id'] . ':');
    		$query_likes = $this->db->get('tr_likes');
    		foreach($query_likes->result_array() as $like) {
    			$this->db->query('update tr_users set user_likes_count = user_likes_count - 1 where user_id = ' . $like['like_user_id']);
    		}
    		
    		$result = $this->get_user_detail($params['user_id']);
    		$status = 1;
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function follow_user($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
    	if($params['user_id'] == $params['ref_user_id']) {
    		$status = 3;
    		$msg = 'You cannot follow by yourself';
    	} else {
    		$query_follow = $this->db->get_where('tr_follows', array('follow_user_id' => $params['user_id']));
    		$follow_ref_ids_str = element('follow_ids', $query_follow->row_array());
    		$count_diff = 1;
    		if(strpos($follow_ref_ids_str, ',' . $params['ref_user_id'] . ';') !== false) {
    			$count_diff = -1;
    		}
    		if($count_diff == 1) {
    			if($follow_ref_ids_str == '') $follow_ref_ids_str = ',';
    			$follow_ref_ids_str .= ($params['ref_user_id'] . ';0;' . date('Y-m-d H:i:s') . ',');
    		} else {
    			$new_follow_ref_ids_arr = array();
    			foreach(explode(',', $this->get_ids_str_trimmed($follow_ref_ids_str)) as $follow_ref_id) {
    				$follow_ref_id_arr = explode(';', $follow_ref_id);
    				if($follow_ref_id_arr[0] != $params['ref_user_id']) $new_follow_ref_ids_arr[] = $follow_ref_id;
    			}
    			$new_follow_ref_ids_str = '';
    			if(count($new_follow_ref_ids_arr)) {
    				$new_follow_ref_ids_str = (',' . implode(',', $new_follow_ref_ids_arr) . ',');
    			}
    			$follow_ref_ids_str = $new_follow_ref_ids_str;
    		}
    		$this->db->update('tr_follows', array('follow_ids' => $follow_ref_ids_str), array('follow_user_id' => $params['user_id']));
    		
    		
    		
    		$query_followers_count = $this->db->select('user_followers_count')->get_where('tr_users', array('user_id' => $params['ref_user_id']));
    		$this->db->update('tr_users', array('user_followers_count' => ((int)element('user_followers_count', $query_followers_count->row_array()) + $count_diff)), array('user_id' => $params['ref_user_id']));
    		
    		$query_following_count = $this->db->select('user_following_count')->get_where('tr_users', array('user_id' => $params['user_id']));
    		$this->db->update('tr_users', array('user_following_count' => ((int)element('user_following_count', $query_following_count->row_array()) + $count_diff)), array('user_id' => $params['user_id']));
    		
    		$result = $this->reference_user_detail($params);
    		
    		// Push Notification
    		if($count_diff == 1) {
	    		$query_sender = $this->db->select('user_name')->get_where('tr_users', array('user_id' => $params['user_id']));
	    		$query_receiver = $this->db->select('user_apns_id')->get_where('tr_users', array('user_id' => $params['ref_user_id']));
	    		$apns_id = element('user_apns_id', $query_receiver->row_array());
	    		if($apns_id != '') {
	    			$message = element('user_name', $query_sender->row_array()) . ' is following you.';
	    			$this->send_APNS($apns_id, $message);
	    		}
    		}
    		$status = 1;
    	}
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function send_APNS($apns_id, $message) {
    	
    	$status = false;
    	
    	$deviceToken = $apns_id;
    	//echo $deviceToken;
    	$passphrase = 'silver';
    	
    	$message_params = array('message' => 'Message arrived');
    	
    	////////////////////////////////////////////////////////////////////////////////
    	
    	$ctx = stream_context_create();
    	stream_context_set_option($ctx, 'ssl', 'local_cert', './application/models/ck.pem');
    	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
    	// Open a connection to the APNS server
    	$fp = stream_socket_client(
    			'ssl://gateway.push.apple.com:2195', $err,
    			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    	
//     	$fp = stream_socket_client(
//     	 'ssl://gateway.sandbox.push.apple.com:2195', $err,
//     			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    	
    	if(!$fp) exit("Failed to connect: $err $errstr" . PHP_EOL);
    	//echo 'Connected to APNS' . PHP_EOL;
    	// Create the payload body
    	$body['aps'] = array(
    			'alert' => $message,
    			'sound' => 'default',
    			'badgecount' => 1,
    			'info'=> $message_params,
    			'notify' => 'notification'
    	);
    	// Encode the payload as JSON
    	$payload = json_encode($body);
    	// Build the binary notification
    	$msg1 = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
    	// Send it to the server
    	$result_apns = fwrite($fp, $msg1, strlen($msg1));
    	if (!$result_apns) {
    		//echo 'Message not delivered' . PHP_EOL;
    		$status = false;
    	} else {
    		//echo 'Message successfully delivered' . PHP_EOL;
    		$status = true;
    	}
    	// Close the connection to the server
    	fclose($fp);
    	
    	return $status;
    	
    }
    
    public function like_post($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
    	
    	$query_like = $this->db->get_where('tr_likes', array('like_user_id' => $params['user_id']));
    	$like_ref_ids_str = element('like_ids', $query_like->row_array());
    	$count_diff = 1;
    	if(strpos($like_ref_ids_str, ',' . $params['ref_post_id'] . ';') !== false) {
    		$count_diff = -1;
    	}
    	if($count_diff == 1) {
    		if($like_ref_ids_str == '') $like_ref_ids_str = ',';
    		$like_ref_ids_str .= ($params['ref_post_id'] . ';0;' . date('Y-m-d H:i:s') . ',');
    	} else {
    		$new_like_ref_ids_arr = array();
    		foreach(explode(',', $this->get_ids_str_trimmed($like_ref_ids_str)) as $like_ref_id) {
    			$like_ref_id_arr = explode(';', $like_ref_id);
    			if($like_ref_id_arr[0] != $params['ref_post_id']) $new_like_ref_ids_arr[] = $like_ref_id;
    		}
    		$new_like_ref_ids_str = '';
    		if(count($new_like_ref_ids_arr)) {
    			$new_like_ref_ids_str = (',' . implode(',', $new_like_ref_ids_arr) . ',');
    		}
    		$like_ref_ids_str = $new_like_ref_ids_str;
    	}
    	$this->db->update('tr_likes', array('like_ids' => $like_ref_ids_str), array('like_user_id' => $params['user_id']));
    	
    	$query_likes_count = $this->db->select('user_likes_count')->get_where('tr_users', array('user_id' => $params['user_id']));
    	$this->db->update('tr_users', array('user_likes_count' => ((int)element('user_likes_count', $query_likes_count->row_array()) + $count_diff)), array('user_id' => $params['user_id']));
    	$query_liked_count = $this->db->select('post_liked_count')->get_where('tr_posts', array('post_id' => $params['ref_post_id']));
    	$this->db->update('tr_posts', array('post_liked_count' => ((int)element('post_liked_count', $query_liked_count->row_array()) + $count_diff)), array('post_id' => $params['ref_post_id']));
    	
    	$result = $this->get_user_detail($params['user_id']);
    	$result['ref_post'] = $this->get_post_info($params['ref_post_id'], $params['user_id']);
    	
    	// Push Notification
    	if($count_diff == 1) {
	    	$query_sender = $this->db->select('user_name')->get_where('tr_users', array('user_id' => $params['user_id']));
	    	$query_receiver = $this->db->select('tr_users.user_apns_id')->join('tr_users', 'tr_users.user_id = tr_posts.post_user_id')->get_where('tr_posts', array('post_id' => $params['ref_post_id']));
	    	$apns_id = element('user_apns_id', $query_receiver->row_array());
	    	if($apns_id != '') {
	    		$message = element('user_name', $query_sender->row_array()) . ' liked your workout.';
	    		$this->send_APNS($apns_id, $message);
	    	}
    	}
    	
    	$status = 1;
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    
    public function comment_post($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
    	
    	if($params['mode'] == 'add') {
    		if (!(isset($params['comment_content']) && $params['comment_content'] != '' && $params['comment_content'] != '0')) {
    			$status = 0;
    		} else {
    			$data['comment_post_id'] = $params['ref_post_id'];
	    		$data['comment_content'] = $params['comment_content'];
	    		$data['comment_date'] = date('Y-m-d H:i:s');
	    		$query_current_user = $this->db->get_where('tr_users', array('user_id' => $params['user_id']));
	    		$current_user = $query_current_user->row_array();
	    		$data['comment_user_id'] = $current_user['user_id'];
	    		$data['comment_user_name'] = $current_user['user_name'];
	    		$data['comment_user_photo_url'] = $current_user['user_photo_url'];
	    		
	    		$this->db->insert('tr_comments', $data);
	    		$comment_insert_id = $this->db->insert_id();
	    		if($comment_insert_id > 0) {
	    			
	    			$this->update_comment_tag_user_ids($comment_insert_id, $data['comment_user_name']);
	    			
	    			$this->db->where('post_id', $params['ref_post_id']);
	    			$this->db->set('post_commented_count', 'post_commented_count + 1', FALSE);
	    			$this->db->update('tr_posts');
	    		
	    			$result = $this->get_user_detail($params['user_id']);
			    	$result['ref_post'] = $this->get_post_info($params['ref_post_id'], $params['user_id']);
			    	
			    	// Push Notification
			    	$query_sender = $this->db->select('user_name')->get_where('tr_users', array('user_id' => $params['user_id']));
			    	$query_receiver = $this->db->select('tr_users.user_apns_id')->join('tr_users', 'tr_users.user_id = tr_posts.post_user_id')->get_where('tr_posts', array('post_id' => $params['ref_post_id']));
			    	$apns_id = element('user_apns_id', $query_receiver->row_array());
			    	if($apns_id != '') {
			    		$message = element('user_name', $query_sender->row_array()) . ' commented on your workout.';
			    		$this->send_APNS($apns_id, $message);
			    	}
			    	$status = 1;
	    		}
    		}
    	} else if($params['mode'] == 'delete') {
    		if (!(isset($params['comment_id']) && $params['comment_id'] != '' && $params['comment_id'] != '0')) {
    			$status = 0;
    		} else {
    			$query_current_comment = $this->db->get_where('tr_comments', array('comment_id' => $params['comment_id']));
    			$current_comment = $query_current_comment->row_array();
    			
    			$this->db->delete('tr_comments', array('comment_id' => $params['comment_id']));
    			
    			$this->db->where('post_id', $current_comment['comment_post_id']);
    			$this->db->set('post_commented_count', 'post_commented_count - 1', FALSE);
    			$this->db->update('tr_posts');
    				
    			$result = $this->get_user_detail($params['user_id']);
    			$result['ref_post'] = $this->get_post_info($current_comment['comment_post_id'], $params['user_id']);
    			$status = 1;
    		}
    	}
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function update_comment_tag_user_ids($comment_id, $commented_user_name) {
    	$query_comment = $this->db->get_where('tr_comments', array('comment_id' => $comment_id));
    	$comment = $query_comment->row_array();
    	$query_users = $this->db->select(array('user_id', 'user_name', 'user_apns_id'))->get_where('tr_users', array('user_closed' => 0));
    	$tagged_user_ids_str = '';
    	foreach($query_users->result_array() as $row) {
    		if(strpos($comment['comment_content'], '@' . $row['user_name']) !== false) {
    			if($tagged_user_ids_str == '') $tagged_user_ids_str = ',';
    			$tagged_user_ids_str .= $row['user_id'] . ';0,';
    			
    			// Push Notification
    			$apns_id = $row['user_apns_id'];
    			if($apns_id != '') {
    				$message = $commented_user_name . ' mentioned you in a comment.';
    				$this->send_APNS($apns_id, $message);
    			}
    		}
    	}
    	$this->db->update('tr_comments', array('comment_tag_user_ids' => $tagged_user_ids_str), array('comment_id' => $comment_id));
    }
    
    public function search($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
    	
    	$param_array = array('user_id', 'user_email', 'user_first_name', 'user_last_name', 'user_full_name', 'user_name', 'user_photo_url', 'user_bio',
    			'user_posts_count', 'user_likes_count', 'user_followers_count', 'user_following_count');
    	
    	//$result = $this->get_user_detail($params['user_id']);
    	$block_ref_ids = $this->get_block_ref_ids($params['user_id']);
    	
    	$query_offset = (isset($params['search_offset']) && $params['search_offset'] != '' && (int)$params['search_offset'] > 0) ? (int)$params['search_offset'] : 0;
    	$query_limit = (isset($params['search_limit']) && $params['search_limit'] != '' && (int)$params['search_limit'] > 0) ? (int)$params['search_limit'] : config_item('limit_query_search_default');

    	$this->db->like('user_full_name', $params['search_keyword']);
    	$this->db->or_like('user_name', $params['search_keyword']);
    	if(count($block_ref_ids) > 0) {
    		$this->db->where_not_in('user_id', $block_ref_ids);
    	}
    	$this->db->order_by('user_last_updated_date', 'desc');
    	$this->db->limit($query_limit, $query_offset);
    	$query_search = $this->db->select($param_array)->get_where('tr_users', array('user_closed' => 0));
    	
    	$query_follow = $this->db->get_where('tr_follows', array('follow_user_id' => $params['user_id']));
    	$follow_ids_str = element('follow_ids', $query_follow->row_array());
    	
    	$search_result = array();
    	foreach($query_search->result_array() as $item) {
    		$item_new = $item;
    		if(strpos($follow_ids_str, ',' . $item['user_id'] . ';') !== false) {
    			$item_new['is_following'] = 1;
    		} else {
    			$item_new['is_following'] = 0;
    		}
    		$search_result[] = $item_new;
    	}
    	
    	$result['search_result'] = $search_result;
    	$status = 1;
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    public function report($params) {
    	$result = array();
    	$data = array();
    	$status = 0;
    	$msg = '';
    
    	
    	if($params['report_content'] != '') {

    		$data = array(
    				'report_mode' => $params['ref_mode'] == 'user' ? 1 : 2,
    				'report_user_id' => $params['user_id'],
    				'report_ref_id' => $params['ref_id'],
    				'report_content' => $params['report_content'],
    				'report_date' => date('Y-m-d H:i:s')
    		);
    		$this->db->insert('tr_reports', $data);
    		if($params['ref_mode'] == 'user') {
    			$this->db->query('update tr_users set user_reported_count = user_reported_count + 1 where user_id = ' . $params['ref_id']);
    		} else if($params['ref_mode'] == 'post') {
    			$this->db->query('update tr_posts set post_reported_count = post_reported_count + 1 where post_id = ' . $params['ref_id']);
    		}
    		
    		$to = config_item('feedback_receiver_email');
    		
    		$ref_name = ($params['ref_mode'] == 'user') ? 'user' : 'workout';
    		
    		$user = $this->get_user_info($params['user_id']);
    		 
    		$user_name = $user['user_full_name'];
    		$device_name = ($params['device_type'] == 1) ? 'iOS' : 'Android';
    		 
    		$subject = 'Train App - ' . $user_name . ' sent a report about a ' . $ref_name;
    		
    		
    		$reported_ref_content = '';
    		if($params['ref_mode'] == 'user') {
    			
    			$query_ref = $this->db->get_where('tr_users', array('user_id' => $params['ref_id']));
				$ref = $query_ref->row_array();
				
    			$reported_ref_content .= '
    			<b>Full Name</b>: ' . $ref['user_full_name'] . '<br>
    			<b>User Name</b>: ' . $ref['user_name'] . '<br>';
    			
    			/* if(isset($ref['user_email']) && $ref['user_email'] != '') {
    				$reported_ref_content .= '<b>Email Address</b>: ' . $ref['user_email'] . '<br>';
    			} */
    			
    			if(isset($ref['user_photo_url']) && $ref['user_photo_url'] != '') {
    				$user_photo_url = '';
    				$prefix_length = strlen(config_item('media_user_self_domain_prefix'));
    				if(substr($ref['user_photo_url'], 0, $prefix_length) == config_item('media_user_self_domain_prefix')) {
    					$user_photo_url = base_url() . config_item('path_media_users') . substr($ref['user_photo_url'], $prefix_length);
    				} else {
    					$user_photo_url = $ref['user_photo_url'];
    				}
    				$reported_ref_content .= '<img src="' . $user_photo_url . '" width="160" height="160" alt="' . $ref['user_full_name'] . '"><br>';
    			}
    			
    			if(isset($ref['user_bio']) && $ref['user_bio'] != '') {
    				$reported_ref_content .= '<b>Bio</b>: ' . $ref['user_bio'] . '<br>';
    			}
    			
    			$reported_ref_content .= '
    			Posts : ' . $ref['user_posts_count'] . ', Likes : ' . $ref['user_likes_count'] . '<br>
    			Followers : ' . $ref['user_followers_count'] . ', Following : ' . $ref['user_following_count'] . '<br>';
    			
    		} else if($params['ref_mode'] == 'post') {
    			
    			$query_ref = $this->db->get_where('tr_posts', array('post_id' => $params['ref_id']));
    			$ref = $query_ref->row_array();
    			
    			if(isset($ref['post_video_url']) && $ref['post_video_url'] != '') {
    				$post_video_url = '';
    				$prefix_length = strlen(config_item('media_post_self_domain_prefix'));
    				if(substr($ref['post_video_url'], 0, $prefix_length) == config_item('media_post_self_domain_prefix')) {
    					$post_video_url = base_url() . config_item('path_media_posts') . substr($ref['post_video_url'], $prefix_length);
    				} else {
    					$post_video_url = $ref['post_video_url'];
    				}
    				$reported_ref_content .= 'Video URL : <a target="_blank" title="Click to check workout video" href="' . $post_video_url . '">' . $post_video_url . '</a><br>';
    			}
    			
    			$reported_ref_content .= '<br>
    			Likes : ' . $ref['post_liked_count'] . ', Comments : ' . $ref['post_commented_count'] . '<br>';
    			
    			$reported_ref_content .= '<br>
    			--- Workout Posted User Information ---<br>
    			<b>User Name</b>: ' . $ref['post_user_name'] . '<br>';
    			if(isset($ref['post_user_photo_url']) && $ref['post_user_photo_url'] != '') {
    				$user_photo_url = '';
    				$prefix_length = strlen(config_item('media_user_self_domain_prefix'));
    				if(substr($ref['post_user_photo_url'], 0, $prefix_length) == config_item('media_user_self_domain_prefix')) {
    					$user_photo_url = base_url() . config_item('path_media_users') . substr($ref['post_user_photo_url'], $prefix_length);
    				} else {
    					$user_photo_url = $ref['post_user_photo_url'];
    				}
    				$reported_ref_content .= '<img src="' . $user_photo_url . '" width="160" height="160" alt="' . $ref['post_user_name'] . '"><br>';
    			}
    		}
    		
    		$message = '
    		A report was received from <b>' . $user_name . '</b> through <b>Train - ' . $device_name . ' Application</b>.<br>
    		<br>
    		<h4>Report Message:</h4><br>
    		-------------------------------------------<br><br>
    		' . $params['report_content'] . '<br><br>
    		-------------------------------------------<br>
    		<br>
    		<br>
    		<h4>Information of the reported ' . $ref_name . ':</h4><br>
    		-------------------------------------------<br><br>
    		' . $reported_ref_content . '
    		<br><br>
    		-------------------------------------------<br>
    		<br>
    		';
    
    		// Always set content-type when sending HTML email
    		$headers = "MIME-Version: 1.0" . "\r\n";
    		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    
    		// More headers
    		$headers .= 'From: <no-reply@trainapp.com>' . "\r\n";
    		//$headers .= 'Cc: cc@example.com' . "\r\n";
    
    		if(mail($to, $subject, $message, $headers)) {
    			$status = 1;
    		} else {
//     			$status = 2;
    		}
    	}
    
    	//     	$result = array_merge($result, $this->get_common_result());
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    
    
    public function block_user($params) {
    	$result = array();
    	$status = 0;
    	$msg = '';
    
    	$where = array('block_user_id' => $params['user_id']);
    	$query = $this->db->get_where('tr_blocks', $where);
    	
    	$ref_user_ids_str = '';
    	if($query->num_rows() == 0) {
    		$this->db->insert('tr_blocks', array('block_user_id' => $params['user_id'], 'block_ref_ids' => ''));
    	} else {
    		$ref_user_ids_str = element('block_ref_ids', $query->row_array());
    	}
    	$is_exist = false;
    	if($ref_user_ids_str != '') {
	    	foreach(explode(',', $ref_user_ids_str) as $ref_id) {
	    		if($ref_id == $params['ref_id']) {
	    			$is_exist = true;
	    			break;
	    		}
	    	}
    	}

    	$new_ref_user_ids_str = '';
    	if(!$is_exist) {
    		if($ref_user_ids_str == '') {
    			$new_ref_user_ids_str = ',' . $params['ref_id'] . ',';
    		} else {
    			$new_ref_user_ids_str = $ref_user_ids_str . $params['ref_id'] . ',';
    		}
    		 
    		$this->db->update('tr_blocks', array('block_ref_ids' => $new_ref_user_ids_str), $where);
    		
    		$result = $this->get_user_detail($params['user_id']);
    		$status = 1;
    	} else {
    		$msg = 'You already blocked this user';
    		$status = 2;
    	}
    	
    	$result['status'] = $status;
    	$result['msg'] = $msg;
    	return $result;
    }
    
    
    // Common Functions
    
    public function get_post_info($post_id, $user_id) {
    	
    	$param_array = array('post_id', 'post_thumb_url', 'post_video_url', 'post_caption', 'post_date', 'post_category_id',
    			'post_user_id', 'post_user_name', 'post_user_photo_url',
    			'post_liked_count', 'post_commented_count');
    	$query_post = $this->db->select($param_array)->get_where('tr_posts', array('post_id' => $post_id));
    	$result = $query_post->row_array();
    	
    	$query_is_liked = $this->db->like('like_ids', ',' . $post_id . ';')->get_where('tr_likes', array('like_user_id' => $user_id));
    	$result['is_liked'] = ($query_is_liked->num_rows() > 0) ? 1 : 0;
    	
    	$result['post_likes'] = $this->get_post_likes($result['post_id'], $user_id);
    	$result['post_comments'] = $this->get_post_comments($result['post_id'], $user_id);
    	$result['post_time_diff'] = time() - strtotime($result['post_date']);
    	
    	return $result;
    }
    
    public function get_block_ref_ids($user_id) {
    	$block_ref_ids = array();
    	$query_blocks = $this->db->select('block_ref_ids')->get_where('tr_blocks', array('block_user_id' => $user_id));
    	$block_ref_ids_str = element('block_ref_ids', $query_blocks->row_array());
    	if($block_ref_ids_str != '') {
    		foreach(explode(',', $this->get_ids_str_trimmed($block_ref_ids_str)) as $block_ref_id_item) {
    			if($block_ref_id_item != '') {
    				$block_ref_ids[] = $block_ref_id_item;
    			}
    		}
    	}
    	
    	$query_blocked = $this->db->select('block_user_id')->like(array('block_ref_ids' => ',' . $user_id . ','))->get('tr_blocks');
    	foreach($query_blocked->result_array() as $row) {
    		$block_ref_ids[] = $row['block_user_id'];
    	}
    	
    	return $block_ref_ids;
    }
    
    public function get_user_info($user_id, $only_user = false) {
    	$result_user = array();
    	
    	
    	$param_array = array('user_id', 'user_email', 'user_first_name', 'user_last_name', 'user_full_name', 'user_name', 'user_photo_url', 'user_bio',
    			'user_posts_count', 'user_likes_count', 'user_followers_count', 'user_following_count');
    	$query = $this->db->select($param_array)->get_where('tr_users', array('user_id' => $user_id, 'user_closed' => 0));
    	$result_user = $query->row_array();
    	
    	if($only_user) {
    		return $result_user;
    	}
    	
    	// Get Blocked ids
    	$block_ref_ids = $this->get_block_ref_ids($user_id);
    	 
    	
    	// User Following
    	$query_follows = $this->db->get_where('tr_follows', array('follow_user_id' => $user_id));
    	$follow_ids_str = element('follow_ids', $query_follows->row_array());
    	$follow_ids_arr = array();
    	foreach(explode(',', $this->get_ids_str_trimmed($follow_ids_str)) as $item) {
    		$follow_id_item_arr = explode(';', $item);
    		$follow_ids_arr[] = $follow_id_item_arr[0];
    	}
    	
    	$user_follows = array();
    	if(count($follow_ids_arr)) {
	    	$this->db->where_in('user_id', $follow_ids_arr);
	    	if(count($block_ref_ids) > 0) {
	    		$this->db->where_not_in('user_id', $block_ref_ids);
	    	}
	    	$query_follows_user = $this->db->select($param_array)->order_by('user_followers_count', 'desc')->get_where('tr_users', array('user_closed' => 0));
	    	foreach($query_follows_user->result_array() as $item) {
	    		$item_new = $item;
	    		$item_new['is_following'] = 1;
	    		$user_follows[] = $item_new;
	    	}
    	}
    	$result_user['user_following'] = $user_follows;
    	
    	// User Followers
    	$query_following = $this->db->select('follow_user_id')->like('follow_ids', ',' . $user_id . ';')->get('tr_follows');
    	$following_user_ids_arr = array();
    	foreach($query_following->result_array() as $row) {
    		$following_user_ids_arr[] = $row['follow_user_id'];
    	}
    	
    	$user_following = array();
    	if(count($following_user_ids_arr)) {
	    	$this->db->where_in('user_id', $following_user_ids_arr);
    		if(count($block_ref_ids) > 0) {
	    		$this->db->where_not_in('user_id', $block_ref_ids);
	    	}
	    	$query_following_user = $this->db->select($param_array)->order_by('user_following_count', 'desc')->get_where('tr_users', array('user_closed' => 0));
	    	foreach($query_following_user->result_array() as $item) {
	    		$item_new = $item;
	    		$query_follow_ids = $this->db->like('follow_ids', ',' . $item['user_id'] . ';')->get_where('tr_follows', array('follow_user_id' => $user_id));
	    		$item_new['is_following'] = ($query_follow_ids->num_rows() > 0) ? 1 : 0;
	    		$user_following[] = $item_new;
	    	}
    	}
    	$result_user['user_follows'] = $user_following;
    	 
    	
    	// User Posts
    	$user_posts = array();
    	$param_array = array('post_id', 'post_thumb_url', 'post_video_url', 'post_caption', 'post_date', 'post_category_id',
    			'post_user_id', 'post_user_name', 'post_user_photo_url',
    			'post_liked_count', 'post_commented_count');
    	$this->db->select($param_array);
    	$this->db->order_by('post_date', 'desc');
    	$query_posts = $this->db->get_where('tr_posts', array('post_user_id' => $user_id));
    	foreach($query_posts->result_array() as $post) {
    		$post_new = $post;
    		$query_is_liked = $this->db->like('like_ids', ',' . $post['post_id'] . ';')->get_where('tr_likes', array('like_user_id' => $user_id));
    		$post_new['is_liked'] = ($query_is_liked->num_rows() > 0) ? 1 : 0;
    		$post_new['post_likes'] = $this->get_post_likes($post['post_id'], $user_id);
    		$post_new['post_comments'] = $this->get_post_comments($post['post_id'], $user_id);
    		$post_new['post_time_diff'] = time() - strtotime($post['post_date']);
    		$user_posts[] = $post_new;
    	}
    	$result_user['user_posts'] = $user_posts;
    	
    	// User Likes Posts
    	$user_likes_posts = array();
    	$query_likes = $this->db->get_where('tr_likes', array('like_user_id' => $user_id));
    	$like_ref_ids = array();
    	foreach(explode(',', element('like_ids', $query_likes->row_array())) as $like_ref_id_item) {
    		$like_ref_id_item_arr = explode(';', $like_ref_id_item);
    		$like_ref_ids[] = $like_ref_id_item_arr[0];
    	}
    	
    	if(count($like_ref_ids)) {
    		$this->db->select($param_array);
    		$this->db->where_in('post_id', $like_ref_ids);
    	   	if(count($block_ref_ids) > 0) {
	    		$this->db->where_not_in('post_user_id', $block_ref_ids);
	    	}
    		$this->db->order_by('post_date', 'desc');
	    	$query_posts = $this->db->get('tr_posts');
	    	foreach($query_posts->result_array() as $post) {
	    		$post_new = $post;
	    		$post_new['is_liked'] = 1;
	    		$post_new['post_likes'] = $this->get_post_likes($post['post_id'], $user_id);
	    		$post_new['post_comments'] = $this->get_post_comments($post['post_id'], $user_id);
	    		$user_likes_posts[] = $post_new;
	    	}
	    	$result_user['user_likes_posts'] = $user_likes_posts;
    	}
    	return $result_user;
    }
    
    public function get_post_likes($post_id, $user_id) {
    	
    	// Get Blocked ids
    	$block_ref_ids = $this->get_block_ref_ids($user_id);
    	
    	$query_liked = $this->db->like('like_ids', ',' . $post_id . ';')->get('tr_likes');
    	$liked_user_ids = array();
    	foreach($query_liked->result_array() as $row) {
    		$liked_user_ids[] = $row['like_user_id'];
    	}
    	
    	$liked_users = array();
    	if(count($liked_user_ids)) {
    		$param_array = array('user_id', 'user_email', 'user_first_name', 'user_last_name', 'user_full_name', 'user_name', 'user_photo_url', 'user_bio',
    				'user_posts_count', 'user_likes_count', 'user_followers_count', 'user_following_count');
    		$this->db->where_in('user_id', $liked_user_ids);
    		
    		
    		if(count($block_ref_ids) > 0) {
    			$this->db->where_not_in('user_id', $block_ref_ids);
    		}
    		$query_liked_users = $this->db->select($param_array)->order_by('user_likes_count', 'desc')->get_where('tr_users', array('user_closed' => 0));
	    	foreach($query_liked_users->result_array() as $item) {
	    		$item_new = $item;
	    		$query_follow_ids = $this->db->like('follow_ids', ',' . $item['user_id'] . ';')->get_where('tr_follows', array('follow_user_id' => $user_id));
	    		$item_new['is_following'] = ($query_follow_ids->num_rows() > 0) ? 1 : 0;
	    		$liked_users[] = $item_new;
	    	}
    	}
    	
    	return $liked_users;
    }
    
    public function get_post_comments($post_id, $user_id) {
    	$param_array = array('comment_id', 'comment_post_id', 'comment_content', 'comment_date', 'comment_activity_done',
    			'comment_user_id', 'comment_user_name', 'comment_user_photo_url', 'comment_tag_user_ids');
    	$query_commented = $this->db->select($param_array)->order_by('comment_date', 'desc')->get_where('tr_comments', array('comment_post_id' => $post_id));
    	$post_comments = array();
    	foreach($query_commented->result_array() as $post_comment) {
    		$post_comment['comment_time_diff'] = time() - strtotime($post_comment['comment_date']);
    		$post_comments[] = $post_comment;
    	}
    	
    	return $post_comments;
    }
    
    public function get_user_detail($param_id) {
    	$result = array();
    	$user_id = $param_id;
    	// Get Current User Info
    	$result['current_user'] = $this->get_user_info($user_id, true);
    	
    	$my_info = $this->get_user_info($user_id);
    	$result['my_posts'] = $my_info['user_posts'];
    	$result['my_likes_posts'] = $my_info['user_likes_posts'];
    	$result['my_follows'] = $my_info['user_follows'];
    	$result['my_following'] = $my_info['user_following'];
    	
    	// Get Categories
    	$query = $this->db->get('tr_categories');
    	$result['categories'] = $query->result_array();
    	return $result;
    }
    
	public function get_post_prefix_removed_media_name($image_name) {
    	$prefix_length = strlen(config_item('media_post_self_domain_prefix'));
    	if(substr($image_name, 0, $prefix_length) == config_item('media_post_self_domain_prefix')) {
    		return substr($image_name, $prefix_length);
    	} else {
    		return $image_name;
    	}
    }
    
    public function get_post_thumb_prefix_removed_media_name($image_name) {
    	$prefix_length = strlen(config_item('media_post_thumb_self_domain_prefix'));
    	if(substr($image_name, 0, $prefix_length) == config_item('media_post_thumb_self_domain_prefix')) {
    		return substr($image_name, $prefix_length);
    	} else {
    		return $image_name;
    	}
    }
    
    public function get_user_prefix_removed_media_name($image_name) {
    	$prefix_length = strlen(config_item('media_user_self_domain_prefix'));
    	if(substr($image_name, 0, $prefix_length) == config_item('media_user_self_domain_prefix')) {
    		return substr($image_name, $prefix_length);
    	} else {
    		return $image_name;
    	}
    }
    
    // Utility Functions
    public function get_ids_str_trimmed($str) {
    	if(strlen($str) < 3) {
    		return '';
    	} else {
    		return substr($str, 1, strlen($str) - 2);
    	}
    }
    public function remove_characters($needles, $str) {
    	$s = $str;
    	foreach($needles as $needle) {
    		$s = str_replace($needle, '', $s);
    	}
    	$s = $this->clean($s);
    	return $s;
    }
    public function clean($text) {
    	$text = trim( preg_replace( '/\s+/', ' ', $text ) );
    	$text = preg_replace( "/\r|\n/", "", $text);
    	return $text;
    }
    
    /*---------------------------------
    function parentChildSort_r
    $idField        = The item's ID identifier (required)
    $parentField    = The item's parent identifier (required)
    $els            = The array (required)
    $parentID       = The parent ID for which to sort (internal)
    $result     = The result set (internal)
    $depth          = The depth (internal)
    ----------------------------------*/
    
    public function parentChildSort_r($idField, $parentField, $els, $parentID = 0, &$result = array(), &$depth = 0){
    	foreach ($els as $key => $value):
    	if ($value[$parentField] == $parentID){
    		$value['depth'] = $depth;
    		array_push($result, $value);
    		unset($els[$key]);
    		$oldParent = $parentID;
    		$parentID = $value[$idField];
    		$depth++;
    		$this->parentChildSort_r($idField,$parentField, $els, $parentID, $result, $depth);
    		$parentID = $oldParent;
    		$depth--;
    	}
    	endforeach;
    	return $result;
    }
    
}
