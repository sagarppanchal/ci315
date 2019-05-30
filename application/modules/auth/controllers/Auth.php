<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require_once(FCPATH."vendor/thetechnicalcircle/codeigniter_social_login/src/Social.php");

class Auth extends CI_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('common');
		

		$this->load->model('User_model', 'user_model', TRUE);
	    $this->load->library('form_validation');
	    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
	    $this->status = $this->config->item('status');
	    $this->roles = $this->config->item('roles');
	    $this->load->library('userlevel');
  	}
  
  /*
    index()
    purpose : login
  */

	public function index(){
		$data = $this->session->userdata;
    $post = $this->input->post();
    
    if(!empty($data['email'])){
        redirect(site_url().'dashboard');
    }else{
          $this->load->library('curl');
          $this->load->library('recaptcha');
          $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
          $this->form_validation->set_rules('password', 'Password', 'required');
          
          $data['title'] = "Welcome Back!";
          
          $result = $this->user_model->getAllSettings();
          $data['recaptcha'] = $result->recaptcha;

          if($this->form_validation->run() == FALSE) {
              $this->load->view('header', $data);
              $this->load->view('container');
              $this->load->view('login');
              $this->load->view('footer');
          }else{
              $post = $this->input->post();
              $clean = $this->security->xss_clean($post);
              $userInfo = $this->user_model->checkLogin($clean);
              
              if($data['recaptcha'] == 'yes'){
                  //recaptcha
                  $recaptchaResponse = $this->input->post('g-recaptcha-response');
                  $userIp = $_SERVER['REMOTE_ADDR'];
                  $key = $this->recaptcha->secret;
                  $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$key."&response=".$recaptchaResponse."&remoteip=".$userIp; //link
                  $response = $this->curl->simple_get($url);
                  $status= json_decode($response, true);
  
                  if(!$userInfo)
                  {
                      $this->session->set_flashdata('flash_message', 'Wrong password or email.');
                      redirect(site_url().'auth');
                  }
                  elseif($userInfo->banned_users == "ban")
                  {
                      $this->session->set_flashdata('danger_message', 'You’re temporarily banned from our website!');
                      redirect(site_url().'auth');
                  }
                  else if(!$status['success'])
                  {
                      //recaptcha failed
                      $this->session->set_flashdata('flash_message', 'Error...! Google Recaptcha UnSuccessful!');
                      redirect(site_url().'auth');
                      exit;
                  }
                  elseif($status['success'] && $userInfo && $userInfo->banned_users == "unban") //recaptcha check, success login, ban or unban
                  {
                      foreach($userInfo as $key=>$val){
                      $this->session->set_userdata($key, $val);
                      }
                      redirect(site_url().'auth/checkLoginUser');
                  }
                  else
                  {
                      $this->session->set_flashdata('flash_message', 'Something Error!');
                      redirect(site_url().'auth');
                      exit;
                  }
              }else{
                  if(!$userInfo)
                  {
                    $this->session->set_flashdata('flash_message', 'Wrong password or email.');
                      redirect(site_url().'auth');
                  }
                  elseif($userInfo->banned_users == "ban")
                  {
                      $this->session->set_flashdata('danger_message', 'You’re temporarily banned from our website!');
                      redirect(site_url().'auth');
                  }
                  elseif($userInfo && $userInfo->banned_users == "unban") //recaptcha check, success login, ban or unban
                  {
                      foreach($userInfo as $key=>$val){
                      $this->session->set_userdata($key, $val);
                      }
                      redirect(site_url().'auth/checkLoginUser');
                  }
                  else
                  {
                      $this->session->set_flashdata('flash_message', 'Something Error!');
                      redirect(site_url().'main/login');
                      exit;
                  }
              }
          }
    }
        
	}


  public function checkLoginUser()
  {
     //user data from session
      $data = $this->session->userdata;
      if(empty($data)){
          redirect(site_url().'auth');
      }

      $this->load->library('user_agent');
      $browser = $this->agent->browser();
      $os = $this->agent->platform();
      $getip = $this->input->ip_address();

      $result = $this->user_model->getAllSettings();
      $stLe = $result->site_title;
      $tz = $result->timezone;

      $now = new DateTime();
      $now->setTimezone(new DateTimezone($tz));
      $dTod =  $now->format('Y-m-d');
      $dTim =  $now->format('H:i:s');

      $this->load->helper('cookie');
      $keyid = rand(1,9000);
      $scSh = sha1($keyid);
      $neMSC = md5($data['email']);
      $setLogin = array(
          'name'   => $neMSC,
          'value'  => $scSh,
          'expire' => strtotime("+2 year"),
      );
      $getAccess = get_cookie($neMSC);

      if(!$getAccess && $setLogin["name"] == $neMSC){
          $this->load->library('email');
          $this->load->library('sendmail');
          $bUrl = base_url();
          $message = $this->sendmail->secureMail($data['first_name'],$data['last_name'],$data['email'],$dTod,$dTim,$stLe,$browser,$os,$getip,$bUrl);
          $to_email = $data['email'];
          $this->email->from($this->config->item('register'), 'New sign-in! from '.$browser.'');
          $this->email->to($to_email);
          $this->email->subject('New sign-in! from '.$browser.'');
          $this->email->message($message);
          $this->email->set_mailtype("html");
          $this->email->send();
          
          $this->input->set_cookie($setLogin, TRUE);
          redirect(site_url().'dashboard');
      }else{
          $this->input->set_cookie($setLogin, TRUE);
          redirect(site_url().'dashboard');
      }
  }

  public function logout()
  {
      $this->session->sess_destroy();
      redirect(site_url().'auth');
  }
	
	private function login_facebook() {
		die("qwer");
		$site_url = $this->config->item('base_url');
		$fb_App_id = "YOUR FB APP ID";
		$fb_secret = "YOUR FB APP SECRET";
		$fb_scope = "public_profile,email,user_friends";
		$social_instance = new Social();
		$fbData = $social_instance->facebook_connect(NULL,$this->session,$site_url,$fb_App_id,$fb_secret,$fb_scope);
		if(!empty($fbData['redirectURL'])) {
			redirect($fbData['redirectURL']);
		} else {
			if(!empty($fbData['id'])) {
				echo "<pre>";
				print_r($fbData);
				echo "</pre>";die; /* all the data returned by facebook will be in this variable (Array). Play with it. */
			}
		}
	}
	
	private function login_twitter() {
  		$site_url = $this->config->item('base_url')."/";
  		$client_id = "YOUR TWITTER CLIENT ID";
  		$client_secret = "YOUR TWITTER CLIENT SECRET";
  		$social_instance = new Social();
  		$twtData = $social_instance->twitter_connect($client_id,$client_secret,$site_url);
  		if(!empty($twtData['redirectURL'])) {
  			redirect($twtData['redirectURL']);
  	  	} else {
  			if(!empty($twtData['id'])) {
  				echo "<pre>";print_r($twtData);echo "</pre>";die();
  			}
  		}
  	}
  
  	private function login_linkedin() {
		$site_url = $this->config->item('base_url')."/";
		$client_id = "YOUR LINKED IN CLIENT ID";
		$client_secret = "YOUR LINKED IN SECRET";
		$social_instance = new Social();
		$ldnData = $social_instance->linkedin_connect(NULL,$site_url,$client_id,$client_secret);
		if(!empty($ldnData['redirectURL'])) {
			 redirect($ldnData['redirectURL']);
		} else {
			if(!empty($ldnData['id'])) {
				echo "<pre>";print_r($ldnData);echo "</pre>";die();
		  	}
		}
	}
  
  	private function login_gmail() {
		$site_url = $this->config->item('base_url')."/";
		$client_id = "YOUR GMAIL CLIENT ID";
		$client_secret = "YOUR GMAIL CLIENT SECRET";
		$client_api_key = "GMAIL API KEY";
		$social_instance = new Social();
		$gmailData = $social_instance->gmail_connect(NULL,$site_url,$client_id,$client_secret,$client_api_key);
		if(!empty($gmailData['redirectURL'])) {
			redirect($gmailData['redirectURL']);
		} else {
			if(!empty($gmailData['email'])) {
				echo "<pre>";print_r($gmailData);echo "</pre>";die();
			}
		}
	}
	
	private function login_yahoo() {
  		$site_url = $this->config->item('base_url')."/";
  		$social_instance = new Social();
  		$yahooData = $social_instance->yahoo_connect($site_url);
  		if(!empty($yahooData['redirectURL'])) {
  			redirect($yahooData['redirectURL']);
  		} else {
  			if(!empty($yahooData['email'])) {
  				echo "<pre>";print_r($yahooData);echo "</pre>";die();
  			}
  		}
  	}
  
  	private function login_foursquare() {
  		$site_url = $this->config->item('base_url')."/";
  		$client_id = "FOURSQUARE CLIENT ID";	
  		$client_secret = "FOURSQUARE CLIENT SECRET";
  		$social_instance = new Social();
  		$fsData = $social_instance->foursquare_connect($client_id,$client_secret,$site_url);
  		if(!empty($fsData['redirectURL'])) {
  			redirect($fsData['redirectURL']);
  		} else {
  			if(!empty($fsData['id'])) {
  				echo "<pre>";print_r($fsData);echo "</pre>";die();
  			}
  		}
  	}
}
