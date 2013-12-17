<?php

if (!class_exists('BON_Backup')) {

	ob_start();
	
	class BON_Backup {

		private $token;
		
		public function __construct () {
			
			$this->token = "bon_backup";
		}

		function render_page() {
			echo $this->option_page();
			echo $this->import();
			echo $this->export();
		}

		public function option_page() {

			$contextual_help =
			  '<h2>' . __( 'Backup Manager', 'bon' ) . '</h2>' .
			  '<div class="backup-info"><p>' . __( 'Here are a few notes on using this screen.', 'bon' ) . '</p><ul>' .
			  '<li>' . __( 'The backup manager allows you to backup or restore your "Theme Options" and other settings to or from a text file.', 'bon' ) . '</li>' .
			  '<li>' . __( 'To create a backup, simply select the setting type you\'d like to backup (or "All Settings") and hit the "Download Export File" button.', 'bon' ) . '</li>' .
			  '<li>' . __( 'To restore your settings from a backup, browse your computer for the file (under the "Import Settings" heading) and hit the "Upload File and Import" button. This will restore only the settings that have changed since the backup.', 'bon' ) . '</li>' .
			  
			  '<li><strong>' . __( 'Please note that only valid backup files generated through the Backup Manager should be imported.', 'bon' ) . '</strong></li>' .

			  '<li><strong>' . __( 'Looking for assistance?', 'bon' ) . '</strong></p>' .
			  '<li>' . sprintf( __( 'Please post your query on the %sBonfirelab Support Desk%s where we will do our best to assist you further.', 'bon' ), '<a href="http://support.bonfirelab.com/" target="_blank">', '</a>' ) . '</li></ul></div>';
				

			$output = '';
			
			$output .= $contextual_help;
			

			return $output;
		}
		

		/*	
		 *	Import feature
		 */
		public function import() {

			$status = false;
			if (isset($_FILES["import"]) && check_admin_referer("bon-backup-import")) {

				if ($_FILES["import"]["error"] > 0) 

					wp_die("Error happens");		

				else {


					$file_name = $_FILES["import"]["name"];

					$file_type = $_FILES["import"]["type"];

					$file_ext = strtolower(end(explode(".", $file_name)));

					$file_size = $_FILES["import"]["size"];

					if ( ($file_ext === "json") && ( $file_type === 'text/json' ) && ($file_size < 500000) ) {


						$url = wp_nonce_url('admin.php?page=bon_backup', 'bon-backup-import');

						$form_fields = array('import');
						$method = '';

						// Get file writing credentials
						if (false === ($creds = request_filesystem_credentials($url, $method, false, false, $form_fields) ) ) {
							return true;
						}
						
						if ( ! WP_Filesystem($creds) ) {
							// our credentials were no good, ask the user for them again
							request_filesystem_credentials($url, $method, true, false, $form_fields);
							return true;
						}
						
						// Write the file if credentials are good
						$upload_dir = wp_upload_dir();
						$filename = trailingslashit($upload_dir['path']).'bon_options.json';
							 
						// by this point, the $wp_filesystem global should be working, so let's use it to create a file
						global $wp_filesystem;
						
						if ( ! $wp_filesystem->move($_FILES['import']['tmp_name'], $filename, true) ) {
							echo 'Error saving file!';
							return;
						}
						
						$encode_options = $wp_filesystem->get_contents($filename);
											
						$options = json_decode($encode_options, true);

						if ($options !== FALSE){

							$optionsframework_settings = get_option( 'bon_optionsframework' );

							// Gets the unique option id
							if ( isset( $optionsframework_settings['id'] ) ) {

								$option_name = $optionsframework_settings['id'];

							}
							
							if( get_option( $option_name ) !== false ) {

								$values =  $this->_extract_values($options);
								
								if ( isset( $values ) ) {

									update_option( $option_name, $values ); // Add option with default settings

									$status = true;

									$wp_filesystem->delete($filename);

								}
							}
						}
					}	

					else {
						echo '<div class="error"><p>Invalid file or file size too big.</p></div>';
					}
						
				}

			}


			$output = '';


			$output .= '<div class="wrap">';
			$output .= '<div id="icon-tools" class="icon32"><br /></div>';
		    $output .= '<h2>Import</h2>';

			if($status == true) {
		    	$output .= '<div class="updated" style="display: block !important;"><p>All options are restored successfully.</p></div>';
		    }
		    
		    $output .= '<p>Click Browse button and choose a json file that you backup before.</p>';
		    
		    $output .= '<form method="post" enctype="multipart/form-data">';
			$output .= '<p class="submit">';
		    $output .=  wp_nonce_field("bon-backup-import", "_wpnonce", true, false);
		    $output .= '<input type="file" name="import" />';
			$output .= '<input type="submit" class="button" name="submit" value="Restore"/>';
			$output .= '</p>';
		    $output .= '</form>';
		    $output .= '</div>';

		    return $output;
		}

		/*	
		 *	Export feature
		 */
		public function export() {
			if (isset($_POST["export"])) { 
			  	check_admin_referer("bon-backup-export"); 
					
				$blogname = str_replace(" ", "", get_option("blogname"));
				$date = date("m-d-Y");
				$json_name = $blogname."-".$date; // Namming the filename will be generated.
				
				$optionsframework_settings = get_option('bon_optionsframework');

				$option_name = $optionsframework_settings['id'];

				$options = get_option($option_name);
				$json_file = json_encode($options); // Encode data into json data
				
				ob_clean();
				echo $json_file;
				header("Content-Type: text/json; charset=" . get_option( "blog_charset"));
				header("Content-Disposition: attachment; filename=$json_name.json");

				exit();
			}
			
			$output = '';
			$output .= '<div class="wrap">';
			$output .= '<div id="icon-tools" class="icon32"><br /></div>';
		    $output .= '<h2>Export</h2>';
		    $output .= '<p>When you click <tt>Backup all options</tt> button, system will generate a JSON file for you to save on your computer.</p>';
		    $output .= '<form method="post">';
	        $output .= '<p class="submit">';
	        $output .=  wp_nonce_field("bon-backup-export", "_wpnonce", true, false);
	        $output .= '<input type="submit" class="button" name="export" value="Backup all options" />';
	        $output .= '</p>';
		    $output .= '</form>';
		    $output .= '</div>';

		    return $output;
		}

		private function _extract_values($config) {

			$output = array();
			
			$optionsframework_settings = get_option('bon_optionsframework');

			$option_name = $optionsframework_settings['id'];

			$options = get_option($option_name);

			foreach ( $options as $key => $value ) {
				
				if( array_key_exists($key, $config) ) {

					$output[$key] = $config[$key];
				}
				else {
					$output[$key] = $value;
				}
				
			}

			return $output;
		}
		
	}

}