<?php

if ( !class_exists( 'Sohc_Config' ) ) :

class Sohc_Config
{

	function __construct() {
		
		add_action( 'plugins_loaded' , array( $this , 'setup_config' ) );
		add_action( 'plugins_loaded' , array( $this , 'setup_record' ) );
		add_action( 'plugins_loaded' , array( $this , 'setup_site_env' ) );
		add_action( 'plugins_loaded' , array( $this , 'setup_current_env' ) );
		add_action( 'plugins_loaded' , array( $this , 'setup_third_party' ) );
		
	}

	function setup_config() {
		
		global $Sohc;

		$Sohc->Plugin['ver']              = '1.3.1';
		$Sohc->Plugin['plugin_slug']      = 'screen-options-and-help-show-customize';
		$Sohc->Plugin['dir']              = trailingslashit( dirname( dirname( __FILE__ ) ) );
		$Sohc->Plugin['name']             = 'Screen Options and Help Show Customize';
		$Sohc->Plugin['page_slug']        = 'sohc';
		$Sohc->Plugin['url']              = plugin_dir_url( dirname( __FILE__ ) );
		$Sohc->Plugin['ltd']              = 'sohc';
		$Sohc->Plugin['nonces']           = array( 'field' => $Sohc->Plugin['ltd'] . '_field' , 'value' => $Sohc->Plugin['ltd'] . '_value' );
		$Sohc->Plugin['UPFN']             = 'Y';
		$Sohc->Plugin['msg_notice']       = $Sohc->Plugin['ltd'] . '_msg';
		$Sohc->Plugin['default_role']     = array( 'child' => 'manage_options' , 'network' => 'manage_network' );
		
		$Sohc->Plugin['dir_admin_assets'] = $Sohc->Plugin['url'] . trailingslashit( 'admin' ) . trailingslashit( 'assets' );
		
	}

	function setup_record() {
		
		global $Sohc;
		
		$Sohc->Plugin['record']['option_help']    = $Sohc->Plugin['ltd'] . '_options';
		$Sohc->Plugin['record']['other']        = $Sohc->Plugin['ltd'] . '_other';
		
	}
	
	function setup_site_env() {
		
		global $Sohc;

		$Sohc->Current['multisite'] = is_multisite();
		$Sohc->Current['blog_id'] = get_current_blog_id();

		$Sohc->Current['main_blog'] = false;
		if( $Sohc->Current['blog_id'] == 1 ) {
			$Sohc->Current['main_blog'] = true;
		}

	}

	function setup_current_env() {
		
		global $Sohc;
		
		$Sohc->Current['admin']         = is_admin();
		$Sohc->Current['ajax']          = false;
		$Sohc->Current['user_role']     = false;
		$Sohc->Current['user_login']    = is_user_logged_in();
		$Sohc->Current['network_admin'] = is_network_admin();
		$Sohc->Current['superadmin']    = false;

		if( $Sohc->Current['multisite'] )
			$Sohc->Current['superadmin']    = is_super_admin();

		$User = wp_get_current_user();
		if( !empty( $User->roles ) ) {
			foreach( $User->roles as $role ) {
				$Sohc->Current['user_role'] = $role;
				break;
			}
		}

		if( defined( 'DOING_AJAX' ) )
			$Sohc->Current['ajax'] = true;
			
		$Sohc->Current['schema'] = is_ssl() ? 'https://' : 'http://';

	}
	
	function setup_third_party() {
		
		global $Sohc;

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		$check_plugins = array( 'woocommerce' => 'woocommerce/woocommerce.php' );
		
		if( !empty( $check_plugins ) ) {
			foreach( $check_plugins as $name => $base_name ) {
				if( is_plugin_active( $base_name ) )
					$Sohc->ThirdParty[$name] = true;
			}
		}
		
	}

	function get_all_user_roles() {

		global $Sohc;
		global $wp_roles;

		$UserRole = array();
		$all_user_roles = $wp_roles->roles;
		foreach ( $all_user_roles as $role => $user ) {
			$user['label'] = translate_user_role( $user['name'] );
			$UserRole[$role] = $user;
		}
		
		if( !empty( $Sohc->Current['multisite'] ) && !empty( $Sohc->Current['network_admin'] ) && !empty( $Sohc->Current['superadmin'] ) ) {
			
			$add_caps = array( 'manage_network' , 'manage_network_users' , 'manage_network_themes' , 'manage_network_plugins' , 'manage_network_options' );
			foreach( $add_caps as $cap ) {
				$UserRole[$Sohc->Current['user_role']]['capabilities'][$cap] = 1;
			}
			
		}

		return $UserRole;

	}
	
