<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {
    var $data;
    public function __construct() {
        parent::__construct();
        
        $valid = !(
        		empty($_SERVER['CONTENT_TYPE']) ||
        		$_SERVER['CONTENT_TYPE'] != 'application/json' ||
        		!(isset($_SERVER['HTTP_API_KEY']) && $_SERVER['HTTP_API_KEY'] == config_item('api_key')));

        if($valid) {
        	$this->data = json_decode(file_get_contents('php://input'), TRUE);
            $valid = !!count($this->data);
        }
        if(!$valid) {
        	echo "Invalid Request";
        	exit;
        }
        
    }

    public function user_signup() {
    	$request_fields = array('signup_mode');
        $request_form_success = true;
        foreach ($request_fields as $request_field) {
            if (!isset($this->data[$request_field])) {
                $request_form_success = false;
                break;
            }
        }
        if (!$request_form_success) {
            $response['status'] = 0;
            $response['msg'] = config_item('msg_fill_form');
        } else {
            $response = $this->api_model->user_signup($this->data);
        }
        echo json_encode($response);
    }
    
    public function user_login() {
    	$request_fields = array('user_email', 'user_password');
    	$request_form_success = true;
    	foreach($request_fields as $request_field) {
    		if(!(isset($this->data[$request_field]) && $this->data[$request_field] != '')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if(!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_login($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function user_retrieve_password() {
    	$request_fields = array('user_email');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if(!(isset($this->data[$request_field]) && $this->data[$request_field] != '')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_retrieve_password($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function user_profile_update() {
    	$request_fields = array('user_id', 'user_full_name', 'user_name');
    	$request_form_success = true;
    	foreach($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_profile_update($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function user_change_password() {
    	$request_fields = array('user_id', 'user_password_old', 'user_password_new');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!isset($this->data[$request_field])) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_change_password($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function user_logout() {
    	$request_fields = array('user_id', 'device_type');
    	$request_form_success = true;
    	foreach($request_fields as $request_field) {
    		if(!(isset($this->data[$request_field]) && $this->data[$request_field] != '')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if(!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->user_logout($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function reference_user_detail() {
    	$request_fields = array('user_id', 'ref_user_id');
    	$request_form_success = true;
    	foreach($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if(!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->reference_user_detail($this->data);
    	}
    	echo json_encode($response);
    }
    
    
    public function home() {
    	$request_fields = array('user_id');
    	$request_form_success = true;
    	foreach($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if(!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->home($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function discover() {
    	$request_fields = array('search_type', 'user_id');
    	$request_form_success = true;
    	foreach($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if(!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->discover($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function activities() {
    	$request_fields = array('user_id');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->activities($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function activity_done() {
    	$request_fields = array('user_id', 'activity_type', 'activity_ref_id', 'activity_user_id');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->activity_done($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function post() {
    	$request_fields = array('user_id', 'mode');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->post($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function follow_user() {
    	$request_fields = array('user_id', 'ref_user_id');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->follow_user($this->data);
    	}
    	echo json_encode($response);
    }
    
	public function like_post() {
    	$request_fields = array('user_id', 'ref_post_id');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->like_post($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function comment_post() {
    	$request_fields = array('user_id', 'ref_post_id', 'mode');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->comment_post($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function search() {
    	$request_fields = array('user_id', 'search_keyword');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->search($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function report() {
    	$request_fields = array('user_id', 'device_type', 'ref_mode', 'ref_id', 'report_content');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->report($this->data);
    	}
    	echo json_encode($response);
    }
    
    public function block_user() {
    	$request_fields = array('user_id', 'ref_id');
    	$request_form_success = true;
    	foreach ($request_fields as $request_field) {
    		if (!(isset($this->data[$request_field]) && $this->data[$request_field] != '' && $this->data[$request_field] != '0')) {
    			$request_form_success = false;
    			break;
    		}
    	}
    	if (!$request_form_success) {
    		$response['status'] = 0;
    		$response['msg'] = config_item('msg_fill_form');
    	} else {
    		$response = $this->api_model->block_user($this->data);
    	}
    	echo json_encode($response);
    }
}
