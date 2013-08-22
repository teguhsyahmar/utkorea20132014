<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class superuser extends CI_Controller {

	public function __construct()
    {
        parent::__construct();								
    	//$this->output->enable_profiler(TRUE);
		$this->load->helper('superuser_helper');
		$this->load->model('utility_model','utility');
				
	    $this->load->library('email');
		$this->email->set_newline("\r\n");		
				
		$this->email->from($this->config->item('mail_from'), $this->config->item('mail_from_name'));  
    }	
	
	public function index()
	{
	    $this->auth->check_auth();	   
	}
	
	public function manajemen_menu(){
		$this->auth->check_auth();	   
		$data['list_menu'] = menu_editor_menu_list();	
		$content['page'] = $this->load->view('superuser/manajemen_menu',$data,TRUE);
        $this->load->view('dashboard',$content);	
	}
	
	function update_menu_status(){
		$result=$this->utility->update_menu_status(str_replace("menu-","",$this->input->post('name')),$this->input->post('status')=='true'?1:0);
	}

}