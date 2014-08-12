<?php

if ( !class_exists( 'Sohc_Data' ) ) :

class Sohc_Data
{

	function __construct() {
		
		if( is_admin() )
			add_action( 'plugins_loaded' , array( $this , 'init' ) , 20 );

	}

	function init() {
		
		global $Sohc;
		
		if( !$Sohc->Current['ajax'] ) {
			add_action( 'admin_init' , array( $this , 'dataUpdate' ) );
		}
	}

	function get_record( $record ) {
		
		global $Sohc;
		
		$Data = array();
		
		if( $Sohc->Current['multisite'] ) {
			
			$GetData = get_site_option( $record );

		} else {
			
			$GetData = get_option( $record );
			
		}

		if( !empty( $GetData ) )
			$Data = $GetData;
		
		return $Data;

	}

	function get_data_option_help() {
		
		global $Sohc;
		
		$Data = $this->get_record( $Sohc->Plugin['record']['option_help'] );
		
		return $Data;

	}

	function get_data_others() {
		
		global $Sohc;
		
		$Data = $this->get_record( $Sohc->Plugin['record']['other'] );
		
		return $Data;

	}

	function get_data_user( $user_role ) {
		
		global $Sohc;
		
		$Data = $this->get_data_option_help();
		if( !empty( $Data[$user_role] ) )
			$Data = $Data[$user_role];
		
		return $Data;

	}

	function get_donate_key( $record ) {
		
		global $Sohc;

		$donateKey = $this->get_record( $record );
		
		return $donateKey;

	}

	function get_donate_width() {
		
		global $Sohc;
		
		$width = false;
		$GetData = $this->get_record( $Sohc->ClassInfo->DonateOptionRecord );

		if( !empty( $GetData ) ) {
			$width = true;
		}

		return $width;

	}





	function dataUpdate() {
		
		global $Sohc;
		
		$RecordField = false;
		$can_capability = $Sohc->ClassManager->get_manager_user_role();
		
		if( !empty( $_POST ) && !empty( $_POST[$Sohc->Plugin['ltd'] . '_settings'] ) && $_POST[$Sohc->Plugin['ltd'] . '_settings'] == $Sohc->Plugin['UPFN'] && !empty( $_POST['record_field'] ) && current_user_can( $can_capability ) ) {

			$RecordField = strip_tags( $_POST['record_field'] );
			
			if( !empty( $_POST[$Sohc->Plugin['nonces']['field']] ) && check_admin_referer( $Sohc->Plugin['nonces']['value'] , $Sohc->Plugin['nonces']['field'] ) ) {
				
				if( !empty( $_POST['reset'] ) ) {
					
					$this->remove_record( $RecordField );
					
				} elseif( $RecordField == $Sohc->Plugin['record']['option_help'] ) {
					
					$this->update_option_help();
					
				} elseif( $RecordField == $Sohc->Plugin['record']['other'] ) {
					
					$this->update_other();
					
				} elseif( $RecordField == 'donate' ) {
					
					$this->update_donate();

				}
				
			} elseif( !empty( $_POST[$Sohc->Plugin['ltd'] . '_import'] ) && check_admin_referer( $Sohc->Plugin['ltd'] . '_import_value' , $Sohc->Plugin['ltd'] . '_import_field') ) {
				
				$this->update_import();
				
			}

		}

	}

	function update_format() {

		global $Sohc;

		$Update = array( 'UPFN' => 1 );

		return $Update;

	}

	function update_option_help() {
		
		global $Sohc;

		$Update = $this->update_format();
		$PostData = array();

		if( !empty( $_POST['data'] ) )
			$PostData = $_POST['data'];

		if( !empty( $PostData ) ) {

			foreach( $PostData as $user_role => $settings ) {
				
				$user_role = strip_tags( $user_role );
				
				foreach( $settings as $screen_id => $setting ) {
					
					$screen_id = strip_tags( $screen_id );

					if( !empty( $setting ) ) {
						
						foreach( $setting as $option ) {
							
							$option = strip_tags( $option );
							$Update[$user_role][$screen_id][$option] = 1;

						}
					}
					
				}
				
			}
			
			if( !empty( $Update ) ) {

				if( $Sohc->Current['multisite'] && $Sohc->Current['network_admin'] ) {
						
					update_site_option( $Sohc->Plugin['record']['option_help'] , $Update );
						
				} else {
						
					update_option( $Sohc->Plugin['record']['option_help'] , $Update );
						
				}
				
				wp_redirect( add_query_arg( $Sohc->Plugin['msg_notice'] , 'update' ) );
				exit;

			}
			
		}

	}

