<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('test_method'))
{
    function pr($data)
    {
    	echo '<pre>';
    	print_r($data);
        return $var;
    }   
}