	function get_all_custom_posts() {
		
		global $wpdb;
		global $Sohc;
		
		$post_types = array();

		if( $Sohc->Current['multisite'] ) {
			
			$all_sites = wp_get_sites();
			$core_posts = get_post_types( array( '_builtin' => true ) , 'names' );
			
			foreach( $all_sites as $site ) {

				switch_to_blog( $site['blog_id'] );

				$sql = 'SELECT `post_type` FROM';
				$sql .= ' `' . $wpdb->posts . '`';
				$sql .= ' GROUP BY `post_type`';
				
				$results_columns = $wpdb->get_col( $sql );

				restore_current_blog();
				
				if( !empty( $results_columns ) ) {
					
					foreach( $results_columns as $post_type_name ) {
						
						$post_types[$post_type_name] = array( 'name' => $post_type_name , 'add' => 'Add New ' . $post_type_name );
						
					}
					
				}
				

			}

			restore_current_blog();
			
			foreach( $core_posts as $post_name ) {
				
				if( !empty( $post_types[$post_name] ) )
					unset( $post_types[$post_name] );
				
			}

		} else {
			
			$custom_posts = get_post_types( array( 'public' => true, '_builtin' => false ) , 'objects' );
			
			if( !empty( $custom_posts ) ) {
				
				foreach( $custom_posts as $post_name => $post_type_obj ) {
					
					$post_types[$post_name] = array( 'name' => $post_type_obj->labels->name , 'add' => $post_type_obj->labels->add_new_item );
					
				}
				
			}

		}
		
		return $post_types;

	}
	
	function get_all_custom_taxonomies() {
		
		global $wpdb;
		global $Sohc;

		$taxonomies = array();

		if( $Sohc->Current['multisite'] ) {
			
			$all_sites = wp_get_sites();
			$core_taxonomies = get_taxonomies( array( '_builtin' => true ) , 'names' );
			
			foreach( $all_sites as $site ) {

				switch_to_blog( $site['blog_id'] );

				$sql = 'SELECT `taxonomy` FROM';
				$sql .= ' `' . $wpdb->term_taxonomy . '`';
				$sql .= ' GROUP BY `taxonomy`';
				
				$results_columns = $wpdb->get_col( $sql );
				
				restore_current_blog();
				
				if( !empty( $results_columns ) ) {
					
					foreach( $results_columns as $tax_name ) {
						
						$taxonomies[$tax_name] = array( 'name' => $tax_name );
						
					}
					
				}

			}

			restore_current_blog();
			
			foreach( $core_taxonomies as $tax_name ) {
				
				if( !empty( $taxonomies[$tax_name] ) )
					unset( $taxonomies[$tax_name] );
				
			}

		} else {
			
			$custom_taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ), 'object' );
	
