<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class superuser extends CI_Controller {

	public function __construct()
    {
        parent::__construct();								
    	$this->output->enable_profiler(TRUE);
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
	
	function check_and_fix_student_class(){
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('nim', 'NIM', 'required|numeric');
		$this->form_validation->set_rules('periode', 'Periode', 'required|numeric');
		$data['message_type'] = 0;
		$data['message'] = '';
		if($this->form_validation->run()){
			$student = $this->utility->get_Table('mahasiswa',array('nim'=>$this->input->post('nim')));
			if($student->num_rows()>0){
				$unins = array();
				$student = $student->result_array();				
				$res = $this->utility->check_valid_class($this->input->post('nim'),$this->input->post('periode'),calculate_semester($student[0]['entry_period']),$student[0]['region']);
				if($res->num_rows()>0){
					$res = $res->result_array();					
					$res2 = $this->utility->get_Table('class',array('id_student'=>$this->input->post('nim')),'id_assignment');
					if($res2->num_rows()>0){						
						$res2 = $res2->result_array();
						
						foreach($res as $row){
							if(!in_array($row['id'],$res2[0])){
								$unins[] = array('id_student'=>$this->input->post('nim'),'id_assignment'=>$row['id']);
							}
						}						
						if(count($unins)>2){
							//Deleting All class for this student
							$this->utility->remove_from_Table('class',array('id_student'=>$this->input->post('nim')));
						}
						if(count($unins)>0){
							$this->utility->insert_batch_Table('class',$unins);	
							$data['message'] .= 'Berhasil Memperbaiki '.count($unins).' Kelas untuk Student NIM = '.$this->input->post('nim');								
						}else{
							$data['message'] .= 'Kelas Sudah sesuai untuk Student NIM = '.$this->input->post('nim');							
						}
					}else{
						$data['message'] .= 'Berhasil Menambahkan <b>3</b> Kelas untuk Student NIM = '.$this->input->post('nim');						
						foreach($res as $row){
							$unins[] = array('id_student'=>$this->input->post('nim'),'id_assignment'=>$row['id']);	
						}
						$this->utility->insert_batch_Table('class',$unins);							
					}					
				}
				
			}			
		}		
		$data = array();
		$content['page'] = $this->load->view('superuser/checkfixclass',$data,TRUE);
        $this->load->view('dashboard',$content);
	}
}