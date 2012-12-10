<?php

$PageTitle = __( 'Screen Options and Help Show Customize' , $this->Td );

// include js css
$ReadedJs = array( 'jquery' , 'jquery-ui-sortable' );
wp_enqueue_script( $this->Slug ,  $this->Dir . dirname( dirname( plugin_basename( __FILE__ ) ) ) . '.js', $ReadedJs , $this->Ver );
wp_enqueue_style( $this->Slug , $this->Dir . dirname( dirname( plugin_basename( __FILE__ ) ) ) . '.css', array() , $this->Ver );

// get data
$Data = $this->get_data();

?>

<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<h2><?php echo $PageTitle; ?></h2>
	<?php echo $this->Msg; ?>
	<p><?php _e( 'Please set by clicking on the item you want to set.' , $this->Td ); ?>
	<p><?php _e( 'Please check the items you want to hide.' , $this->Td ); ?>

	<form id="sohc" method="post" action="">
		<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y">
		<?php wp_nonce_field(); ?>

		<div class="metabox-holder columns-2">

			<div class="postbox-container" id="postbox-container-1">
				<?php echo $this->get_lists( 'Dashboard' , 'dashboard' , $Data ); ?>
				<?php echo $this->get_lists( 'Links' , 'link-manager' , $Data ); ?>
				<?php echo $this->get_lists( 'Add New Link' , 'link' , $Data ); ?>
				<?php echo $this->get_lists( 'Link Categories' , 'edit-link_category' , $Data ); ?>
				<?php echo $this->get_lists( 'Manage Themes' , 'themes' , $Data ); ?>
				<?php echo $this->get_lists( 'Install Themes' , 'theme-install' , $Data ); ?>
				<?php echo $this->get_lists( 'Widgets' , 'widgets' , $Data ); ?>
				<?php echo $this->get_lists( 'Menus' , 'nav-menus' , $Data ); ?>
				<?php echo $this->get_lists( 'Users' , 'users' , $Data ); ?>
				<?php echo $this->get_lists( 'Add New User' , 'user' , $Data ); ?>
				<?php echo $this->get_lists( 'Profile' , 'profile' , $Data ); ?>
				<?php echo $this->get_lists( 'Tools' , 'tools' , $Data ); ?>
			</div>

			<div class="postbox-container" id="postbox-container-2">
				<?php echo $this->get_lists( 'All Posts' , 'edit-post' , $Data ); ?>
				<?php echo $this->get_lists( 'New Post' , 'post' , $Data ); ?>
				<?php echo $this->get_lists( 'Categories' , 'edit-category' , $Data ); ?>
				<?php echo $this->get_lists( 'Tags' , 'edit-post_tag' , $Data ); ?>
				<?php echo $this->get_lists( 'Media Library' , 'upload' , $Data ); ?>
				<?php echo $this->get_lists( 'Upload New Media' , 'media' , $Data ); ?>
				<?php echo $this->get_lists( 'Pages' , 'edit-page' , $Data ); ?>
				<?php echo $this->get_lists( 'Add New Page' , 'page' , $Data ); ?>
				<?php echo $this->get_lists( 'Comments' , 'edit-comments' , $Data ); ?>
				<?php echo $this->get_lists( 'Edit Comment' , 'comment' , $Data ); ?>
			</div>

			<div class="clear"></div>

		</div>

		<p class="submit">
			<input type="submit" class="button-primary" name="update" value="<?php _e( 'Save' ); ?>" />
		</p>
		<p class="submit reset">
			<span class="description"><?php _e( 'Would initialize?' , 'plvc' ); ?></span>
			<input type="submit" class="button-secondary" name="reset" value="<?php _e( 'Reset' ); ?>" />
		</p>

	</form>
</div>