			if( !empty( $custom_taxonomies ) ) {
	
				foreach( $custom_taxonomies as $tax_name => $tax_obj ) {
					$taxonomies[$tax_name] = array( 'name' => $tax_obj->labels->name );
				}
	
			}
		
		}
		
		return $taxonomies;
		
	}
	
	function get_all_screens() {
		
		global $Sohc;

		$screens = array();

		$screens['dashboard'] = array(
			'label' => __( 'Dashboard' ),
			'screen' => array(
				'dashboard' => array( 'label' => __( 'Home' ) , 'screenoptions' => 1 , 'help' => 1 ),
			)
		);

		if( $Sohc->Current['multisite'] )
			$screens['dashboard']['screen']['my-sites'] = array( 'label' => __( 'My Sites' ) , 'screenoptions' => 0 , 'help' => 1 );


		$screens['posts'] = array(
			'label' => __( 'Posts' ),
			'screen' => array(
				'edit-post' => array( 'label' => __( 'Posts' ) , 'screenoptions' => 1 , 'help' => 1 ),
				'post' => array( 'label' => __( 'New Post' ) , 'screenoptions' => 1 , 'help' => 1 ),
				'edit-category' => array( 'label' => __( 'Categories' ) , 'screenoptions' => 1 , 'help' => 1 ),
				'edit-post_tag' => array( 'label' => __( 'Tags' ) , 'screenoptions' => 1 , 'help' => 1 ),
			)
		);

		$screens['media'] = array(
			'label' => __( 'Media Library' ),
			'screen' => array(
				'upload' => array( 'label' => __( 'Media Library' ) , 'screenoptions' => 1 , 'help' => 1 ),
				'media' => array( 'label' => __( 'Upload New Media' ) , 'screenoptions' => 0 , 'help' => 1 ),
			)
		);

		$screens['pages'] = array(
			'label' => __( 'Pages' ),
			'screen' => array(
				'edit-page' => array( 'label' => __( 'Pages' ) , 'screenoptions' => 1 , 'help' => 1 ),
				'page' => array( 'label' => __( 'Add New Page' ) , 'screenoptions' => 1 , 'help' => 1 ),
			)
		);

		$screens['comments'] = array(
			'label' => __( 'Comments' ),
			'screen' => array(
				'edit-comments' => array( 'label' => __( 'Comments' ) , 'screenoptions' => 1 , 'help' => 1 ),
				'comment' => array( 'label' => __( 'Edit Comment' ) , 'screenoptions' => 0 , 'help' => 1 ),
			)
		);

		$screens['themes'] = array(
			'label' => __( 'Comments' ),
			'screen' => array(
				'themes' => array( 'label' => __( 'Themes' ) , 'screenoptions' => 0 , 'help' => 1 ),
			)
		);
		
		if( !$Sohc->Current['multisite'] )
			$screens['themes']['screen']['theme-install'] = array( 'label' => __( 'Install Themes' ) , 'screenoptions' => 0 , 'help' => 1 );
			
		$screens['themes']['screen']['widgets'] = array( 'label' => __( 'Widgets' ) , 'screenoptions' => 1 , 'help' => 1 );
		$screens['themes']['screen']['nav-menus'] = array( 'label' => __( 'Menus' ) , 'screenoptions' => 1 , 'help' => 1 );

		$screens['users'] = array(
			'label' => __( 'Users' ),
			'screen' => array(
				'users' => array( 'label' => __( 'Users' ) , 'screenoptions' => 1 , 'help' => 1 ),
				'user-edit' => array( 'label' => __( 'Edit User' ) , 'screenoptions' => 0 , 'help' => 1 ),
				'user' => array( 'label' => __( 'Add New User' ) , 'screenoptions' => 0 , 'help' => 1 ),
				'profile' => array( 'label' => __( 'Profile' ) , 'screenoptions' => 0 , 'help' => 1 ),
			)
		);

		$screens['tools'] = array(
			'label' => __( 'Tools' ),
			'screen' => array(
				'tools' => array( 'label' => __( 'Tools' ) , 'screenoptions' => 0 , 'help' => 1 ),
				'import' => array( 'label' => __( 'Import' ) , 'screenoptions' => 0 , 'help' => 1 ),
				'export' => array( 'label' => __( 'Export' ) , 'screenoptions' => 0 , 'help' => 1 ),
			)
		);

		$screens['options-general'] = array(
			'label' => __( 'Settings' ),
			'screen' => array(
				'options-general' => array( 'label' => __( 'General Settings' ) , 'screenoptions' => 0 , 'help' => 1 ),
				'options-writing' => array( 'label' => __( 'Writing Settings' ) , 'screenoptions' => 0 , 'help' => 1 ),
				'options-reading' => array( 'label' => __( 'Reading Settings' ) , 'screenoptions' => 0 , 'help' => 1 ),
				'options-discussion' => array( 'label' => __( 'Discussion' ) , 'screenoptions' => 0 , 'help' => 1 ),
				'options-media' => array( 'label' => __( 'Media' ) , 'screenoptions' => 0 , 'help' => 1 ),
				'options-permalink' => array( 'label' => __( 'Permalink Settings' ) , 'screenoptions' => 0 , 'help' => 1 ),
			)
		);

		$all_custom_posts = $this->get_all_custom_posts();
		if( !empty( $all_custom_posts ) ) {

			$screens['custom-post-types'] = array(
				'label' => __( 'Custom Post Types' , $Sohc->Plugin['ltd'] ),
			);
			
			foreach( $all_custom_posts as $post_type_name => $post_type ) {
				
				$screens['custom-post-types']['screen']['edit-' . $post_type_name] = array( 'label' => $post_type['name'] , 'screenoptions' => 1 , 'help' => 1 );
				$screens['custom-post-types']['screen'][$post_type_name] = array( 'label' => $post_type['add'] , 'screenoptions' => 1 , 'help' => 1 );
				
			}

		}

		$all_custom_taxs = $this->get_all_custom_taxonomies();
		if( !empty( $all_custom_taxs ) ) {

			$screens['custom-taxonomies'] = array(
				'label' => __( 'Custom Taxonomies' , $Sohc->Plugin['ltd'] ),
			);
			
			foreach( $all_custom_taxs as $taxonomy_name => $taxonomy ) {
				
				$screens['custom-taxonomies']['screen']['edit-' . $taxonomy_name] = array( 'label' => $taxonomy['name'] , 'screenoptions' => 1 , 'help' => 1 );
				
			}

		}

		if( !empty( $Sohc->ThirdParty['woocommerce'] ) ) {
			
			$screens['woocommerce']['label'] = 'WooCommerce';

			$woo_settings = array(
				'edit-shop_order' => array( 'type' => 'custom-post-types' , 'settings' => array( 'label' => __( 'Orders', 'woocommerce' ) , 'screenoptions' => 1 , 'help' => 1 ) ),
				'shop_order' => array( 'type' => 'custom-post-types' , 'settings' => array( 'label' => __( 'Add New Order', 'woocommerce' ) , 'screenoptions' => 1 , 'help' => 1 ) ),
				'edit-shop_coupon' => array( 'type' => 'custom-post-types' , 'settings' => array( 'label' => __( 'Coupons', 'woocommerce' ) , 'screenoptions' => 1 , 'help' => 1 ) ),
				'shop_coupon' => array( 'type' => 'custom-post-types' , 'settings' => array( 'label' => __( 'Add New Coupon', 'woocommerce' ) , 'screenoptions' => 1 , 'help' => 1 ) ),
				'woocommerce_page_wc-reports' => array( 'type' => 'page' , 'settings' => array( 'label' => __( 'Reports', 'woocommerce' ) , 'screenoptions' => 0 , 'help' => 1 ) ),
				'woocommerce_page_wc-settings' => array( 'type' => 'page' , 'settings' => array( 'label' => __( 'Settings', 'woocommerce' ) , 'screenoptions' => 0 , 'help' => 1 ) ),
				'woocommerce_page_wc-status' => array( 'type' => 'page' , 'settings' => array( 'label' => __( 'System Status', 'woocommerce' ) , 'screenoptions' => 0 , 'help' => 1 ) ),
				'woocommerce_page_wc-addons' => array( 'type' => 'page' , 'settings' => array( 'label' => __( 'WooCommerce Add-ons/Extensions', 'woocommerce' ) , 'screenoptions' => 0 , 'help' => 1 ) ),
			);
			
			foreach( $woo_settings as $screen_id => $screen_set ) {
				
				if( $screen_set['type'] == 'custom-post-types' ) {

					if( array_key_exists( $screen_id , $screens['custom-post-types']['screen'] ) )
						unset( $screens['custom-post-types']['screen'][$screen_id] );

				}
				
				$screens['woocommerce']['screen'][$screen_id] = $screen_set['settings'];
				
			}
			
			$screens['woocommerce-products']['label'] = 'WooCommerce ' . __( 'Products', 'woocommerce' );
			
			$woo_settings = array(
				'edit-product' => array( 'type' => 'custom-post-types' , 'settings' => array( 'label' => __( 'Products', 'woocommerce' ) , 'screenoptions' => 1 , 'help' => 1 ) ),
				'product' => array( 'type' => 'custom-post-types' , 'settings' => array( 'label' => __( 'Add New Product', 'woocommerce' ) , 'screenoptions' => 1 , 'help' => 1 ) ),
				'edit-product_cat' => array( 'type' => 'custom-taxonomies' , 'settings' => array( 'label' => __( 'Product Categories', 'woocommerce' ) , 'screenoptions' => 1 , 'help' => 1 ) ),
				'edit-product_tag' => array( 'type' => 'custom-taxonomies' , 'settings' => array( 'label' => __( 'Product Tags', 'woocommerce' ) , 'screenoptions' => 1 , 'help' => 1 ) ),
				'edit-product_shipping_class' => array( 'type' => 'custom-taxonomies' , 'settings' => array( 'label' => __( 'Shipping Classes', 'woocommerce' ) , 'screenoptions' => 1 , 'help' => 1 ) ),
				'product_page_product_attributes' => array( 'type' => 'page' , 'settings' => array( 'label' => __( 'Attributes', 'woocommerce' ) , 'screenoptions' => 0 , 'help' => 1 ) ),
			);

			foreach( $woo_settings as $screen_id => $screen_set ) {
				
				if( $screen_set['type'] == 'custom-post-types' ) {

					if( array_key_exists( $screen_id , $screens['custom-post-types']['screen'] ) )
						unset( $screens['custom-post-types']['screen'][$screen_id] );

				} elseif( $screen_set['type'] == 'custom-taxonomies' ) {
					
					if( array_key_exists( $screen_id , $screens['custom-taxonomies']['screen'] ) )
						unset( $screens['custom-taxonomies']['screen'][$screen_id] );

				}
				
				$screens['woocommerce-products']['screen'][$screen_id] = $screen_set['settings'];
				
			}
			
			unset( $screens['custom-taxonomies']['screen']['edit-product_type'] );
			unset( $screens['custom-taxonomies']['screen']['edit-shop_order_status'] );

		}

		return $screens;
		
	}

}

endif;