	function remove_record( $record ) {
		
		global $Sohc;
		
		if( $Sohc->Current['multisite'] && $Sohc->Current['network_admin'] ) {

			delete_site_option( $record );

		} else {

			delete_option( $record );

		}

		wp_redirect( add_query_arg( $Sohc->Plugin['msg_notice'] , 'delete' ) );
		exit;

	}
	
	function update_other() {
		
		global $Sohc;

		$Update = $this->update_format();
		$PostData = array();

		if( !empty( $_POST['data']['other'] ) )
			$OtherData = $_POST['data']['other'];

		if( !empty( $OtherData ) ) {

			if( !empty( $OtherData['capability'] ) )
				$Update['capability'] = strip_tags( $OtherData['capability'] );

			if( $Sohc->Current['multisite'] && $Sohc->Current['network_admin'] ) {
				
				update_site_option( $Sohc->Plugin['record']['other'] , $Update );
				
			} else {
				
				update_option( $Sohc->Plugin['record']['other'] , $Update );

			}

			wp_redirect( add_query_arg( $Sohc->Plugin['msg_notice'] , 'update' ) );
			exit;

		}
		
	}
	
	function update_import() {
		
		global $Sohc;
		
		$import_slug = $Sohc->Plugin['page_slug'] . '_import';
		$upload_field = $import_slug . '_file';

		if( !empty( $_POST[$import_slug] ) && !empty( $_FILES[$upload_field] ) ) {
			
			$file = $_FILES[$upload_field];
			if ( empty( $file['error'] ) or !empty( $file['tmp_name'] ) or strpos( $file['name'] , 'csv') ) {
					
				$ImportData = array();

				$import_file = file_get_contents( $file['tmp_name'] );
				$content_line = array();
				if( !empty( $import_file ) )
					$content_line = explode( "\n" , $import_file );

				if( !empty( $content_line ) ) {

					foreach( $content_line as $line ) {
							
						if( !empty( $line ) ) {
								
							$line_n = explode( ',' , $line );
								
							$user = strip_tags( $line_n[0] );
							$screen_id = strip_tags( $line_n[1] );
							$meta = strip_tags( $line_n[2] );
							$val = intval( $line_n[3] );
								
							$ImportData[$user][$screen_id][$meta] = $val;
	
						}
							
					}

				}
					
				if( !empty( $ImportData ) ) {
					
					if( $Sohc->Current['multisite'] && $Sohc->Current['network_admin'] ) {
						
						update_site_option( $Sohc->Plugin['record']['option_help'] , $ImportData );
						
					} else {
						
						update_option( $Sohc->Plugin['record']['option_help'] , $ImportData );
						
					}
					
				}

				wp_redirect( add_query_arg( $Sohc->Plugin['msg_notice'] , 'updated' ) );
				exit;

			}
			
		}
		
	}
	
	function update_donate() {
		
		global $Sohc;

		$is_donate_check = false;
		$submit_key = false;

		if( !empty( $_POST['donate_key'] ) ) {

			$is_donate_check = $Sohc->ClassInfo->is_donate_key_check( $_POST['donate_key'] );

			if( !empty( $is_donate_check ) ) {

				if( $Sohc->Current['multisite'] && $Sohc->Current['network_admin'] ) {
					
					update_site_option( $Sohc->ClassInfo->DonateRecord , $is_donate_check );
					
				} else {

					update_option( $Sohc->ClassInfo->DonateRecord , $is_donate_check );
					
				}
				wp_redirect( add_query_arg( $Sohc->Plugin['msg_notice'] , 'donated' ) );
				exit;

			}

		}
		
	}
	
	function update_donate_toggle( $Data ) {
		
		global $Sohc;

		if( $Sohc->Current['multisite'] && $Sohc->Current['ajax'] ) {

			update_site_option( $Sohc->ClassInfo->DonateOptionRecord , $Data );
			
		} else {

			update_option( $Sohc->ClassInfo->DonateOptionRecord , $Data );
			
		}

	}

}

endif;
