<?php

if ( !class_exists( 'Sohc_Plugin_Info' ) ) :

class Sohc_Plugin_Info
{

	var $links = array();
	var $DonateKey = 'd77aec9bc89d445fd54b4c988d090f03';
	var $DonateRecord = '';
	var $DonateOptionRecord = '';

	function __construct() {
		
		add_action( 'plugins_loaded' , array( $this , 'set_links' ) , 20 );
		add_action( 'plugins_loaded' , array( $this , 'setup' ) , 20 );
		add_action( 'plugins_loaded' , array( $this , 'set_ajax' ) , 20 );
		
	}

	function set_links() {
		
		global $Sohc;

		$this->links['author'] = 'http://gqevu6bsiz.chicappa.jp/';
		$this->links['forum'] = 'http://wordpress.org/support/plugin/' . $Sohc->Plugin['plugin_slug'];
		$this->links['review'] = 'http://wordpress.org/support/view/plugin-reviews/' . $Sohc->Plugin['plugin_slug'];
		$this->links['profile'] = 'http://profiles.wordpress.org/gqevu6bsiz';
		
		if( $Sohc->Current['multisite'] ) {
			
			$this->links['setting'] = network_admin_url( 'settings.php?page=' . $Sohc->Plugin['page_slug'] );
		
		} else {

			$this->links['setting'] = admin_url( 'options-general.php?page=' . $Sohc->Plugin['page_slug'] );

		}
		
	}

	function setup() {
		
		global $Sohc;
		
		$this->DonateRecord = $Sohc->Plugin['ltd'] . '_donated';
		$this->DonateOptionRecord = $Sohc->Plugin['ltd'] . '_donate_width';
		
		if( is_admin() && $Sohc->ClassManager->is_manager && !$Sohc->Current['ajax'] ) {
			
			$base_plugin = trailingslashit( $Sohc->Plugin['plugin_slug'] ) . $Sohc->Plugin['plugin_slug'] . '.php';
			
			if( $Sohc->Current['multisite'] ) {
				
				add_action( 'network_admin_notices' , array( $this , 'donate_notice' ) );

			} else {

				add_action( 'admin_notices' , array( $this , 'donate_notice' ) );

			}
			
		}

	}
	
	function set_ajax() {
		
		global $Sohc;
		
		if( $Sohc->Current['admin'] && $Sohc->Current['ajax'] ) {
			add_action( 'wp_ajax_' . $Sohc->Plugin['ltd'] . '_donation_toggle' , array( $this , 'wp_ajax_donation_toggle' ) );
		}

	}

	function wp_ajax_donation_toggle() {
		
		global $Sohc;

		if( isset( $_POST['f'] ) ) {

			$val = intval( $_POST['f'] );
			$Sohc->ClassData->update_donate_toggle( $val );

		}
		
		die();
		
	}

	function is_donated() {
		
		global $Sohc;

		$donated = false;
		$donateKey = $Sohc->ClassData->get_donate_key( $this->DonateRecord );

		if( !empty( $donateKey ) && $donateKey == $this->DonateKey ) {
			$donated = true;
		}

		return $donated;

	}

	function donate_notice() {
		
		global $Sohc;
		
		if( $Sohc->ClassManager->is_settings_page() ) {
			
			$is_donated = $this->is_donated();
			if( empty( $is_donated ) )
				printf( '<div class="updated"><p><strong><a href="%1$s" target="_blank">%2$s</a></strong></p></div>' , $this->author_url( array( 'donate' => 1 , 'tp' => 'use_plugin' , 'lc' => 'footer' ) ) , __( 'Please consider making a donation.' , $Sohc->Plugin['ltd'] ) );
				
		}

	}
	
	function version_checked() {

		global $Sohc;

		$readme = file_get_contents( $Sohc->Plugin['dir'] . 'readme.txt' );
		$items = explode( "\n" , $readme );
		$version_checked = '';
		foreach( $items as $key => $line ) {
			if( strpos( $line , 'Requires at least: ' ) !== false ) {
				$version_checked .= str_replace( 'Requires at least: ' , '' ,  $line );
				$version_checked .= ' - ';
			} elseif( strpos( $line , 'Tested up to: ' ) !== false ) {
				$version_checked .= str_replace( 'Tested up to: ' , '' ,  $line );
				break;
			}
		}
		
		return $version_checked;
		
	}

	function author_url( $args ) {
		
		global $Sohc;

		$url = 'http://gqevu6bsiz.chicappa.jp/';
		
		if( !empty( $args['translate'] ) ) {
			$url .= 'please-translation/';
		} elseif( !empty( $args['donate'] ) ) {
			$url .= 'please-donation/';
		} elseif( !empty( $args['contact'] ) ) {
			$url .= 'contact-us/';
		}
		
		$url .= $this->get_utm_link( $args );

		return $url;

	}

	function get_utm_link( $args ) {
		
		global $Sohc;

		$url = '?utm_source=' . $args['tp'];
		$url .= '&utm_medium=' . $args['lc'];
		$url .= '&utm_content=' . $Sohc->Plugin['ltd'];
		$url .= '&utm_campaign=' . str_replace( '.' , '_' , $Sohc->Plugin['ver'] );

		return $url;

	}

	function is_donate_key_check( $key ) {
		
		$check = false;
		$key = md5( strip_tags( $key ) );
		if( $this->DonateKey == $key )
			$check = $key;

		return $check;

	}

	function get_width_class() {
		
		global $Sohc;

		$class = $Sohc->Plugin['ltd'];
		
		if( $this->is_donated() ) {
			$width_option = $Sohc->ClassData->get_donate_width();
			if( !empty( $width_option ) ) {
				$class .= ' full-width';
			}
		}
		
		return $class;

	}
	
	function get_gravatar_src( $size = 40 ) {
		
		global $Sohc;

		$img_src = $Sohc->Current['schema'] . 'www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=' . $size;

		return $img_src;

	}

	function admin_footer_text() {
		
		$author_url = $this->author_url( array( 'tp' => 'use_plugin' , 'lc' => 'footer' ) );
		$text = sprintf( '<a href="%1$s" target="_blank"><img src="%2$s" width="18" /></a>' ,  $author_url , $this->get_gravatar_src( '18' ) );
		$text .= sprintf( 'Plugin developer : <a href="%s" target="_blank">gqevu6bsiz</a>' , $author_url );

		return $text;
		
	}

}

endif;
