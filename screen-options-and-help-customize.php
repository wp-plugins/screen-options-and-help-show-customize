<?php
/*
Plugin Name: Screen Options and Help Show Customize
Description: Screen options and help to show customize.
Plugin URI:http://wordpress.org/extend/plugins/screen-options-and-help-show-customize/
Version: 1.2.4.2
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=list&utm_content=sohc&utm_campaign=1_2_4_2
Text Domain: sohc
Domain Path: /languages
*/

/*  Copyright 2012 gqevu6bsiz (email : gqevu6bsiz@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/





class Sohc
{

	var $Ver,
		$Name,
		$Dir,
		$Slug,
		$RecordName,
		$ltd,
		$ltd_p,
		$Nonces,
		$UPFN,
		$Msg;


	function __construct() {
		$this->Ver = '1.2.4.2';
		$this->Name = 'Screen Options and Help Show Customize';
		$this->Dir = WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) . '/';
		$this->Slug = 'screen_option_and_help_show_customize';
		$this->RecordName = 'sohc_options';
		$this->ltd = 'sohc';
		$this->ltd_p = $this->ltd . '_plugin';
		$this->Nonces = array( "value" => $this->ltd . '_value' , "field" => $this->ltd . '_field' );
		$this->DonateKey = 'd77aec9bc89d445fd54b4c988d090f03';
		$this->UPFN = 'Y';

		$this->PluginSetup();
		$this->FilterStart();
		add_action( 'load-settings_page_' . $this->Slug , array( $this , 'export' ) );
		add_action( 'load-settings_page_' . $this->Slug , array( $this , 'import' ) );
	}

	// PluginSetup
	function PluginSetup() {
		// load text domain
		load_plugin_textdomain( $this->ltd , false , basename( dirname( __FILE__ ) ) . '/languages' );
		load_plugin_textdomain( $this->ltd_p , false , basename( dirname( __FILE__ ) ) . '/languages' );

		// plugin links
		add_filter( 'plugin_action_links' , array( $this , 'plugin_action_links' ) , 10 , 2 );
		add_filter( 'network_admin_plugin_action_links' , array( $this , 'network_admin_plugin_action_links' ) , 10 , 2 );

		// add menu
		if( defined( 'WP_ALLOW_MULTISITE' ) ) {
			$Data = get_site_option( $this->RecordName );
			if( empty( $Data ) ) {
				add_action( 'admin_menu' , array( $this , 'admin_menu' ) );
			}
			add_action( 'network_admin_menu' , array( $this , 'admin_menu' ) );
		} else {
			add_action( 'admin_menu' , array( $this , 'admin_menu' ) );
		}
	}

	// PluginSetup
	function plugin_action_links( $links , $file ) {
		if( plugin_basename(__FILE__) == $file ) {
			$support_link = '<a href="http://wordpress.org/support/plugin/screen-options-and-help-show-customize" target="_blank">' . __( 'Support Forums' ) . '</a>';
			$setting_link = '<a href="' . self_admin_url( 'options-general.php?page=' . $this->Slug ) . '">' . __('Settings') . '</a>';

			array_unshift( $links, $setting_link , $support_link );
		}
		return $links;
	}

	// PluginSetup
	function network_admin_plugin_action_links( $links , $file ) {
		if( plugin_basename(__FILE__) == $file ) {
			$support_link = '<a href="http://wordpress.org/support/plugin/screen-options-and-help-show-customize" target="_blank">' . __( 'Support Forums' ) . '</a>';
			$setting_link = '<a href="' . self_admin_url( 'settings.php?page=' . $this->Slug ) . '">' . __('Settings') . '</a>';

			array_unshift( $links, $setting_link , $support_link );
		}
		return $links;
	}

	// PluginSetup
	function admin_menu() {
		add_options_page(  __( 'Screen Options and Help Show Customize' , $this->ltd ) , __( 'Screen Options Customize' , $this->ltd ) , 'administrator', $this->Slug , array( $this , 'settings'));
		if( defined( 'WP_ALLOW_MULTISITE' ) ) {
			add_submenu_page( 'settings.php' , __( 'Screen Options and Help Show Customize' , $this->ltd ) , __( 'Screen Options Customize' , $this->ltd ) , 'manage_network' , $this->Slug, array( $this , 'settings_multi') );
		}
	}


	// SettingPage
	function settings() {

		// footer text
		add_filter( 'admin_footer_text' , array( $this , 'admin_footer_text' ) );

		// translation
		$mofile = $this->TransFileCk();
		if( $mofile == false && empty( $this->Msg ) ) {
			$this->Msg = '<div class="updated" style="background-color: rgba(255,204,190,1.0); border-color: rgba(160,0,0,1.0);"><p><strong>Please translate to your language.</strong> &gt; <a href="http://gqevu6bsiz.chicappa.jp/please-translation/?utm_source=use_plugin&utm_medium=translation&utm_content=sohc&utm_campaign=1_2_2" target="_blank">To translate</a></p></div>';
		}

		if( !empty( $_POST["donate_key"] ) ) {
			$SubmitKey = md5( strip_tags( $_POST["donate_key"] ) );
			if( $this->DonateKey == $SubmitKey ) {
				update_option( $this->ltd . '_donated' , $SubmitKey );
				$this->Msg .= '<div class="updated"><p><strong>' . __( 'Thank you for your donation.' , $this->ltd_p ) . '</strong></p></div>';
			}
		} elseif( !empty( $_POST["reset"] ) ) {
			$this->update_reset();
		} elseif( !empty( $_POST[$this->UPFN] ) ) {
			$this->update();
		}

		include_once 'inc/settings.php';
	}


	// SettingPage
	function settings_multi() {

		// footer text
		add_filter( 'admin_footer_text' , array( $this , 'admin_footer_text' ) );

		// translation
		$mofile = $this->TransFileCk();
		if( $mofile == false && empty( $this->Msg )  ) {
			$this->Msg = '<div class="updated" style="background-color: rgba(255,204,190,1.0); border-color: rgba(160,0,0,1.0);"><p><strong>Please translate to your language.</strong> &gt; <a href="http://gqevu6bsiz.chicappa.jp/please-translation/?utm_source=use_plugin&utm_medium=translation&utm_content=sohc&utm_campaign=1_2_2" target="_blank">To translate</a></p></div>';
		}

		if( !empty( $_POST["reset"] ) ) {
			$this->update_reset_multi();
		} elseif( !empty( $_POST[$this->UPFN] ) ) {
			$this->update_multi();
		}

		include_once 'inc/settings.php';
	}



	// Layout
	function admin_footer_text( $text ) {
		
		$text = '<img src="http://www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=18" width="18" /> Plugin developer : <a href="http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=footer&utm_content=' . $this->ltd . '&utm_campaign=' . str_replace( '.' , '_' , $this->Ver ) . '" target="_blank">gqevu6bsiz</a>';

		return $text;
	}

	// Translation File Check
	function TransFileCk() {
		$file = false;
		$moFile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $this->ltd . '-' . get_locale() . '.mo';
		if( file_exists( $moFile ) ) {
			$file = true;
		}
		return $file;
	}




	// Data get
	function get_data() {
		$NewData = array();
		
		if( is_network_admin() ) {
			$Data = get_site_option( $this->RecordName );
		} else {
			$Data = get_option( $this->RecordName );
		}
		if( !empty( $Data ) ) {
			$NewData = $Data;
		}
		
		return $NewData;
	}

	// Setting Item
	function get_users() {
		$UserRole = array();
		$editable_roles = get_editable_roles();
		foreach ( $editable_roles as $role => $details ) {
			$UserRole[$role] = translate_user_role( $details['name'] );
		}
		
		return $UserRole;
	}

	// Setting Item
	function get_lists( $type , $screenid , $Data , $tab_help , $tab_so ) {
		$Contents = '';

		$Closed = 'closed';
		if( !empty( $Data ) ) {
			foreach($Data as $Role => $val) {
				if( !empty( $val[$screenid] ) ) {
					$Closed = '';
					break;
				}
			}
		}
		$Contents .= '<div class="postbox ' . $Closed . '">';
		$Contents .= '<div class="handlediv" title="Click to toggle"><br></div>';
		$Contents .= '<h3 class="hndle"><span>' . __( $type ). '</span></h3>';
		$Contents .= '<div class="inside">';
		$Contents .= '<table cellspacing="0" class="wp-list-table widefat fixed">';
			$Contents .= '<thead>';
				$Contents .= '<tr>';
					$Contents .= '<th>';
						$Contents .= __( 'User Roles' );
					$Contents .= '</th>';
					
					if( !empty( $tab_so ) ) {
						$Contents .= '<th>';
							$Contents .= __( 'Screen Options' );
						$Contents .= '</th>';
					}
					if( !empty( $tab_help ) ) {
						$Contents .= '<th>';
							$Contents .= __( 'Help' );
						$Contents .= '</th>';
					}

				$Contents .= '</tr>';
			$Contents .= '</thead>';
			$Contents .= '<tbody>';
			
			foreach($this->get_users() as $name => $name_d) {
				$Contents .= '<tr>';
					$Contents .= '<td>';
						$Contents .= __( $name_d );
					$Contents .= '</td>';
					if( !empty( $tab_so ) ) {
						$Contents .= '<td>';
							$Checked = '';
							if( !empty( $Data[$name][$screenid]["screenoptions"] ) ) {
								$Checked = 'checked="checked"';
							}
							$Contents .= '<label><input type="checkbox" name="data[' . $name . '][' . $screenid . '][]" value="screenoptions" ' . $Checked . '> ' . __( 'Hide' ) . '</labe>';
						$Contents .= '</td>';
					}
					if( !empty( $tab_help ) ) {
						$Contents .= '<td>';
							$Checked = '';
							if( !empty( $Data[$name][$screenid]["help"] ) ) {
								$Checked = 'checked="checked"';
							}
							$Contents .= '<label><input type="checkbox" name="data[' . $name . '][' . $screenid . '][]" value="help" ' . $Checked . '> ' . __( 'Hide' ) . '</labe>';
						$Contents .= '</td>';
					}
				$Contents .= '</tr>';
			}

			$Contents .= '</tbody>';

			// all check
			$Contents .= '<tfoot>';
				$Contents .= '<tr>';
					$Contents .= '<td>&nbsp;</td>';
					if( !empty( $tab_so ) ) {
						$Contents .= '<td><label><input type="checkbox" name="all_checked" title="' . $screenid . '" class="screenoptions" /> ' . __( 'All checked' , $this->ltd ) . '</label></td>';
					}
					if( !empty( $tab_help ) ) {
						$Contents .= '<td><label><input type="checkbox" name="all_checked" title="' . $screenid . '" class="help" /> ' . __( 'All checked' , $this->ltd ) . '</label></td>';
					}
					
					
				$Contents .= '</tr>';
			$Contents .= '</tfoot>';

		$Contents .= '</table>';
		$Contents .= '</div>';
		$Contents .= '</div>';
		
		return $Contents;
	}

	// Setting Item
	function get_custom_posts() {
		$CustomPosts = array();
		
		$Data = get_site_option( $this->RecordName );

		if( is_network_admin() ) {

			global $wpdb;
			
			$query = "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}'";
			$Blogs = $wpdb->get_results( $query, ARRAY_A );
			
			foreach( $Blogs as $key => $blog ) {

				switch_to_blog( $blog["blog_id"] );
				global $wpdb;
				$And = "AND post_type !=  'page' AND post_type !=  'revision' AND post_type !=  'attachment' AND post_type !=  'nav_menu_item'";
				$row = $wpdb->get_col( "SELECT DISTINCT post_type FROM $wpdb->posts WHERE post_type != 'post' " . $And );
				
				if( !empty( $row ) ) {
					foreach( $row as $custom_post_name ) {
						$CustomPosts[$custom_post_name] = array( "edit" => $custom_post_name , "add" => "Add New " . $custom_post_name );
					}
				}

			}
			
			restore_current_blog();

		} else {

			$args = array( 'public' => true , '_builtin' => false , 'show_ui' => true );
			$cpt = get_post_types( $args , 'objects' );
			
			if( !empty( $cpt ) ) {
				foreach( $cpt as $custom_post_name => $cp ) {
					$CustomPosts[$custom_post_name] = array( "edit" => $cp->labels->name , "add" => $cp->labels->add_new_item );
				}
			}

		}
		
		return $CustomPosts;
	}





	// Update Setting
	function update() {
		$UPFN = strip_tags( $_POST[$this->UPFN] );
		if( $UPFN == 'Y' && check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {
			unset( $_POST[$this->UPFN] );

			$Update = array();
			if(!empty( $_POST["data"] )) {
				foreach ($_POST["data"] as $rolename => $val) {
					foreach($val as $tab => $options) {
						if( !empty( $options ) ) {
							foreach( $options as $option) {
								$Update[strip_tags( $rolename )][strip_tags( $tab )][$option] = 1;
							}
						}
					}
				}
			}
			if(!empty( $Update )) {
				$Record = $this->RecordName;
				update_option( $Record , $Update );
				$this->Msg = '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
			}
		}
	}

	// Update Setting
	function update_multi() {
		$UPFN = strip_tags( $_POST[$this->UPFN] );
		if( $UPFN == 'Y' && check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {
			unset( $_POST[$this->UPFN] );

			$Update = array();
			if(!empty( $_POST["data"] )) {
				foreach ($_POST["data"] as $rolename => $val) {
					foreach($val as $tab => $options) {
						if( !empty( $options ) ) {
							foreach( $options as $option) {
								$Update[strip_tags( $rolename )][strip_tags( $tab )][$option] = 1;
							}
						}
					}
				}
			}
			if(!empty( $Update )) {
				$Record = $this->RecordName;
				update_site_option( $Record , $Update );
				$this->Msg = '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
			}
		}
	}

	// Update Reset
	function update_reset() {
		if( check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {
			$Record = $this->RecordName;
			delete_option( $Record );
			$this->Msg = '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
		}
	}

	// Update Reset
	function update_reset_multi() {
		if( check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {
			$Record = $this->RecordName;
			delete_site_option( $Record );
			$this->Msg = '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
		}
	}


	// Data Export
	function export() {
		$Data = $this->get_data();
		if( !empty( $Data ) && !empty( $_GET["download"] ) && check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {

			$filename = $this->Slug . '.csv';
			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

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
			
			die();

		}
	}

	// Data Import
	function import() {

		if( !empty( $_POST["upload"] ) && !empty( $_FILES["import"] ) && check_admin_referer( $this->Nonces["value"] , $this->Nonces["field"] ) ) {

			$file = $_FILES["import"];
			if ( !empty( $file['error'] ) or empty( $file['tmp_name'] ) or !strpos( $file['name'] , 'csv') ) {
				$this->Msg = '<div class="error"><p><strong>' . __('Sorry, there has been an error.') . '</strong></p>';
				$this->Msg .= esc_html( $file['error'] ) . '</div>';
				return false;
			}
			
			$f = fopen( $file["tmp_name"] , 'r' );
			$content = fread( $f , filesize( $file["tmp_name"] ) );

			$content_n = explode( "\n" , $content );

			$Update = array();
			foreach( $content_n as $line ) {
				if( !empty( $line ) ) {
					$line_n = explode( ',' , $line );
					
					$user = strip_tags( $line_n[0] );
					$screen_id = strip_tags( $line_n[1] );
					$meta = strip_tags( $line_n[2] );
					$val = strip_tags( $line_n[3] );

					$Update[$user][$screen_id][$meta] = $val;
				}
			}

			if( !empty( $Update ) ) {
				$Record = $this->RecordName;
				if( is_network_admin() ) {
					update_site_option( $Record , $Update );
				} else {
					update_option( $Record , $Update );
				}
				$this->Msg = '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
			}

		}
		
	}



	// FilterStart
	function FilterStart() {
		add_action( 'admin_head' , array( $this , 'ScreenMeta' ) );
	}

	// FilterStart
	function ScreenMeta() {

		$Data = array();

		if( defined( 'WP_ALLOW_MULTISITE' ) ) {
			$Data = get_site_option( $this->RecordName );
		}
		
		if( empty( $Data ) ) {
			$Data = get_option( $this->RecordName );
		}

		$Userinfo = wp_get_current_user();
		$Userrole = $Userinfo->roles[0];
		
		$screen = get_current_screen();

		if( !empty( $Data[$Userrole] ) ) {
			$screen = get_current_screen();
			$UserSetData = $Data[$Userrole];
			if( !empty( $UserSetData[$screen->id] ) ) {
				if( !empty( $UserSetData[$screen->id]["screenoptions"] ) ) {
					add_filter( 'screen_options_show_screen' , '__return_false' );
				}
				if( !empty( $UserSetData[$screen->id]["help"] ) ) {
					$screen->remove_help_tabs();
				}
			}
			
		}

	}

}

$Sohc = new Sohc();
