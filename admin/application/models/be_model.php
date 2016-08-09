<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Be_model extends CI_Model {
	
	public function get_site_info() {
		$site_info = array();
		$query = $this->db->get('site_info');
		foreach($query->result_array() as $row) {
			$site_info[$row['name']] = $row['value'];
		}
		$site_info['front_url'] = substr(base_url(), 0, strlen(base_url()) - 6);
		return $site_info;
	}
	
	
	public function logged_in() {
		if($this->session->userdata('admin_logged_in')) {
			return $this->session->userdata('admin_logged_in');
		} else {
			return false;
		}
	}
	
	public function is_valid_email($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	public function set_hidden_tags($requests = array()) {
		if(count($requests) > 0) {
			foreach($requests as $request_field => $request_value) {
				echo '<input type="hidden" name="' . $request_field . '" value="' . $request_value . '">';
			}
		}
	}
	
	public function be_character_limiter($str, $n = 15) {
		if(strlen($str) <= $n) {
			return $str;
		} else {
			return substr($str, 0, $n) . '...';
		}
	}
	
	public function get_breadcrumb($param) {
		$str = '
		<ol class="breadcrumb">
			<li><a href="' . base_url() . '"><i class="fa fa-dashboard"></i> Home</a></li>';
		foreach($param as $title => $link) {
			if($link != '') {
				$str .= '<li><a href="' . base_url() . $link . '">' . $title . '</a></li>';
			} else {
				$str .= '<li class="active">' . $title . '</li>';
			}
		}
		$str .= '
		</ol>
		';
		return $str;
	}
	
	public function get_menu_active($page, $menu) {
		$is_active = false;
		switch($menu) {
			case 'home':
				if($page == 'main') $is_active = true;
				break;
			case 'products':
				if($page == 'functional/products') $is_active = true;
				break;
			case 'services':
				if($page == 'functional/services') $is_active = true;
				break;
			case 'downloads':
				if($page == 'functional/downloads') $is_active = true;
				break;
			case 'newsroom':
				if($page == 'functional/newsroom') $is_active = true;
				break;
			case 'company':
				if($page == 'pages/company') $is_active = true;
				break;
			case 'contact':
				if($page == 'pages/contact') $is_active = true;
				break;
			default:
				$is_active = false;
				break;
		}
		return $is_active ? ' class="active"' : '';
	}
	
	public function get_form_validation_rule($item) {
		$rule = 'xss_clean';
		if(isset($item['minlength'])) $rule .= '|min_length['.$item['minlength'].']';
		if(isset($item['maxlength'])) $rule .= '|max_length['.$item['maxlength'].']';
		if(isset($item['required'])) $rule .= '|required';
		if(isset($item['valid_email'])) $rule .= '|valid_email';
		if(isset($item['password_confirm'])) $rule .= '|matches[password_confirm]';
		if(isset($item['numeric'])) $rule .= '|numeric';
		if(isset($item['exact_length'])) $rule .= '|exact_length['.$item['exact_length'].']';
		if(isset($item['is_unique'])) $rule .= '|is_unique['.$item['is_unique'].']';
		return $rule;
	}
	
	
	// Dashboard
	public function get_dashboard_info() {
		$info = array();
		$query_users = $this->db->select('user_id')->get_where('tr_users', array('user_closed' => 0));
		$info['count_registered_users'] = $query_users->num_rows();
		
		$query_posts = $this->db->select('post_id')->get_where('tr_posts');
		$info['count_posts'] = $query_posts->num_rows();
		
		return $info;
	}
	
	// Users
	public function get_users() {
		$result = array();
		
		$query_users = $this->db->order_by('user_id', 'desc')->get_where('tr_users', array('user_closed' => 0));
		$result = $query_users->result_array();
		
		return $result;
	}
	
	public function get_user($item_id = 0) {
		$arr = array();
		if($item_id) {
			$query = $this->db->where('user_id', $item_id)->get('tr_users');
			$arr = $query->row_array();
			
			$query_reports = $this->db
				->order_by('report_date', 'desc')
				->join('tr_users', 'tr_users.user_id = tr_reports.report_user_id')
				->get_where('tr_reports', array('report_mode' => 1, 'report_ref_id' => $item_id));
			$arr['reports'] = $query_reports->result_array();
		}
		return $arr;
	}
	
	public function get_posts() {
		$result = array();
		
		$query_posts = $this->db->order_by('post_id', 'desc')->join('tr_categories', 'tr_categories.category_id = tr_posts.post_category_id')->get('tr_posts');
		$result = $query_posts->result_array();
		
		return $result;
	}
	
	public function get_post($item_id = 0) {
		$arr = array();
		if($item_id) {
			$query = $this->db->where('post_id', $item_id)
				->join('tr_categories', 'tr_categories.category_id = tr_posts.post_category_id')
				->join('tr_users', 'tr_users.user_id = tr_posts.post_user_id')
				->get('tr_posts');
			$arr = $query->row_array();
			
			$query_reports = $this->db
				->order_by('tr_reports.report_date', 'desc')
				->join('tr_users', 'tr_users.user_id = tr_reports.report_user_id')
				->get_where('tr_reports', array('report_mode' => 2, 'report_ref_id' => $item_id));
			$arr['reports'] = $query_reports->result_array();
		}
		return $arr;
	}
	
	
	public function get_social_links() {
		$query = $this->db->get('social_icons');
		$arr = array();
		foreach($query->result_array() as $row) {
			$arr[$row['type']] = $row;
		}
		return $arr;
	}
}