<?php
/*
	view_loader($view, $vars=array(), $output = false)
	Purpose : For loding views
*/
if(!function_exists('view_loader')){
  function view_loader($view, $vars=array(), $output = false){
    $CI = &get_instance();
    return $CI->load->view($view, $vars, $output);
  }
}