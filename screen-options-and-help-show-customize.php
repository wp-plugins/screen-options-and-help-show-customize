<?php
/*
Plugin Name: Screen Options and Help Show Customize
Description: Screen options and help to show customize.
Plugin URI:http://wordpress.org/extend/plugins/screen-options-and-help-show-customize/
Version: 1.3.1
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/?utm_source=use_plugin&utm_medium=list&utm_content=sohc&utm_campaign=1_3_1
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



if ( !class_exists( 'Sohc' ) ) :

class Screen_Option_And_Help_Show_Customize
{

	var $Plugin = array();
	var $Current = array();
	var $ThirdParty = array();

	var $ClassConfig;
	var $ClassData;
	var $ClassManager;
	var $ClassInfo;

	function __construct() {

		$inc_path = plugin_dir_path( __FILE__ );
		
		include_once $inc_path . 'inc/class-config.php';
		include_once $inc_path . 'inc/class-data.php';
		include_once $inc_path . 'inc/class-manager.php';
		include_once $inc_path . 'inc/class-plugin-info.php';

		$this->ClassConfig = new Sohc_Config();
		$this->ClassData = new Sohc_Data();
		$this->ClassManager = new Sohc_Manager();
		$this->ClassInfo = new Sohc_Plugin_Info();

		add_action( 'plugins_loaded' , array( $this , 'init' ) , 100 );

	}

	function init() {
		
		load_plugin_textdomain( $this->Plugin['ltd'] , false , $this->Plugin['plugin_slug'] . '/languages' );

		$this->ClassManager->init();

		add_action( 'wp_loaded' , array( $this , 'FilterStart' ) );

	}

	function setting_list( $user_role ) {
		
		$Data = $this->ClassData->get_data_user( $user_role );
		$types = array( 'screenoptions' => __( 'Screen Options' ) , 'help' => __( 'Help' ) );
		$all_screens = $this->ClassConfig->get_all_screens();
		$all_user_roles = $this->ClassConfig->get_all_user_roles();
		
		foreach( $all_screens as $parent_screen_id => $screens ) {
			
			if( !empty( $screens['screen'] ) ) {

				printf( '<tr class="parent_screen"><th colspan="3">%s</th></tr>' , $screens['label'] );
			
				foreach( $screens['screen'] as $screen_id => $screen_set ) {
					
					echo '<tr>';
				
					printf( '<th>%s</th>' , $screen_set['label'] );
	
					foreach( $types as $type => $type_label ) {
					
						echo '<td>';
	
						if( !empty( $screen_set[$type] ) ) {
							
							$name_field = sprintf( 'data[%1$s][%2$s][]' , $user_role , $screen_id );
							
							$title_field = sprintf( __( '%1$s of %2$s' , $this->Plugin['ltd'] ) , $type_label , $all_user_roles[$user_role]['label'] );
							$checked = false;
							if( !empty( $Data[$screen_id][$type] ) ) $checked = 1;
							printf( '<label title="%5$s"><input type="checkbox" name="%2$s" value="%3$s" %4$s /> %1$s</label>' , __( 'Hide' ) ,  $name_field , $type , checked( $checked , 1 , false ) , $title_field );
				
						}
	
						echo '</td>';
	
					}
	
					echo '</tr>';
	
				}
				
			}

		}
		
	}

	// SetList
	function is_list_page() {
		
		global $current_screen;
		
		$check = false;
		$all_screens = $this->ClassConfig->get_all_screens();
		
		foreach( $all_screens as $parent_screen => $screens ) {
			
			if( array_key_exists( $current_screen->id , $screens['screen'] ) ) {
				
				$check = true;
				break;
				
			}
			
		}
		
		return $check;
		
	}
	
	// SetList
	function is_apply_user() {
		
		$check = false;

		$Data = $this->ClassData->get_data_user( $this->Current['user_role'] );

		if( !empty( $Data['UPFN'] ) )
			unset( $Data['UPFN'] );

		if( !empty( $Data ) )
			$check = true;

		return $check;
		
	}




	// FilterStart
	function FilterStart() {

		if( !$this->Current['network_admin'] && $this->Current['admin'] ) {
			
			if( !$this->Current['ajax'] ) {
				
				add_action( 'admin_head' , array( $this , 'hide_screen_options' ) );
				
			}
			
		}

	}

	// FilterStart
	function hide_screen_options() {
		
		global $current_screen;
		
		if( $this->is_apply_user() && $this->is_list_page() ) {
			
			$Data = $this->ClassData->get_data_user( $this->Current['user_role'] );
			$screen_id = $current_screen->id;
			
			if( !empty( $Data[$screen_id] ) ) {

				if( !empty( $Data[$screen_id]['screenoptions'] ) )
					add_filter( 'screen_options_show_screen' , '__return_false' );

				if( !empty( $Data[$screen_id]['help'] ) )
					$current_screen->remove_help_tabs();
				
			}

		}
		
	}

}

$GLOBALS['Sohc'] = new Screen_Option_And_Help_Show_Customize();

endif;
