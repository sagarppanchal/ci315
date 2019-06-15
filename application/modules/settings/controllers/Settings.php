<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require_once(FCPATH."vendor/thetechnicalcircle/codeigniter_social_login/src/Social.php");

class Settings extends CI_Controller 
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
        //_loadLang();
  	}
  
	public function index()
    {
	    $data = $this->session->userdata;
        if(empty($data['role'])){
	        redirect(site_url().'auth');
	    }
	    $dataLevel = $this->userlevel->checkLevel($data['role']);
	    //check user level

        $data['title'] = "Settings";
        $this->form_validation->set_rules('site_title', 'Site Title', 'required');
        $this->form_validation->set_rules('timezone', 'Timezone', 'required');
        $this->form_validation->set_rules('recaptcha', 'Recaptcha', 'required');
        $this->form_validation->set_rules('theme', 'Theme', 'required');

        $result = $this->user_model->getAllSettings();
        $data['id'] = $result->id;
	    $data['site_title'] = $result->site_title;
	    $data['timezone'] = $result->timezone;
	    
	    if (!empty($data['timezone']))
	    {
	        $data['timezonevalue'] = $result->timezone;
	        $data['timezone'] = $result->timezone;
	    }
	    else
	    {
	        $data['timezonevalue'] = "";
            $data['timezone'] = "Select a time zone";
	    }
	    
	    if($dataLevel == "is_admin"){
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header', $data);
                $this->load->view('navbar', $data);
                $this->load->view('container');
                $this->load->view('settings', $data);
                $this->load->view('footer');
            }else{
                $post = $this->input->post(NULL, TRUE);
                $cleanPost = $this->security->xss_clean($post);
                $cleanPost['id'] = $this->input->post('id');
                $cleanPost['site_title'] = $this->input->post('site_title');
                $cleanPost['timezone'] = $this->input->post('timezone');
                $cleanPost['recaptcha'] = $this->input->post('recaptcha');
                $cleanPost['theme'] = $this->input->post('theme');
    
                if(!$this->user_model->settings($cleanPost)){
                    $this->session->set_flashdata('flash_message', 'There was a problem updating your data!');
                }else{
                    $this->session->set_flashdata('success_message', 'Your data has been updated.');
                }
                redirect(site_url().'settings');
            }
	    }

	}
}
