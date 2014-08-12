<?php

if ( !class_exists( 'Sohc_Manager' ) ) :

class Sohc_Manager
{

	var $is_manager = false;
	
	function __construct() {
		
		if( is_admin() )
			add_action( 'plugins_loaded' , array( $this , 'set_manager' ) , 20 );

	}

	function get_manager_user_role() {

		global $Sohc;

		$cap = false;

		if( is_multisite() ) {

			$cap = $Sohc->Plugin['default_role']['network'];

		} else {

			$cap = $Sohc->Plugin['default_role']['child'];

		}
		
		$other_data = $Sohc->ClassData->get_data_others();
		if( !empty( $other_data['capability'] ) )
			$cap = strip_tags( $other_data['capability'] );
			
		return $cap;

	}

	function set_manager() {
		
		$cap = $this->get_manager_user_role();
		if( current_user_can( $cap ) )
			$this->is_manager = true;
		
	}

	function init() {
		
		global $Sohc;

		if( is_admin() && $this->is_manager && !$Sohc->Current['ajax'] ) {
			
			$base_plugin = trailingslashit( $Sohc->Plugin['plugin_slug'] ) . $Sohc->Plugin['plugin_slug'] . '.php';
			
			if( $Sohc->Current['multisite'] ) {
				
				add_filter( 'network_admin_plugin_action_links_' . $base_plugin , array( $this , 'plugin_action_links' ) );
				add_action( 'network_admin_menu' , array( $this , 'admin_menu' ) );
				add_action( 'network_admin_notices' , array( $this , 'update_notice' ) );
				add_action( 'load-toplevel_page_' . $Sohc->Plugin['page_slug'] , array( $this , 'export' ) );

			} else {

				add_filter( 'plugin_action_links_' . $base_plugin , array( $this , 'plugin_action_links' ) );
				add_action( 'admin_menu' , array( $this , 'admin_menu' ) );
				add_action( 'admin_notices' , array( $this , 'update_notice' ) );
				add_action( 'load-settings_page_' . $Sohc->Plugin['page_slug'] , array( $this , 'export' ) );

			}
			
			add_action( 'admin_print_scripts' , array( $this , 'admin_print_scripts' ) );
			
		}
		
	}

	function plugin_action_links( $links ) {

		global $Sohc;
		
		$link_setting = sprintf( '<a href="%1$s">%2$s</a>' , $Sohc->ClassInfo->links['setting'] , __( 'Settings' ) );
		$link_support = sprintf( '<a href="%1$s" target="_blank">%2$s</a>' , $Sohc->ClassInfo->links['forum'] , __( 'Support Forums' ) );

		array_unshift( $links , $link_setting, $link_support );

		return $links;

	}

	function admin_menu() {
		
		global $Sohc;

		$cap = $this->get_manager_user_role();
		$main_slug = $Sohc->Plugin['page_slug'];

		if( $Sohc->Current['multisite'] ) {

			add_menu_page( $Sohc->Plugin['name'] , $Sohc->Plugin['name'] , $cap , $main_slug , array( $this , 'views') );
			
		} else {
			
			add_options_page( $Sohc->Plugin['name'] , $Sohc->Plugin['name'] , $cap , $main_slug , array( $this , 'views') );

		}

	}

	function is_settings_page() {
		
		global $plugin_page;
		global $Sohc;
		
		$is_settings_page = false;
		
		$setting_pages = array( $Sohc->Plugin['page_slug'] );
		
		if( in_array( $plugin_page , $setting_pages ) )
			$is_settings_page = true;
			
		return $is_settings_page;
		
	}

	function admin_print_scripts() {
		
		global $plugin_page;
		global $wp_version;
		global $Sohc;
		
		if( $this->is_settings_page() ) {
			
			$ReadedJs = array( 'jquery' );
			wp_enqueue_script( $Sohc->Plugin['page_slug'] ,  $Sohc->Plugin['url'] . $Sohc->Plugin['ltd'] . '.js', $ReadedJs , $Sohc->Plugin['ver'] );
			
			wp_enqueue_style( $Sohc->Plugin['page_slug'] , $Sohc->Plugin['url'] . $Sohc->Plugin['ltd'] . '.css', array() , $Sohc->Plugin['ver'] );
			if( version_compare( $wp_version , '3.8' , '<' ) )
				wp_enqueue_style( $Sohc->Plugin['page_slug'] . '-37' , $Sohc->Plugin['url'] . $Sohc->Plugin['ltd'] . '-3.7.css', array() , $Sohc->Plugin['ver'] );

		}
		
	}

	function views() {

		global $Sohc;
		global $plugin_page;

		if( $this->is_settings_page() ) {
			
			$manage_page_path = $Sohc->Plugin['dir'] . trailingslashit( 'inc' );
			
			if( $plugin_page == $Sohc->Plugin['page_slug'] ) {
				
				if( !empty( $_GET['page_tab'] ) && $_GET['page_tab'] == 'other' ) {
					
					include_once $manage_page_path . 'setting_other.php';
					
				} else {

					include_once $manage_page_path . 'setting.php';

				}
				
			}
			
			add_filter( 'admin_footer_text' , array( $Sohc->ClassInfo , 'admin_footer_text' ) );

		}
		
	}
	
	function get_action_link() {
		
		global $Sohc;
		
		$url = remove_query_arg( array( $Sohc->Plugin['msg_notice'] ) );
		
		return $url;

	}
	
	function update_notice() {
		
		global $Sohc;

		if( $this->is_settings_page() ) {
			
			if( !empty( $_GET ) && !empty( $_GET[$Sohc->Plugin['msg_notice']] ) ) {
				
				$update_nag = $_GET[$Sohc->Plugin['msg_notice']];
				
				if( $update_nag == 'update' or $update_nag == 'delete' ) {

					printf( '<div class="updated"><p><strong>%s</strong></p></div>' , __( 'Settings saved.' ) );

				} elseif( $update_nag == 'donated' ) {
					
					printf( '<div class="updated"><p><strong>%s</strong></p></div>' , __( 'Thank you for your donation.' , $Sohc->Plugin['ltd'] ) );
					
				}
				
			}
			
		}
		
	}
	
	function export() {
		
		global $Sohc;
		
		$export_slug = $Sohc->Plugin['page_slug'] . '_export';

		if( $this->is_manager && $this->is_settings_page() && !empty( $_GET[$export_slug] ) ) {
			
			$Data = $Sohc->ClassData->get_data_option_help();
			
			$filename = $Sohc->Plugin['page_slug'] . '.csv';
				
			if( $Sohc->Current['multisite'] ) {

				$charset = get_site_option( 'blog_charset' );

			} else {

				$charset = get_option( 'blog_charset' );

			}
			
			if( !empty( $Data['UPFN'] ) )
				unset( $Data['UPFN'] );

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/xml; charset=' . $charset , true );

			if( !empty( $Data ) && check_admin_referer( $Sohc->Plugin['ltd'] . '_export_value' , $Sohc->Plugin['ltd'] . '_export_field') ) {
				
				$Content = '';
				foreach( $Data as $user => $screen ) {
					foreach( $screen as $screen_id => $type ) {
						if( !empty( $type ) ) {
							foreach( $type as $meta => $val ) {
								if( !empty( $val ) ) {
	
									$Content .= $user . ',';
									$Content .= $screen_id . ',';
									$Content .= $meta . ',';
									$Content .= $val . "\n";
	
								}
							}
						}
					}
					
				}

				if( !empty( $Content ) ) {
					echo $Content;
				}

			}

			die();
			
		}

	}
	
}

endif;
