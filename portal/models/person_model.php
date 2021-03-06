<?php
class person_model extends CI_Model {
    

    function __construct()
    {        
        parent::__construct();
    }
	

	function delete_mahasiswa($id)
	{
		return $this->db->delete('mahasiswa',array('nim'=>$id));
	}
	
	function update_mahasiswa($nim,$col)
	{
		$this->db->where('nim',$nim);
		return $this->db->update('mahasiswa',$col);
	}
	
	function add_mahasiswa($col)
	{
		return $this->db->insert('mahasiswa',$col);
	}
	

	function get_list_mahasiswa($option = "all")
	{
		if ($option == 'active') {
			$this->db->where('status',1);	
		}
		
		$query = $this->db->get('mahasiswa');
		
		if ($query->num_rows() > 0) {
			return $query->result();	
		}
	}		
	

	function check_nim($nim)
	{
		
		$this->db->where("nim", $nim); 
		$result = $this->db->get('mahasiswa');
		if($result->num_rows())return true;
		return false;
	}
	

	function get_mahasiswa_sms($select='*', $where = NULL)
	{
		$this->db->distinct();
		$this->db->select($select);
		if (!is_null($where)) {
			$this->db->where_in('nim',$where);
		}
		$this->db->where("phone IS NOT ", "NULL",false); 
        $this->db->where("status", "1");
		
		$query = $this->db->get('mahasiswa');
		
		if($query->num_rows() > 0) {
			return $query->result();
		}
	}
    
