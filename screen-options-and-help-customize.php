<?php
/*
Plugin Name: Screen Options and Help Show Customize
Description: Screen options and help show customize.
Plugin URI: http://gqevu6bsiz.chicappa.jp
Version: 1.1
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/author/admin/
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
		$UPFN,
		$Msg;


	function __construct() {
		$this->Ver = '1.1';
		$this->Name = 'Screen Options and Help Show Customize';
		$this->Dir = WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) . '/';
		$this->Slug = 'screen_option_and_help_show_customize';
		$this->RecordName = 'sohc_options';
		$this->Td = 'sohc';
		$this->UPFN = 'Y';

		$this->PluginSetup();
		add_action( 'admin_head' , array( $this , 'FilterStart' ) );
		add_action( 'load-settings_page_' . $this->Slug , array( $this , 'export' ) );
		add_action( 'load-settings_page_' . $this->Slug , array( $this , 'import' ) );
	}

	// PluginSetup
	function PluginSetup() {
		// load text domain
		load_plugin_textdomain( $this->Td , false , basename( dirname( __FILE__ ) ) . '/languages' );

		// plugin links
		add_filter( 'plugin_action_links' , array( $this , 'plugin_action_links' ) , 10 , 2 );

		// add menu
		add_action( 'admin_menu' , array( $this , 'admin_menu' ) );
	}

	// PluginSetup
	function plugin_action_links( $links , $file ) {
		if( plugin_basename(__FILE__) == $file ) {
			$link = '<a href="' . 'admin.php?page=' . $this->Slug . '">' . __('Settings') . '</a>';
			array_unshift( $links, $link );
		}
		return $links;
	}

	// PluginSetup
	function admin_menu() {
		add_options_page(  __( 'Screen Options and Help Show Customize' ) , __( 'Screen Options Customize' , $this->Td ) , 'administrator', $this->Slug , array( $this , 'settings'));
	}


	// SettingPage
	function settings() {

		if( !empty( $_POST["reset"] ) ) {
			$this->update_reset();
		} elseif( !empty( $_POST[$this->UPFN] ) ) {
			$this->update();
		}

		include_once 'inc/settings.php';
	}


	// Data get
	function get_data() {
		$NewData = array();
		$Data = get_option( $this->RecordName );
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
	function get_lists( $type , $screenid , $Data ) {
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
					
					$helponly = $this->helponly( $screenid );
					if( empty( $helponly ) ) {
						$Contents .= '<th><label>';
							$Contents .= __( 'Screen Options' );
						$Contents .= '</label></th>';
					}
					$Contents .= '<th><label>';
						$Contents .= __( 'Help' );
					$Contents .= '</label></th>';
				$Contents .= '</tr>';
			$Contents .= '</thead>';
			$Contents .= '<tbody>';
			
			foreach($this->get_users() as $name => $name_d) {
				$Contents .= '<tr>';
					$Contents .= '<td>';
						$Contents .= __( $name_d );
					$Contents .= '</td>';
					if( empty( $helponly ) ) {
						$Contents .= '<td>';
							$Checked = '';
							if( !empty( $Data[$name][$screenid]["screenoptions"] ) ) {
								$Checked = 'checked="checked"';
							}
							$Contents .= '<label><input type="checkbox" name="data[' . $name . '][' . $screenid . '][]" value="screenoptions" ' . $Checked . '> ' . __( 'Hide' ) . '</labe>';
						$Contents .= '</td>';
					}
					$Contents .= '<td>';
						$Checked = '';
						if( !empty( $Data[$name][$screenid]["help"] ) ) {
							$Checked = 'checked="checked"';
						}
						$Contents .= '<label><input type="checkbox" name="data[' . $name . '][' . $screenid . '][]" value="help" ' . $Checked . '> ' . __( 'Hide' ) . '</labe>';
					$Contents .= '</td>';
				$Contents .= '</tr>';
			}

			$Contents .= '</tbody>';

			// all check
			$Contents .= '<tfoot>';
				$Contents .= '<tr>';
					$Contents .= '<td>&nbsp;</td>';
					if( empty( $helponly ) ) {
						$Contents .= '<td><label><input type="checkbox" name="all_checked" title="' . $screenid . '" class="screenoptions" /> ' . __( 'All checked' , $this->Td ) . '</label></td>';
					}
					
					$Contents .= '<td><label><input type="checkbox" name="all_checked" title="' . $screenid . '" class="help" /> ' . __( 'All checked' , $this->Td ) . '</label></td>';
				$Contents .= '</tr>';
			$Contents .= '</tfoot>';

		$Contents .= '</table>';
		$Contents .= '</div>';
		$Contents .= '</div>';
		
		return $Contents;
	}

	// Setting Item
	function helponly( $screenid ) {
		$helparr = array( 'profile' , 'media' , 'comment' , 'themes' , 'theme-install' , 'user' , 'tools' );
		return in_array( $screenid , $helparr );
	}

	// Update Setting
	function update() {
		$UPFN = strip_tags( $_POST[$this->UPFN] );
		if( $UPFN == 'Y' ) {
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

	// Update Reset
	function update_reset() {
		$Record = $this->RecordName;
		delete_option( $Record );
		$this->Msg = '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
	}


	// Data Export
	function export() {
		$Data = $this->get_data();
		if( !empty( $Data ) && !empty( $_GET["download"] ) ) {

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

		if( !empty( $_POST["upload"] ) && !empty( $_FILES["import"] ) ) {

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
				update_option( $Record , $Update );
				$this->Msg = '<div class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
			}

		}
		
	}



	// FilterStart
	function FilterStart() {
		$Data = $this->get_data();
		if( !empty( $Data) ) {
			$this->ScreenMeta();
		}
	}
	
	// FilterStart
	function ScreenMeta() {
		$Data = $this->get_data();
		$Userinfo = wp_get_current_user();
		$Userrole = $Userinfo->roles[0];

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
