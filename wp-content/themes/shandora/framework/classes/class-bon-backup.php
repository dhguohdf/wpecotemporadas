<?php

if (!class_exists('BON_Backup')) {
	
	class BON_Backup {

		private $token;
		public $admin_page;
		
		public function __construct () {

			$this->admin_page = "";
			$this->token = "bon_backup";

		}

		/**
		 * init()
		 *
		 * Initialize the class.
		 *
		 * @since 1.0.0
		 */
		
		function init() {

			$of_settings = get_option( 'bon_framework_optionsframework' );
			$of_name = get_option( $of_settings['id'] );
			if ( is_admin() && ( $of_name['bon_framework_backupmenu_disable'] === false ) ) {
				// Register the admin screen.
				add_action( 'admin_menu', array( &$this, 'register_admin_screen' ), 20 );
			}
		} // End init()
		
		/**
		 * register_admin_screen()
		 *
		 * Register the admin screen within WordPress.
		 *
		 * @since 1.0.0
		 */
		
		function register_admin_screen () {
			
			$func = "add_submenu_page";
			$this->admin_page = $func('bon_options', __( 'Import / Export', 'bon' ), __( 'Import / Export', 'bon' ), 'manage_options', $this->token, array( &$this, 'admin_screen' ) );

			// Admin screen logic.
			add_action( 'load-' . $this->admin_page, array( &$this, 'admin_screen_logic' ) );
			
			// Add contextual help.
			add_action( 'contextual_help', array( &$this, 'admin_screen_help' ), 10, 3 );
					
			add_action( 'admin_notices', array( &$this, 'admin_notices' ), 10 );
		
		} // End register_admin_screen()

		public function admin_screen() {

			$export_type = 'all';
		
			if ( isset( $_POST['export-type'] ) ) {
				$export_type = esc_attr( $_POST['export-type'] );
			}
	?>
		<div class="wrap">
			<?php echo get_screen_icon( $screen = 'tools' ); ?>	
			<h2><?php _e( 'Import / Export', 'bon' ); ?></h2>
			
			<div class="import">
				<h3><?php _e( 'Import Settings', 'bon' ); ?></h3>
				<p><?php _e( 'If you have Theme settings in a backup file on your computer, the Import / Export system can import those into this site. To get started, upload your backup file to import from below.', 'bon' ); ?></p>

					<form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin.php?page=' . $this->token ); ?>">
						<?php wp_nonce_field( 'bon-backup-import' ); ?>
						<label for="bon-import-file"><?php printf( __( 'Upload File: (Maximum Size: %s)', 'bon' ), ini_get( 'post_max_size' ) ); ?></label>
						<input type="file" id="bon-import-file" name="bon-import-file" size="25" />
						<input type="hidden" name="bon-backup-import" value="1" />
						<input type="submit" class="button" value="<?php _e( 'Upload File and Import', 'bon' ); ?>" />
					</form>
			</div>

			<br />

			<div class="export">
				<h3><?php _e( 'Export Settings', 'bon' ); ?></h3>
				
				<p><?php _e( 'When you click the button below, the Import / Export system will create a text file for you to save to your computer.', 'bon' ); ?></p>
				<p><?php echo sprintf( __( 'This text file can be used to restore your settings here on "%s", or to easily setup another website with the same settings".', 'bon' ), get_bloginfo( 'name' ) ); ?></p>
					
				<form method="post" action="<?php echo admin_url( 'admin.php?page=' . $this->token ); ?>">
					<?php wp_nonce_field( 'bon-backup-export' ); ?>
					<input type="hidden" name="bon-backup-export" value="1" />
					<input type="submit" class="button" value="<?php _e( 'Download Export File', 'bon' ); ?>" />
				</form>
			</div>
			
		</div><!--/.wrap-->
			<?php
		}
				
		/**
		 * admin_screen_help()
		 *
		 * Add contextual help to the admin screen.
		 *
		 * @since 1.0.0
		 */
		
		function admin_screen_help ( $contextual_help, $screen_id, $screen ) {
					
			if ( $this->admin_page == $screen->id ) {

				$contextual_help =
				  '<h2>' . __( 'Import / Export', 'bon' ) . '</h2>' .
				  '<div class="backup-info"><p>' . __( 'Here are a few notes on using this screen.', 'bon' ) . '</p><ul>' .
				  '<li>' . __( 'The backup manager allows you to backup or restore your "Theme Options" and other settings to or from a text file.', 'bon' ) . '</li>' .
				  '<li>' . __( 'To create a backup, simply select the setting type you\'d like to backup (or "All Settings") and hit the "Download Export File" button.', 'bon' ) . '</li>' .
				  '<li>' . __( 'To restore your settings from a backup, browse your computer for the file (under the "Import Settings" heading) and hit the "Upload File and Import" button. This will restore only the settings that have changed since the backup.', 'bon' ) . '</li>' .
				  
				  '<li><strong>' . __( 'Please note that only valid backup files generated through the Backup Manager should be imported.', 'bon' ) . '</strong></li>' .

				  '<li><strong>' . __( 'Looking for assistance?', 'bon' ) . '</strong></p>' .
				  '<li>' . sprintf( __( 'Please post your query on the %sBonfirelab Support Desk%s where we will do our best to assist you further.', 'bon' ), '<a href="http://support.bonfirelab.com/" target="_blank">', '</a>' ) . '</li></ul></div>';
					
			} // End IF Statement
			
			return $contextual_help;
		
		} // End admin_screen_help()
		

		/**
		 * admin_notices()
		 *
		 * Display admin notices when performing backup/restore.
		 *
		 * @since 1.0.0
		 */
		
		function admin_notices() {
		
			if ( ! isset( $_GET['page'] ) || ( $_GET['page'] != $this->token ) ) { return; }
		
			echo '<div id="import-notice" class="updated"><p>' . sprintf( __( 'Please note that this backup manager backs up only your settings and not your content. To backup your content, please use the %sWordPress Export Tool%s.', 'bon' ), '<a href="' . admin_url( 'export.php' ) . '">', '</a>' ) . '</p></div><!--/#import-notice .message-->' . "\n";
				
			if ( isset( $_GET['error'] ) && $_GET['error'] == 'true' ) {
				echo '<div id="message" class="error"><p>' . __( 'There was a problem importing your settings. Please Try again.', 'bon' ) . '</p></div>';
			} else if ( isset( $_GET['error-export'] ) && $_GET['error-export'] == 'true' ) {  
				echo '<div id="message" class="error"><p>' . __( 'There was a problem exporting your settings. Please Try again.', 'bon' ) . '</p></div>';
			} else if ( isset( $_GET['invalid'] ) && $_GET['invalid'] == 'true' ) {  
				echo '<div id="message" class="error"><p>' . __( 'The import file you\'ve provided is invalid. Please try again.', 'bon' ) . '</p></div>';
			} else if ( isset( $_GET['imported'] ) && $_GET['imported'] == 'true' ) {  
				echo '<div id="message" class="updated"><p>' . sprintf( __( 'Settings successfully imported. | Return to %sTheme Options%s', 'bon' ), '<a href="' . admin_url( 'admin.php?page=bon_options' ) . '">', '</a>' ) . '</p></div>';
			} // End IF Statement
			
		} // End admin_notices()

		/**
		 * import()
		 *
		 * Import settings from a backup file.
		 *
		 * @since 1.0.0
		 */

		public function admin_screen_logic() {
			if ( ! isset( $_POST['bon-backup-export'] ) && isset( $_POST['bon-backup-import'] ) && ( $_POST['bon-backup-import'] == true ) ) {
				$this->import();
			}
			
			if ( ! isset( $_POST['bon-backup-import'] ) && isset( $_POST['bon-backup-export'] ) && ( $_POST['bon-backup-export'] == true ) ) {
				$this->export();
			}
		}

		function import() {

			check_admin_referer( 'bon-backup-import' ); // Security check.
			
			if ( ! isset( $_FILES['bon-import-file'] ) ) { return; } // We can't import the settings without a settings file.
			
			$url = wp_nonce_url('admin.php?page='. $this->token , 'bon-backup-import');

			$form_fields = array('bon-import-file');
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
			
			if ( ! $wp_filesystem->move($_FILES['bon-import-file']['tmp_name'], $filename, true) ) {
				echo 'Error saving file!';
				return;
			}
			
			$encode_options = $wp_filesystem->get_contents($filename);
								
			$datafile = json_decode($encode_options, true);
			
			// Check for errors
			if ( ! $datafile || $_FILES['bon-import-file']['error'] ) {
				wp_redirect( admin_url( 'admin.php?page=' . $this->token . '&error=true' ) );
				exit;
			}
			
			// Make sure this is a valid backup file.
			if ( ! isset( $datafile['bon-backup-validator'] ) ) {
				wp_redirect( admin_url( 'admin.php?page=' . $this->token . '&invalid=true' ) );
				exit;
			} else {
				unset( $datafile['bon-backup-validator'] ); // Now that we've checked it, we don't need the field anymore.
			}


			if ($datafile !== false ) {

				// Get the theme name from the database.
				$optionsframework_data = get_option('bon_optionsframework');

				// Gets the unique option id
				if ( isset( $optionsframework_data['id'] ) ) {

					$optionsframework_name = $optionsframework_data['id'];
				}
				
				if( get_option( $optionsframework_name ) !== false ) {

					$values =  $this->_extract_values($datafile);
					
					if ( isset( $values ) ) {

						update_option( $optionsframework_name, $values ); // Add option with default settings
						$wp_filesystem->delete($filename);
						wp_redirect( admin_url( 'admin.php?page=' . $this->token . '&imported=true' ) );
						exit;
						

					} else {
						wp_redirect( admin_url( 'admin.php?page=' . $this->token . '&error=true' ) );
						exit;
					}
				}
			}
			
		} // End import()

		
		/**
		 * export()
		 *
		 * Export settings to a backup file.
		 *
		 * @since 1.0.0
		 * @uses global $wpdb
		 */
		 
		function export() {
			global $wpdb;
			check_admin_referer( 'bon-backup-export' ); // Security check.
			
			$optionsframework_settings = get_option('bon_optionsframework');
			$database_options = get_option( $optionsframework_settings['id'] );

			$date = date("Y-m-d");
			$blogname = strtolower(str_replace(" ", "", get_option("blogname")));
			$filename = $json_name = $blogname.".themeoptions.".$date;
			// Error trapping for the export.
			if ( $database_options == '' ) {
				wp_redirect( admin_url( 'admin.php?page=' . $this->token . '&error-export=true' ) );
				return;
			}
			
			if ( ! $database_options ) { return; }
		
			// Add our custom marker, to ensure only valid files are imported successfully.
			$database_options['bon-backup-validator'] = date( 'Y-m-d h:i:s' );
		
			// Generate the export file.
		    $output = json_encode( (array)$database_options );
		
		    header( 'Content-Description: File Transfer' );
		    header( 'Cache-Control: public, must-revalidate' );
		    header( 'Pragma: hack' );
		    header( 'Content-Type: text/plain' );
		    header( 'Content-Disposition: attachment; filename="' . $filename . '.json"' );
		    header( 'Content-Length: ' . strlen( $output ) );
		    echo $output;
		    exit;
				
		} // End export()

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



	/**
	 * Create $woo_backup Object.
	 *
	 * @since 1.0.0
	 * @uses OptionsFramework_Backup
	 */

	$bon_backup = new BON_Backup();
	$bon_backup->init();
}
?>