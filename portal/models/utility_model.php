<?php
class utility_model extends CI_Model {
    

    function __construct()
    {        
        parent::__construct();
    }
	
	function get_Table($table,$where="",$select="*")
	{
		if($select!="*"){
			$this->db->select($select);}
		if($where!=""){
			$this->db->where($where);}
		return $this->db->get($table);		
	}	
	
	function find_Receipt($nim,$period,$type){
		$this->db->like('subject',$nim,'both');
		$this->db->where('receipt_period',$period);
		if($type=='du'){
			$this->db->not_like('remarks','Biaya Studi','both');
		}else{
			$this->db->like('remarks','Biaya Studi','both');
		}
		return $this->db->get('receipt');
	}
	
	function get_Table_like($table,$field,$like,$wildcard='both'){
		$this->db->like($field,$like,$wildcard);
		return $this->db->get($table);
		
	}	
	
	function update_menu_status($id,$status){
		$this->db->where('id',$id);
		$this->db->update('permissions',array('menu' => $status));
		$this->db->select('menu');
		$this->db->where('id',$id);
		return $this->db->get('permissions')->row()->menu;		
	}
	
	function remove_from_Table($table,$where){		
		$this->db->delete($table,$where);
	}
	
	function insert_batch_Table($table,$data){
		$this->db->insert_batch($table,$data);
	}
	
	function check_valid_class($nim,$periode,$semester,$region){
		
		$this->db->select('a.id');
		$this->db->from('assignment a');
		$this->db->join('courses b','a.course_id = b.course_id');
		$this->db->join('mahasiswa c','c.major = b.major');
		$this->db->where('c.nim',$nim);
		$this->db->where('b.semester',$semester);
		$this->db->where('a.region',$region);
		$this->db->where('a.time_period',$periode);
		
		return $this->db->get();
	}
}