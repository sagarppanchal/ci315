<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	pr($array)
	purpose : to print data
	author : sagarppanchal:04-05-2019
*/
if ( ! function_exists('pr'))
{
    function pr($data)
    {
    	echo '<pre>';
    	print_r($data);
    }   
}

/*
	res($array)
	purpose : to return json data from ajax
	author : sagarppanchal:04-05-2019
*/

if ( ! function_exists('res'))
{
    function res($data)
    {
    	echo json_encode($data);
    }   
}

/*
    tr($string)
    purpose : to translate a string
    author : sagarppanchal:04-05-2019
*/

if ( ! function_exists('_tr'))
{
    function _tr($string)
    {
        $ci =& get_instance();
        return $ci->lang->line($string);
    }   
}

/*
    _enc($string)
    purpose : to encrypt data using default CI method
    author : sagarppanchal:05-05-2019
*/

if ( ! function_exists('_enc'))
{
    function _enc($string)
    {
        $ci =& get_instance();
        return $ci->encryption->encrypt($string);
    }
}

/*
    _enc($string)
    purpose : to decrypt data using default CI method
    author : sagarppanchal:05-05-2019
*/

if ( ! function_exists('_decrypt'))
{
    function _decrypt($string)
    {
        $ci =& get_instance();
        return $ci->encryption->decrypt($string);
    }
}

/*
    _loadLang()
    purpose : load language
    author : sagarppanchal:05-05-2019
*/

if ( ! function_exists('_loadLang'))
{
    function _loadLang()
    {
        $ci =& get_instance();
        $userLang = $ci->session->userdata['current_language'];
        $ci->lang->load($userLang, $userLang);
    }
}

