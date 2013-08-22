<?php
	function get_all_usergroup_id(){
		$ci =& get_instance();
		
		$query = $ci->db->get('usergroups');
		
		if ($query->num_rows() > 0) {
			$usergroups = array();
			$i=0;
			foreach ($query->result() as $row) {
				$usergroups[$i]=$row->usergroup_id;
				$i++;
			}
			return $usergroups;
		}else{
			return FALSE;
		}
	}
	
	function menu_editor_menu_list(){
		$ci =& get_instance();
		$usergroups=get_all_usergroup_id();
		$menu = menu($usergroups,0);
		
		$list = "<nav id='main-nav'> \n <ul style='background-position:left bottom;'> \n";
			
		if ($menu != FALSE) {
			
			foreach ($menu as $key => $val) {
				$list .=  "<li style='position:relative'><a href='#' title='' class='".$val['icons']."'>".$val['page']."</a>";
				$list .= "<input class='menu-editor' style='position:absolute;top:12px' type='checkbox' ".($val['active']?"CHECKED":"")." name='menu-".$val['id']."' value='1'>";
				$list .=  "\n <ul>";
				
				if (isset($val['child'])) {
					foreach ($val['child'] as $key2 => $val2) {
						//$list .= "<li><table style='border:none;font-size:100%'><tr style='background-color:transparent'><td style='width:200px;border:none;padding:0px'><a href='#' title='' class='pnc_link'>".$val2['page']."</a></td><td style='border:none;padding:0px'><input type='checkbox' ".($val2['active']?"CHECKED":"")." name='".$val2['page']."' value='1'></td></tr></table></li>";
						$list .= "<li style='position:relative'><a href='#' title='' class='pnc_link'>".$val2['page']."</a><input type='checkbox' class='menu-editor' style='position:absolute;top:3px;left:-20px' ".($val2['active']?"CHECKED":"")." name='menu-".$val2['id']."' value='1'></li>";
					}	
				}			
				
				$list .= "</ul> \n </li>";
			}
		} 
		
		$list .= "</ul>\n </nav>";
		return $list;
	}
?>