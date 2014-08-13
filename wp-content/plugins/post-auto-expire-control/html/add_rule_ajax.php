<?php 
		$newrole = $_POST['role'];
		$newcpt = $_POST['cpt'];
		$newstatus = $_POST['status'];
		$newday =$_POST['day'];
		$c = $_POST['counter'];
		global $wp_roles;
		$post_types=get_post_types('','names'); 
		unset($post_types['revision']); unset($post_types['attachment']);unset($post_types['nav_menu_item']);
		$actions = array("", "pending", "draft", "trash", "delete");
		
		$html = '<tr style="background:#BEF781"><td><input type="hidden" id="counter" name="counter" value="'.$c.'"/>';
		$html .= '<select id="therole" name="apext[rules]['.$c.'][role]">';
        $html .= '<option value=""></option>';
              foreach($wp_roles->role_names as $role => $name  ) {
			  $selected = ($newrole==strtolower($name)) ? 'selected="selected"' : '';
	    $html .=  '<option value="'.strtolower($name).'" '.$selected.'>'.$name.'</option>';}
        $html .= '</select><br></td>';
        
		$html .= '<td><select id="thecpt" name="apext[rules]['.$c.'][cpt]" >';
        $html .= '<option value=""></option>';
             foreach($post_types as $post_type ) {
			 $selected = ($newcpt==$post_type) ? 'selected="selected"' : '';  
	   $html .=  "<option value='$post_type' $selected>$post_type</option>";}
       $html .= '</select><br></td>';
       
       $html .= '<td><select id="theaction" name="apext[rules]['.$c.'][action]">';
	         foreach($actions as $action) {
		     $selected = ($newstatus==$action) ? 'selected="selected"' : '';
	   $html .="<option value='$action' $selected>$action</option>";}
	   $html .= '</select><br></td>';
	   $html .= '<td><input type="text" id="theday" name="apext[rules]['.$c.'][day]" value="'.$newday.'"/><br></td>';
	   $html .= '<td><span class="remove_rule button-secondary">'.__("Remove","Apext").'</span></td></tr>';
		echo $html;

?>
