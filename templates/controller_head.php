<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ResourcesClassName extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('ResourceClassName');
        $this->load->helper('base');
        crud_rest_support();
    }
