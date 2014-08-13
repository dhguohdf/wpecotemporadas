<?php
			global $wp_roles;global $pagenow; 
			$post_types=get_post_types('','names'); 
			unset($post_types['revision']); unset($post_types['attachment']);unset($post_types['nav_menu_item']);
			$actions = array("pending", "draft", "trash", "delete");
			?>
			<div class="wrap">  
			<div id="icon-themes" class="icon32"></div>  
			<h2><?php _e('Posts Auto Expire Control', 'Apext');?></h2>  
			
			
			
			<?php settings_errors(); ?>  
			
                   
			<?php $this->add_new_rule_form(); ?>
			<div class="clear"></div>
			<?php 
				if(isset($_GET['error'])){
					if ($pagenow == 'options-general.php' && $_GET['page'] =='apext_setting' &&  $_GET['error'] == 'duplicate') {
					echo '<div id="message" class="error"><p>'.__("<b>Duplicate Rules Found!</b> Check your User Role and Post Type for each rule columns, make sure they are not identical for each rule. Your previous settings was not saved","Apext").'</strong></p></div>';
					}
					elseif ($pagenow == 'options-general.php' && $_GET['page'] =='apext_setting' &&  $_GET['error'] == 'numeric') {
					echo '<div id="message" class="error"><p>'.__("<b>The Life Spane column Should Be Numbers and Not null!</b> Check your User Days columns for each rule. Your previous settings was not saved.","Apext").'</strong></p></div>';
					}
				}
			?>
			
			<form method="post" action="options.php" id="ruletableform"> 
			
			<?php settings_fields( 'apext_setting' ); ?>
			<table id="rule_table" class="widefat">
       
			<thead>
				<tr>
				<th><?php _e('User Role','Apext'); ?></th>
				<th><?php _e('Post Type','Apext'); ?></th>
				<th><?php _e('Action','Apext'); ?></th>
				<th><?php _e('Life Span (Days)','Apext'); ?></th>
				<th><?php _e('Edit Rule','Apext'); ?></th>
				</tr>
			</thead>
		 
			<tfoot>
				<tr>
				<th><?php _e('User Role','Apext'); ?></th>
				<th><?php _e('Post Type','Apext'); ?></th>
				<th><?php _e('Action','Apext'); ?></th>
				<th><?php _e('Life Span (Days)','Apext'); ?></th>
				<th><?php _e('Edit Rule','Apext'); ?></th>
				</tr>
			</tfoot>					
			<tbody class="tblbody"> <?php	 
				$options = get_option('apext');  
 
				$html = '<h3>'.__("Post Expiration Rules","Apext").'</h3>';
				$c =0;
			if(isset($options['rules'])){
				$html .= '<tr>';
				foreach($options['rules'] as $k => $v){
				$html .=  '<td><input type="hidden" id="counter" name="counter" value="'.$c.'"/>';//rules array counter
				//display user role
				$html .=  '<div class="show">'.$v['role'].'</div>';
				$html .=  '<div class="noshow">';
				$html .= '<select id="therole" name="apext[rules]['.$c.'][role]">';
				foreach($wp_roles->role_names as $role => $name ) {
				$selected = ($v['role']==strtolower($name)) ? 'selected="selected"' : '';
				$html .=  '<option value="'.strtolower($name).'" '.$selected.'>'.$name.'</option>';}
				$html .= '</select></div><br></td>';
				//display custom post type
				$html .=  '<td>';
				$html .=  '<div class="show">'.$v['cpt'].'</div>';
				$html .= '<div class="noshow"><select id="thecpt" name="apext[rules]['.$c.'][cpt]" >';
				foreach($post_types as $post_type ) {
				$selected = ($v['cpt']==$post_type) ? 'selected="selected"' : '';  
				$html .=  "<option value='$post_type' $selected>$post_type</option>";}
				$html .= '</select></div><br></td>';
				//display actions	
				$html .=  '<td>';
				$html .=  '<div class="show">'.$v['action'].'</div>';
				$html .= '<div class="noshow"><select id="theaction" name="apext[rules]['.$c.'][action]">';
				foreach($actions as $action) {
				$selected = ($v['action']==$action) ? 'selected="selected"' : '';
				$html .="<option value='$action' $selected>$action</option>";}
				$html .= '</select></div><br></td>';
				//display days before limit
				$html .=  '<td>';
				$html .=  '<div class="show">'.$v['day'].'</div>';
				$html .=  '<div class="noshow"><input type="text" id="theday" name="apext[rules]['.$c.'][day]" value="'.$v['day'].'"/></div>';
				$html .= '<br></td>';
				$html .= '<td><span class="edit_rule button-secondary">'.__("Edit","Apext").'</span> <span class="remove_rule button-secondary">'.__("Remove","Apext").'</span><br></td>';
				$html .= '</tr>';
				$c++;
				} ;
				echo '<input type="hidden" name="_wp_http_referer" value="'.admin_url('/options-general.php?page=apext_setting').'" />';
	
			} echo $html; ?> </tbody> </table>
    
				<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary form_submit rowedit" value="<?php _e("Save Changes","Apext") ;?>"  /><label style="margin-left:5px;font-style:italic"><?php _e("Click this after add/edit rules","Apext") ;?></label></p>	
			</form>
       
       
        <br> 
        <div>
        <h3><?php _e("Apply rules to previously published posts","Apext") ;?></h3>
       <input type="submit" name="submit" id="submit" class="button-primary meta_upadate" value="<?php _e("Apply","Apext") ;?>"  /><br><label> <?php _e("Click this <b>ONLY</b> if you want your new edited rules applied to your previously published posts. Otherwise only new posts will applied to your added/edited rules." , "Apext")?></label>
        
        </div>
        <br>
        <div class="divider"></div>
        <div id="apextmaildiv"></div>
       <?php 
				if(isset($_GET['error'])){
					if ($pagenow == 'options-general.php' && $_GET['page'] =='apext_setting' &&  $_GET['error'] == 'digit') {
					echo '<div id="message" class="error"><p><b>'.__("Only number allowed in the input of day of send notification before expiration!","Apext").'</b></p></div>';
					}
					elseif($pagenow == 'options-general.php' && $_GET['page'] =='apext_setting' &&  $_GET['error'] == 'digitnull'){
						echo '<div id="message" class="error"><p><b>'.__("The day of send notification cannot be empty when you enabled notification before post's expiry date!","Apext").'</b></p></div>';
					}
				}
			?>
       
       <div>
       <h3><?php _e("Auto Post Expiration Email Notification Control","Apext") ;?></h3>
       <p><?php _e("Indicate whether to send notifications before the post is expired, or on the day of expiration.","Apext") ;?></p>
		
       <form method="post" action="options.php" id="apextmail"> 
		<?php  settings_fields( 'apextmail' ); $this->mail_setting_cb(); ?>
		<br>
		<input type="hidden" name="_wp_http_referer" value="<?php admin_url('/options-general.php?page=apext_setting');?>" />
		<input type="submit" value="<?php _e("Save Notification Setting","Apext") ;?>" class="button-primary" />
	   </form>		
       </div>
      
     
