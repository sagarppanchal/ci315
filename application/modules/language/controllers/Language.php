<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require_once(FCPATH."vendor/thetechnicalcircle/codeigniter_social_login/src/Social.php");

class Language extends CI_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->model('User_model', 'user_model', TRUE);
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->status = $this->config->item('status');
        $this->roles = $this->config->item('roles');
        $this->load->library('userlevel');
        $this->load->helper('language');
  	}
  
	public function changeLanguage(){
		die("yeah");
		$data = $this->session->userdata;
		if(empty($data)){
	        redirect(site_url().'auth/auth/');
	    }

	    //check user level
	    if(empty($data['role'])){
	        redirect(site_url().'auth/auth/');
	    }
	}

	
}
