<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require_once(FCPATH."vendor/thetechnicalcircle/codeigniter_social_login/src/Social.php");

class Dashboard extends CI_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		//$this->load->helper('language');
		$this->load->model('User_model', 'user_model', TRUE);
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->status = $this->config->item('status');
        $this->roles = $this->config->item('roles');
        $this->load->library('userlevel');

  	}
  
	public function index(){
		$data = $this->session->userdata;
		if(empty($data)){
	        redirect(site_url().'auth/auth/');
	    }

	    //check user level
	    if(empty($data['role'])){
	        redirect(site_url().'auth/auth/');
	    }

	    /* load language */

	    $userLang = $this->session->userdata['current_language'];
  		$this->lang->load($userLang, $userLang);

	    $dataLevel = $this->userlevel->checkLevel($data['role']);
	    //check user level
        
	    $data['title'] = "Dashboard Admin";

        if(empty($this->session->userdata['email'])){
            redirect(site_url().'auth');
        }else{
        	$this->load->view('header', $data);
            $this->load->view('navbar', $data);
            $this->load->view('container');
            $this->load->view('dashboard', $data);
            $this->load->view('footer');

			$this->load->view('language/language');
        }
	}
}
