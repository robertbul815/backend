<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {
	public function index() {
	}
	public function edit($edit_id = NULL) {
		$requests = $this->input->post();
		$response = array();
		if(isset($requests['tag']) && isset($requests['remove_id'])) {
			$this->api_model->remove_user($requests['remove_id']);
			$response['result'] = 1;
		}
		$page_title = 'Registered Users';
		$page_link = 'users/edit';
		$breadcrumb = array(
				'Registered Users' => $page_link
		);
		$data = array();
		$data['users'] = $this->be_model->get_users();
		if(isset($edit_id)) $data['edit_id'] = $edit_id;
		
		$data_param = array('page_title' => $page_title, 'breadcrumb' => $breadcrumb, 'data' => $data);
		$this->be_page->generate(true, $page_link, $data_param, $response);
	}
}