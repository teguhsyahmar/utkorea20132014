<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class bendahara extends CI_Controller {

	public function __construct()
    {
        parent::__construct();								
    	//$this->output->enable_profiler(TRUE);
        $this->load->model('person_model','person');
		$this->load->model('finance_model','finance');
		
	    $this->load->library('email');
		$this->email->set_newline("\r\n");		
				
		$this->email->from($this->config->item('mail_from'), $this->config->item('mail_from_name'));  
    }	
	
	public function index()
	{
	       
	}
	
	public function daftar_ulang() 
	{
		$this->auth->check_auth();
		$data = array();					
		$content['page'] = $this->load->view('bendahara/daftar_ulang',$data,TRUE);
        $this->load->view('dashboard',$content);	
	}
	
	public function getlistJQGRID()
	{
		$page = $this->input->post("page", TRUE );
		if(!$page)$page=1;
		
		$rows = $this->input->post("rows", TRUE );
		if(!$rows)$rows=20;
		
		$sort_by = $this->input->post( "sidx", TRUE );
		if(!$sort_by)$sort_by='id';
		
		$sort_direction = $this->input->post( "sord", TRUE );
		if(!$sort_direction)$sort_direction='DESC';
		
		$req_param = array (
            "sort_by" => $sort_by,
			"sort_direction" => $sort_direction,
			"page" => $page,
			"rows" => $rows,
			"search" => $this->input->post( "_search", TRUE ),
			"search_field" => $this->input->post( "searchField", TRUE ),
			"search_operator" => $this->input->post( "searchOper", TRUE ),
			"search_str" => $this->input->post( "searchString", TRUE )
		);

		$data->page = $page;
		$data->records = count ($this->finance->get_list_JQGRID($req_param,"all")->result_array());		
		$records = $this->finance->get_list_JQGRID($req_param,"current")->result_array();
		
		$records2 = array();
		foreach($records as $row){			
			if($row['entry_period'] != ''){
				$row['entry_period'] = calculate_semester($row['entry_period']);
			}else{
				$row['entry_period'] = 'MABA';
			}
			$records2[] = $row;
		}
		
		
		
		$data->total = ceil($data->records /$rows );
		$data->rows = $records2;

		echo json_encode ($data );
		exit( 0 );
	}
	
	function verify_daftar_ulang(){
		$this->form_validation->set_rules('id','id','trim|required');
		if($this->form_validation->run())
		{
			// update tabel reregistration
			$this->finance->update_daftar_ulang($this->input->post('id'),array('is_verified'=>1,'verified_by'=>$this->session->userdata('id'),'verified_time'=>date("Y-m-d H:i:s")));
			echo "";
		}else{
			echo validation_errors();
		}
	}
	
	public function biaya_studi() 
	{
		$this->auth->check_auth();
		$data = array();					
		$content['page'] = $this->load->view('bendahara/biaya_studi',$data,TRUE);
        $this->load->view('dashboard',$content);	
	}
	
	public function get_payment_list()
	{
		$page = $this->input->post("page", TRUE );
		if(!$page)$page=1;
		
		$rows = $this->input->post("rows", TRUE );
		if(!$rows)$rows=20;
		
		$sort_by = $this->input->post( "sidx", TRUE );
		if(!$sort_by)$sort_by='id';
		
		$sort_direction = $this->input->post( "sord", TRUE );
		if(!$sort_direction)$sort_direction='DESC';
		
		$req_param = array (
            "sort_by" => $sort_by,
			"sort_direction" => $sort_direction,
			"page" => $page,
			"rows" => $rows,
			"search" => $this->input->post( "_search", TRUE ),
			"search_field" => $this->input->post( "searchField", TRUE ),
			"search_operator" => $this->input->post( "searchOper", TRUE ),
			"search_str" => $this->input->post( "searchString", TRUE )
		);

		$data->page = $page;
		$data->records = count ($this->finance->get_payment_list($req_param,"all")->result_array());		
		$records = $this->finance->get_payment_list($req_param,"current")->result_array();
		
		$records2 = array();
		foreach($records as $row){			
			if($row['entry_period'] != ''){
				$row['entry_period'] = calculate_semester($row['entry_period']);
			}else{
				$row['entry_period'] = 'MABA';
			}
			$records2[] = $row;
		}
		
		$data->total = ceil($data->records /$rows );
		$data->rows = $records2;

		echo json_encode ($data );
		exit( 0 );
	}

	function verify_payment(){
		$this->form_validation->set_rules('id','id','trim|required');
		if($this->form_validation->run())
		{		
			$this->finance->update_payment($this->input->post('id'),array('is_verified'=>1,'verified_by'=>$this->session->userdata('id'),'verified_time'=>date("Y-m-d H:i:s")));
			echo "";
		}else{
			echo validation_errors();
		}
	}
	
	public function download_receipt($type,$data){
		
			//Daftar Ulang
			$data = explode('n',$data);
			if(strpos($data[1],'UTKOR')!=0){
				$nim = substr($data[1], strlen($data[1]-5), 4);
			}else{
				$nim = $data[1];
			}
			$period = substr($data[0],4,1).'/'.substr($data[0], 0, 4);
			$this->load->model('utility_model','um');
			$res = $this->um->find_Receipt($nim,$period,$type);
			if($res->num_rows()>0){
				$row = $res->row();
				echo $row->id;
			}else{
				return 0;
			}
		
	}
	
	public function receipt($receipt_id,$data) 	{
	    $this->load->helper(array('dompdf', 'file'));
				
		$path = $this->config->item('absolute_path')."assets/core/pdf/receipt/kuitansi_".$receipt_id;
		
		$html = $this->load->view('bendahara/kuitansi', $data, true);
			
		$data = pdf_create($html, $path.".pdf", false);			
		write_file($path.".pdf", $data);
	}
	
	public function change_receipt_status($table,$id){
		$this->form_validation->set_rules('id','id','trim|required');
		if($this->form_validation->run())
		{
			$receipt_id = $this->finance->change_receipt_status($id,$table);
			if ( $receipt_id <> FALSE) return TRUE; else return FALSE;			
		} else {			
			echo validation_errors();
		}
	}
	
	public function mail_receipt($receipt_type,$subject,$type,$name,$email,$receipt_id) {
		$this->email->to($email);
		$this->email->bcc('bendahara@utkorea.org');
		
		$this->email->subject($subject);
        $message = $this->lang->line('send_mail_receipt');
        
		$message = sprintf($message,$name,$type);
		$this->email->message($message);
		$filename = $this->config->item('absolute_path')."assets/core/pdf/receipt/kuitansi_".$receipt_id.".pdf";
		$this->email->attach($filename);
		$this->email->send();		
		
		//echo $this->email->print_debugger();		
	}

	public function sent_receipt_reregistration(){
		$id = $this->input->post('id');
		$registration_id = $this->change_receipt_status('reregistration', $id);
				
		if ($registration_id <> FALSE) {
			$user_id = $this->finance->get_userid_payment($id);
			echo $user_id;
			
			if ($user_id < 10000) {
				//mahasiswa baru
				$user_detail = $this->person->get_mahasiswa_baru_by_reg_code($user_id);
				$data['nim']       = "UTKOR".$user_detail['reg_code'];
				$data['semester']  = "-";
				$data['payment'][0]['type']  = "Pendaftaran Mahasiswa Baru UT Korea Selatan";	
			} else {
				//mahasiswa lama
				$user_detail = $this->person->get_mahasiswa_by_id($user_id);
				$data['nim']       = $user_detail['nim'];
				$data['semester']  = numberToRoman(calculate_semester($user_detail['entry_period']));
				$data['payment'][0]['type']  = "Daftar Ulang Mahasiswa UT Korea Selatan";
			}
			
			
   			$data['name']      = $user_detail['name'];
			$data['major']     = get_major($user_detail['major']); 
			$data['payment'][0]['amount'] = 100000;
			
			$data['current_date']      = convertHumanDate(date("Y-m-d"));
			
			$subject = "Bukti Pembayaran ".$data['payment'][0]['type'];
			
			//$receipt_id => id di tabel receipt
			//Save receipt data to database
			$receipt_period = get_settings('time_period');
			$receipt_period = substr($receipt_period, 4)."/".substr($receipt_period, 0,4);
			$receipt_subject = $data['name']."(".$data['nim'].")";
			
			$receipt_data = array('receipt_period' => $receipt_period,
								  'remarks' => $data['payment'][0]['type'],
								  'receipt_status' => 'M',
								  'subject' => $receipt_subject);
								  
			$receipt_id = $this->finance->save_receipt($receipt_data);
			
			if ($receipt_id <> FALSE) {
				$data['receipt_no'] = $receipt_data['receipt_status']."/".sprintf("%1$03d",$receipt_id)."/KEU/".$receipt_period;
				
				$this->receipt($receipt_id,$data);
				$this->mail_receipt('reregistrant',$subject,$data['payment'][0]['type'],$user_detail['name'],$user_detail['email'],$receipt_id);				
			}
			
		}

	}
		
  	public function sent_receipt_payment(){
		$id = $this->input->post('id');
		$registration_id = $this->change_receipt_status('payment', $id);
				
		if ($registration_id <> FALSE) {
			$user_id = $this->finance->get_userid_payment($id,'payment');
			echo $user_id;
			
			$user_detail = $this->person->get_mahasiswa_by_id($user_id);
			$data['nim']       = $user_detail['nim'];
			$data['semester']  = numberToRoman(calculate_semester($user_detail['entry_period']));
			$data['payment'][0]['type']  = "Pembayaran Biaya Studi Mahasiswa UT Korea Selatan";
			
			
   			$data['name']      = $user_detail['name'];
			$data['major']     = get_major($user_detail['major']); 
			$data['payment'][0]['amount'] = $this->input->post('amount');
			
			$data['current_date']      = convertHumanDate(date("Y-m-d"));
			
			$subject = "Bukti Pembayaran ".$data['payment'][0]['type'];
			
			//$receipt_id => id di tabel receipt
			//Save receipt data to database
			$receipt_period = get_settings('time_period');
			$receipt_period = substr($receipt_period, 4)."/".substr($receipt_period, 0,4);
			$receipt_subject = $data['name']."(".$data['nim'].")";
			
			$receipt_data = array('receipt_period' => $receipt_period,
								  'remarks' => $data['payment'][0]['type'],
								  'receipt_status' => 'M',
								  'subject' => $receipt_subject);
								  
			$receipt_id = $this->finance->save_receipt($receipt_data);
			
			if ($receipt_id <> FALSE) {
				$data['receipt_no'] = $receipt_data['receipt_status']."/".sprintf("%1$03d",$receipt_id)."/KEU/".$receipt_period;
				
				$this->receipt($receipt_id,$data);
				$this->mail_receipt('payment',$subject,$data['payment'][0]['type'],$user_detail['name'],$user_detail['email'],$receipt_id);				
			}
			
		}

	}
    
	function rekap_mahasiswa_lama(){
		$this->auth->check_auth();
		$data = array();					
		$content['page'] = $this->load->view('bendahara/rekap_mahasiswa_lama',$data,TRUE);
        $this->load->view('dashboard',$content);			
	}	
	
	public function get_rekap_mahasiswa_lama($type='all')
	{
		$page = $this->input->post("page", TRUE );
		if(!$page)$page=1;
		
		$rows = $this->input->post("rows", TRUE );
		if(!$rows)$rows=20;
		
		$sort_by = $this->input->post( "sidx", TRUE );
		if(!$sort_by)$sort_by='a.nim';
		
		$sort_direction = $this->input->post( "sord", TRUE );
		if(!$sort_direction)$sort_direction='ASC';
		
		$req_param = array (
            "sort_by" => $sort_by,
			"sort_direction" => $sort_direction,
			"page" => $page,
			"rows" => $rows,
			"search" => $this->input->post( "_search", TRUE ),
			"search_field" => $this->input->post( "searchField", TRUE ),
			"search_operator" => $this->input->post( "searchOper", TRUE ),
			"search_str" => $this->input->post( "searchString", TRUE )
		);

		$data->page = $page;
		$data->records = count ($this->finance->get_rekap_mahasiswa_lama($req_param,"all",$type,get_settings('time_period'))->result_array());		
		$records = $this->finance->get_rekap_mahasiswa_lama($req_param,"current",$type,get_settings('time_period'))->result_array();

		$data->total = ceil($data->records /$rows );
		$data->rows = $records;

		echo json_encode ($data );
		exit( 0 );
	}
	
	public function transport() 
	{
		$this->auth->check_auth();
		$data = array();					
		$content['page'] = $this->load->view('bendahara/transport',$data,TRUE);
        $this->load->view('dashboard',$content);	
	}
	
	public function get_transport_list()
	{
		$page = $this->input->post("page", TRUE );
		if(!$page)$page=1;
		
		$rows = $this->input->post("rows", TRUE );
		if(!$rows)$rows=20;
		
		$sort_by = $this->input->post( "sidx", TRUE );
		if(!$sort_by)$sort_by='id';
		
		$sort_direction = $this->input->post( "sord", TRUE );
		if(!$sort_direction)$sort_direction='DESC';
		
		$req_param = array (
            "sort_by" => $sort_by,
			"sort_direction" => $sort_direction,
			"page" => $page,
			"rows" => $rows,
			"search" => $this->input->post( "_search", TRUE ),
			"search_field" => $this->input->post( "searchField", TRUE ),
			"search_operator" => $this->input->post( "searchOper", TRUE ),
			"search_str" => $this->input->post( "searchString", TRUE )
		);

		$data->page = $page;
		$data->records = count ($this->finance->get_transport_list($req_param,"all")->result_array());		
		$records = $this->finance->get_transport_list($req_param,"current")->result_array();
		
		$data->total = ceil($data->records /$rows );
		$data->rows = $records;

		echo json_encode ($data );
		exit( 0 );
	}

	function verify_transport(){
		$this->form_validation->set_rules('id','id','trim|required');
		if($this->form_validation->run())
		{		
			$this->finance->update_transport($this->input->post('id'),array('is_verified'=>1,'verified_by'=>$this->session->userdata('id'),'verified_time'=>date("Y-m-d H:i:s")));
			echo "";
		}else{
			echo validation_errors();
		}
	}
		
	function get_last_transport(){		
			$res = $this->finance->get_last_transport($this->session->userdata('id'));			
			if($res){
				echo json_encode(array(
					'tanggalttm' => $res->tanggalttm,
					'total' => $res->total,
					'deskripsi' => $res->deskripsi,
					'waktudibayar' => $res->waktudibayar,
					'message'=>''
				));
			}else{
				echo json_encode(array('message'=>'Tidak ada data sebelumnya'));
			}
		
	}	
	
	function rekap_maba(){
		$this->auth->check_auth();
		$data = array();					
		$content['page'] = $this->load->view('bendahara/rekap_maba',$data,TRUE);
        $this->load->view('dashboard',$content);
	}
	
	public function get_rekap_maba()
	{
		$page = $this->input->post("page", TRUE );
		if(!$page)$page=1;
		
		$rows = $this->input->post("rows", TRUE );
		if(!$rows)$rows=20;
		
		$sort_by = $this->input->post( "sidx", TRUE );
		if(!$sort_by)$sort_by='reg_code';
		
		$sort_direction = $this->input->post( "sord", TRUE );
		if(!$sort_direction)$sort_direction='DESC';
		
		$req_param = array (
            "sort_by" => $sort_by,
			"sort_direction" => $sort_direction,
			"page" => $page,
			"rows" => $rows,
			"search" => $this->input->post( "_search", TRUE ),
			"search_field" => $this->input->post( "searchField", TRUE ),
			"search_operator" => $this->input->post( "searchOper", TRUE ),
			"search_str" => $this->input->post( "searchString", TRUE )
		);

		$data->page = $page;
		$data->records = count ($this->finance->rekap_maba_bendahara_kemahasiswaan($req_param,"all")->result_array());		
		$records = $this->finance->rekap_maba_bendahara_kemahasiswaan($req_param,"current")->result_array();
		
		$data->total = ceil($data->records /$rows );
		$data->rows = $records;

		echo json_encode ($data );
		exit( 0 );
	}
	
	public function exportCurrentCRUD_rekap_maba(){
		$page = $this->input->get("page", TRUE );
		if(!$page)$page=1;
		
		$rows = $this->input->get("rows", TRUE );
		if(!$rows)$rows=10;
		
		$sort_by = $this->input->get( "sidx", TRUE );
		if(!$sort_by)$sort_by='reg_code';
		
		$sort_direction = $this->input->get( "sord", TRUE );
		if(!$sort_direction)$sort_direction='ASC';
		
		$req_param = array (
            "sort_by" => $sort_by,
			"sort_direction" => $sort_direction,
			"page" => $page,
			"rows" => $rows,
			"search" => $this->input->get( "_search", TRUE ),
			"search_field" => $this->input->get( "searchField", TRUE ),
			"search_operator" => $this->input->get( "searchOper", TRUE ),
			"search_str" => $this->input->get( "searchString", TRUE )
		);
		
		$this->jqgrid_export->exportCurrent_rekap_maba($req_param);
	}

}