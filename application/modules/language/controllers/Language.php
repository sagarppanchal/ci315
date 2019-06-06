<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require_once(FCPATH."vendor/thetechnicalcircle/codeigniter_social_login/src/Social.php");

class Language extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('language');
		$this->load->database();
  	}
  
	public function changeLanguage()
	{
		$sdata = $this->session->userdata;
		$postData=$this->input->post("lang");
		$updateArray=array("current_language"=>$postData);
		$this->db->where('id', $sdata['id']);
		$res=$this->db->update('users',$updateArray);
		$this->session->set_userdata($updateArray);
		//pr($this->session->userdata);
		echo res($res);
		die;
	}
}