    function get_staff_sms($select='*', $where = NULL)
	{
		$this->db->distinct();
		$this->db->select($select);
		if (!is_null($where)) {
			$this->db->where_in('staff_id',$where);
		}
		$this->db->where("phone IS NOT ", "NULL",false); 
		$this->db->where("is_active ", 1,false); 
		$query = $this->db->get('staff');
		
		if($query->num_rows() > 0) {
			return $query->result();
		}
	}
    
	
	function save_history_sms($userid,$apimsgid,$phone,$message) {
		$data = array('userid'=>$userid,
					  'apimsgid' => $apimsgid,
					  'phone' => $phone,
					  'message' => $message		
		);
		
		$this->db->insert('history_sms',$data);
		
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function get_sms_history() {
		$this->db->order_by("history_id", "desc"); 
		$query = $this->db->get('history_sms');
		
		if($query->num_rows() > 0) {
			return $query->result();
		}
	}
	

	function get_list_JQGRID($type,$params = "" , $page = "all",$is_export=false,$major="",$entry_period="",$region="",$active_only = false)
	{	
		if($type=='staff'){
			$this->db->select("staff_id,name,phone,email,affiliation");
			$this->db->from("staff");
			$this->db->where("group_id IS NOT ","NULL",false);		
		}elseif($type=='baru'){
			//$this->db->select("mahasiswa.name,mahasiswa.nim,mahasiswa.phone,mahasiswa.major,mahasiswa.region,mahasiswa.status,mahasiswa.gender,mahasiswa.entry_period,mahasiswa.email,mahasiswa.birth_date,mahasiswa.address_id,mahasiswa.address_kr,mahasiswa.religion,mahasiswa.marital_status,mahasiswa.remarks,mahasiswa_baru.reg_time,mahasiswa_baru.verified");
            $this->db->select("name,CONCAT('UTKOR',reg_code) as nim,phone,major,region,status,gender,period,email,birth_date,address_id,address_kr,religion,marital_status,DATE_FORMAT(reg_time, '%d-%m-%Y') as reg_time,verified",FALSE);
            $this->db->from("mahasiswa_baru");			
			//$this->db->from("mahasiswa");
			//$this->db->join('mahasiswa_baru','mahasiswa.nim = mahasiswa_baru.reg_code','left');
			//$this->db->where("length(nim)<5");
		}elseif($type=='baru_default'){
			$this->db->select("name,reg_code,passport,phone,major,region,status,gender,period,email,birth_date,address_id,address_kr,religion,mother_name,year_graduate,marital_status,DATE_FORMAT(reg_time, '%d-%m-%Y') as reg_time,verified",FALSE);
            $this->db->from("mahasiswa_baru");			
		}elseif($type=='tutor'){
			if($is_export){
				$this->db->select("staff_id,name,phone,email,affiliation,region,major.major as major,birth,is_active");
				$this->db->from("staff");
				$this->db->join('major','staff.major_id = major.major_id');
				$this->db->where("staff.major_id IS NOT ","NULL",false);
			}else{
				$this->db->select("name,phone,email,affiliation,region,a.major_id as major_id,birth,staff_id,is_active");
				$this->db->from("staff a");
				$this->db->join('major b','a.major_id = b.major_id');
				$this->db->where("a.major_id IS NOT ","NULL",false);
			}
			
		}else{
		    if (! empty($major)) {
		        $this->db->where("major",$major);
		    }
            
            if (! empty($entry_period)) {
		        $this->db->where("entry_period",$entry_period);
		    }
            
            if (! empty($region)) {
		        $this->db->where("region",$region);
		    }
            
			if($active_only){
            	$this->db->where('status','1');
            }
            
            $this->db->order_by('name');
            
            
			if($is_export){
				$this->db->select("name,nim,phone,major.major as major,region,status,gender,entry_period,email,birth_date,address_id,address_kr,religion,marital_status,remarks");
				$this->db->from("mahasiswa");
				$this->db->join('major','mahasiswa.major = major.major_id');
			}else{
				$this->db->select("name,nim,phone,major,region,status,gender,entry_period,email,birth_date,address_id,address_kr,religion,marital_status,remarks");
				$this->db->from("mahasiswa");			
			}
		}
		
		if (!empty($params))		{			
			if ( (($params["rows"]*$params["page"]) >= 0 && $params ["rows"] > 0))
			{
				$ops = array (
							"eq" => "=",
							"ne" => "<>",
							"lt" => "<",
							"le" => "<=",
							"gt" => ">",
							"ge" => ">="
				);										
				
				if(!empty($params['search_field'])){
					if($params['search_operator']=='cn'||$params['search_operator']=='nc'){
						if($params['search_operator']=='cn'){
							$this->db->like($params['search_field'],$params['search_str']);
						}else{
							$this->db->not_like($params['search_field'],$params['search_str']);
						}
					}else{
						$this->db->where ($params['search_field'].' '.$params['search_operator'], $params['search_str']);
					}
					
				}
				
				$this->db->order_by($params['sort_by'], $params ["sort_direction"] );


				if ($page != "all")
				{
					$this->db->limit ($params ["rows"], $params ["rows"] *  ($params ["page"] - 1) );
				}

				$query = $this->db->get();
				

			}
		}
		else
		{			
				$this->db->limit (5);
				$query = $this->db->get();

		}

		return $query;
	}
	
	function registration_process($data) {
		$insert = populate_form($data, 'mahasiswa_baru');
		
		$insert['birth_date'] = $data['year_birth']."-".$data['month_birth']."-".$data['day_birth'];
		$insert['phone'] = "82".$data['phone'];
        $insert['period'] = "20132";
        
		$this->db->set('reg_time', 'now()', FALSE);
		$this->db->set('uid', 'uuid()', FALSE);
						
		$this->db->insert('mahasiswa_baru',$insert);
		
		if ($this->db->affected_rows() > 0) {
			$uuid = id_to_uuid($this->db->insert_id());
			$row = array('id'=>$this->db->insert_id(), 'uuid'=>$uuid,'name'=>$data['name'], 'email'=>$data['email']);
			return $row;
		} else {
			return FALSE;
		}
	}

	function education_list($limit=NULL,$offset=NULL) 
	{
		//$this->db->limit($limit,$offset);
		$query = $this->db->get('education_list');
		
		if($query->num_rows() > 0) {
			return $query->result();
		} else {
			return FALSE;
		}
	}
	
	function total_education_list() 
	{
		$query = $this->db->get('education_list');
		
		return $query->num_rows();
	}
	
	function password_check($table,$password,$username='')
	{
		if (empty($username)) {
			$username = $this->session->userdata('username');
		}
		
		if($table=='mahasiswa'){
			$where = array ('password'=>hashPassword($password),'nim'=>$username);
		}else{
			$where = array ('password'=>hashPassword($password),'username'=>$username);	
		}
		
		$query = $this->db->get_where($table,$where);
	
		if ($query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function change_password($table,$password,$username='') {
		if (empty($username)) {
			$username = $this->session->userdata('username');
		}
		
		$set = array ('password'=>hashPassword($password));
		if($table=='mahasiswa'){
			$where = array ('nim'=>$username);
		}else{
			$where = array ('username'=>$username);
		}
	
		$query = $this->db->update($table,$set,$where);
	
		if ($this->db->affected_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function delete_tutor($id)
	{
		return $this->db->delete('staff',array('staff_id'=>$id));
	}
	
	function update_tutor($nim,$col)
	{
		if(is_numeric($nim)){
			$this->db->where('staff_id',$nim);
		}else{
			$this->db->where('username',$nim);
		}
		return $this->db->update('staff',$col);
	}
	
	function add_tutor($col)
	{
		$colnew = $col;
		
		$colnew['username'] = substr(str_replace(" ","",$col['name']),0,6);
		$colnew['password'] = hashPassword($colnew['username']);
		$colnew['group_id'] = 8;
		
		return $this->db->insert('staff',$colnew);
	}
	
	function check_null_field($id,$table = 'mahasiswa') {
		$this->db->where('nim',$id);
		$query = $this->db->get($table);
		
		$ignore_list = array('teach','teach_at','teach_major','verified','verified_by','verified_time','remarks');
		
		$empty_val = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				foreach ($row as $key => $value ) {
					if (is_null($value)) {
						if(! in_array($key,$ignore_list)) $empty_val[] = $key;
					}
				}
			}
		}
		
		return $empty_val;
	}
	
	function get_new_student_details($id) {
		$this->db->where('reg_code',$id);
		$query = $this->db->get('mahasiswa_baru');
		
		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			return false;
		}	
	}
	
	function new_student_list($filter='') {
		$this->db->select("CONCAT('UTKOR',`reg_code`) as `reg_code`",FALSE);
		$this->db->select("uid,name,email,verified,verified_by,verified_time,reg_time");
		$query = $this->db->get('mahasiswa_baru');
		
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function check_registration_status($reg_code) 
	{
		$this->db->select('name,status,verified');
		$query = $this->db->get_where('mahasiswa_baru',array('reg_code' => $reg_code));
		
		if ( $query->num_rows() > 0 ) 
		{
			$row = $query->row();
			$data = "Nama    : ".$row->name."<br />";
			$data .= "Status    : ";
			
			if (empty($row->verified)) {				
				$data .= "Dokumen Belum Diterima/ Menunggu Verifikasi Dokumen";
			} else if ($row->verified == 1) {
				$data .= "Dokumen Sudah di Verifikasi";
			}
			
			 
		} else {
			$data = false;
		} 
		
		return $data;
			
	}	
	
	function get_field_value_by_id($id,$fieldname)
	{
		$this->db->select($fieldname);
		$this->db->where('nim',$id);
		$result = $this->db->get('mahasiswa');
		if($result->num_rows()>0)
		{
			return $result->row();
		}else{
			return false;
		}
	}
	
	function get_mahasiswa_by_id($id)
	{
		$this->db->where('nim',$id);
		$result = $this->db->get('mahasiswa');
		if($result->num_rows()>0)
		{
			return $result->row_array();
		}else{
			return false;
		}
	}
	
	function get_mahasiswa_baru_by_reg_code($reg_code)
	{
		$this->db->where('reg_code',$reg_code);
		$result = $this->db->get('mahasiswa_baru');
		if($result->num_rows()>0)
		{
			return $result->row_array();
		}else{
			return false;
		}
	}
	
	function edit_mahasiswa_baru($reg_code,$val)
	{
		$this->db->where('reg_code',$reg_code);
		return $this->db->update('mahasiswa_baru',$val);
	}
	
	function get_staff_by_username($username)
	{
		$this->db->where('username',$username);
		$result = $this->db->get('staff');
		if($result->num_rows()>0)
		{
			return $result->row_array();
		}else{
			return false;
		}
	}
	
	function mahasiswa_semester($id) {
		$this->db->select('entry_period');
		$this->db->where('nim',$id);
		$query = $this->db->get('mahasiswa');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$user_period = $row->entry_period;
			
			return calculate_semester($user_period);
		}
	}
	
	function max_semester() {
		$this->db->select_min('entry_period');
		$query = $this->db->get('mahasiswa');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$user_period = $row->entry_period;
			
			return calculate_semester($user_period);
		}
		
	}
	
	function distinct_semester() {
		$this->db->distinct();
		$this->db->select('entry_period');
		$query = $this->db->get('mahasiswa');
		
		if ($query->num_rows() > 0) {
			$data = array();
			foreach ( $query->result() as $row ) {
				$data[$row->entry_period] = calculate_semester($row->entry_period);
			}		
			return $data;			
		}
		
	}
	
	function field_export_data() {
		$fields = $this->db->list_fields('mahasiswa');
		
		$ignore = array('password','major','last_education_major','teach','teach_at','teach_major','ijasah_image','photo_image','entry_period');
		
		$row = array();
		
		foreach ($fields as $field)
		{
			if (! in_array($field, $ignore)) $row[] = $field;
		}
		
		return $row;
	}

	function export_data() {
		$select = array('major','entry_period');
		$select = array_merge($select,$this->input->post('row'));
		$select = implode(',',$select);
		
		$this->db->select($select);
		
		if ($this->input->post('major') != 0) {
			$this->db->where('major',$this->input->post('major'));
		}
		

		if ($this->input->post('period') != 0) {
			$this->db->where('entry_period',$this->input->post('period'));
		}
		
		if ($this->input->post('status') != 0) {
			$this->db->where('status',$this->input->post('status'));
		}
		
		$this->db->order_by('entry_period');
		$this->db->order_by('major');
		
		$query = $this->db->get('mahasiswa');
		
		if ($query->num_rows() > 0 ) {
			$data = array();
			$i = 0;
			foreach ($query->result_array() as $row) {
				$entry_period = $row['entry_period'];
				$major = $row['major'];
							
				$data[$entry_period][$major][$i] = $row;
				
				$i++;
			}
			
			return $data;			
		}
		
	}
	
	function check_email($email)
	{
		
		$this->db->where("email", $email); 
		$result = $this->db->get('mahasiswa_baru');
		if($result->num_rows())return true;
		return false;
	}
    
    function get_active_semester($major)
    {
        $this->db->distinct();
        $this->db->select('entry_period');
        $this->db->where('status','1');
        $this->db->where('major',$major);
        $this->db->order_by('entry_period',"desc");
        
        $query = $this->db->get('mahasiswa');
        
        if ($query->num_rows() > 0) {
            return $query->result();
        }
    }
    
    function get_total_student($major,$entry_period,$region)
    {
        $where = array('status' => '1','major'=>$major,'entry_period'=>$entry_period,'region'=>$region);
        $this->db->where($where);
        
        return $this->db->count_all_results('mahasiswa');

    }
    
    function check_username($username)
	{
		
		$this->db->where("username", $username); 
		$result = $this->db->get('staff');
		if($result->num_rows())return true;
		return false;
	}
    
    function check_recover_active($username) {
		$where = array('username' => $username,'active' => 1);
        $this->db->where("expire <= ","now()",false);
        $this->db->where($where);
		$query = $this->db->get("forgot_temp");		
		
		if ($query->num_rows()>0)
			return FALSE;
		else
			return TRUE;
  }
  
  function activationKey($username,$key) {   		   
        $data = array('activationkey'=>$key,
                      'username' => $username,					  
					  'active' => 1   
		 );
		 
		 $this->db->set('expire', 'UNIX_TIMESTAMP(date_add(now(),INTERVAL 7 DAY))',false);
		 $this->db->set($data);

         $this->db->insert('forgot_temp');  	   
   }
   
   function recover_information($key) {
		$where = array('activationkey' => $key);
        $this->db->where($where);
		$query = $this->db->get('forgot_temp',$where);		
		
		if ($query->num_rows()>0) {
			$row = $query->row_array();
   			return $row;			
		}else {
			return FALSE;	
		}	
  }
  
  function setRandomPass($id,$idfieldname,$table,$pass) {
		
        $update = array ('password'=>hashPassword($pass));
		$where = array ($idfieldname=>$id);
		$query = $this->db->update($table,$update,$where);
		
		if ($this->db->affected_rows() > 0) {
			return TRUE;	
		} else {
			return FALSE;
		}
	}
	
	function changeStatusRecovery($key) {
		$update = array ('active'=>'0');
		$where = array ('activationkey'=>$key);
		$query = $this->db->update('forgot_temp',$update,$where);
		
		if ($this->db->affected_rows() > 0) {
			return TRUE;	
		} else {
			return FALSE;
		}	
	}			
	
}