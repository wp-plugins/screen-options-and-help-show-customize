<?php

global $wp_version;

// include js css
$ReadedJs = array( 'jquery' , 'jquery-ui-sortable' );
wp_enqueue_script( $this->PageSlug ,  $this->Url . $this->PluginSlug . '.js', $ReadedJs , $this->Ver );

if ( version_compare( $wp_version , '3.8' , '<' ) ) {
	wp_enqueue_style( $this->PageSlug , $this->Url . $this->PluginSlug . '-3.7.css', array() , $this->Ver );
} else {
	wp_enqueue_style( $this->PageSlug , $this->Url . $this->PluginSlug . '.css', array() , $this->Ver );
}

// get data
$Data = $this->get_data();
$CustomPosts = $this->get_custom_posts();
$CustomTaxs = $this->get_custom_taxs();

?>

<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<h2><?php _e( 'Screen Options and Help Show Customize' , $this->ltd ); ?></h2>
	<?php echo $this->Msg; ?>

	<p><?php _e( 'Please set by clicking on the item you want to set.' , $this->ltd ); ?>
	<p><?php _e( 'Please check the items you want to hide.' , $this->ltd ); ?>
	<?php if( is_network_admin() ) : ?>
		<p><strong><?php _e ( 'Data set in the network management screen is applied to all site management screen.' , $this->ltd ); ?></strong></p>
	<?php endif; ?>

	<div id="sohc" class="metabox-holder columns-2">

		<div class="postbox-container" id="postbox-container-1">

			<form id="sohc_form" method="post" action="">
				<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y">
				<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>

				<?php echo $this->get_lists( 'Dashboard' , 'dashboard' , $Data , true , true ); ?>

				<?php if( defined( 'WP_ALLOW_MULTISITE' ) ) : ?>
					<?php echo $this->get_lists( 'My Sites' , 'my-sites' , $Data , true , false ); ?>
				<?php endif; ?>

				<div class="clear"></div>

				<?php echo $this->get_lists( 'All Posts' , 'edit-post' , $Data , true , true ); ?>
				<?php echo $this->get_lists( 'New Post' , 'post' , $Data , true , true ); ?>

				<?php echo $this->get_lists( 'Categories' , 'edit-category' , $Data , true , true ); ?>
				<?php echo $this->get_lists( 'Tags' , 'edit-post_tag' , $Data , true , true ); ?>

				<div class="clear"></div>

				<?php echo $this->get_lists( 'Media Library' , 'upload' , $Data , true , true ); ?>
				<?php echo $this->get_lists( 'Upload New Media' , 'media' , $Data , true , false ); ?>

				<div class="clear"></div>

				<?php if ( get_option( 'link_manager_enabled' ) ) : ?>
					<?php echo $this->get_lists( 'Links' , 'link-manager' , $Data , true , true ); ?>
					<?php echo $this->get_lists( 'Add New Link' , 'link' , $Data , true , true ); ?>
					<?php echo $this->get_lists( 'Link Categories' , 'edit-link_category' , $Data , true , true ); ?>
				<?php endif; ?>

				<div class="clear"></div>

				<?php echo $this->get_lists( 'Pages' , 'edit-page' , $Data , true , true ); ?>
				<?php echo $this->get_lists( 'Add New Page' , 'page' , $Data , true , true ); ?>

				<div class="clear"></div>

				<?php echo $this->get_lists( 'Comments' , 'edit-comments' , $Data , true , true ); ?>
				<?php echo $this->get_lists( 'Edit Comment' , 'comment' , $Data , true , false ); ?>

				<div class="clear"></div>

				<?php echo $this->get_lists( 'Manage Themes' , 'themes' , $Data , true , false ); ?>
				<?php if( !defined( 'WP_ALLOW_MULTISITE' ) ) : ?>
					<?php echo $this->get_lists( 'Install Themes' , 'theme-install' , $Data , true , false ); ?>
				<?php endif; ?>

				<div class="clear"></div>

				<?php echo $this->get_lists( 'Widgets' , 'widgets' , $Data , true , true ); ?>
				<?php echo $this->get_lists( 'Menus' , 'nav-menus' , $Data , true , true ); ?>

				<div class="clear"></div>

				<?php echo $this->get_lists( 'Users' , 'users' , $Data , true , true ); ?>
				<?php echo $this->get_lists( 'Add New User' , 'user' , $Data , true , false ); ?>
				<?php echo $this->get_lists( 'Profile' , 'profile' , $Data , true , false ); ?>

				<div class="clear"></div>

				<?php echo $this->get_lists( 'Tools' , 'tools' , $Data , true , false ); ?>
				
				<div class="clear"></div>
				
				<?php if( defined( 'WP_ALLOW_MULTISITE' ) ) : ?>
					<?php echo $this->get_lists( 'General Settings' , 'options-general' , $Data , true , false ); ?>
					<?php echo $this->get_lists( 'Writing Settings' , 'options-writing' , $Data , true , false ); ?>
					<?php echo $this->get_lists( 'Reading Settings' , 'options-reading' , $Data , true , false ); ?>
					<?php echo $this->get_lists( 'Discussion' , 'options-discussion' , $Data , true , false ); ?>
					<?php echo $this->get_lists( 'Media' , 'options-media' , $Data , true , false ); ?>
					<?php echo $this->get_lists( 'Permalink Settings' , 'options-permalink' , $Data , true , false ); ?>
					<div class="clear"></div>
				<?php endif; ?>

				<h3><?php _e( 'Custom post types' , $this->ltd ); ?></h3>

				<?php if( defined( 'WP_ALLOW_MULTISITE' ) ) : ?>
					<p class="description"><?php _e( 'It can be select when posted of the Custom Post types exists.' , $this->ltd ); ?></p>
				<?php endif; ?>

				<?php if( !empty( $CustomPosts ) ) : ?>
					<?php foreach( $CustomPosts as $custom_id => $Cpt ) : ?>

						<?php echo $this->get_lists( $Cpt["edit"] , 'edit-' . $custom_id , $Data , false , true ); ?>
						<?php echo $this->get_lists( $Cpt["add"] , $custom_id , $Data , false , true ); ?>
						
						<div class="clear"></div>

					<?php endforeach; ?>
				<?php endif; ?>
				
				<h3><?php _e( 'Custom taxonomies' , $this->ltd ); ?></h3>

				<?php if( defined( 'WP_ALLOW_MULTISITE' ) ) : ?>
					<p class="description"><?php _e( 'It can be select when created of the Custom Taxonomies exists.' , $this->ltd ); ?></p>
				<?php endif; ?>

				<?php if( !empty( $CustomTaxs ) ) : ?>
					<?php foreach( $CustomTaxs as $custom_id => $Ctax ) : ?>

						<?php echo $this->get_lists( $Ctax["edit"] , 'edit-' . $custom_id , $Data , false , true ); ?>
						
					<?php endforeach; ?>

					<div class="clear"></div>
				<?php endif; ?>

				<p class="submit">
					<input type="submit" class="button-primary" name="update" value="<?php _e( 'Save' ); ?>" />
				</p>
		
				<p class="submit reset">
					<span class="description"><?php _e( 'Reset all settings?' , $this->ltd ); ?></span>
					<input type="submit" class="button-secondary" name="reset" value="<?php _e( 'Reset settings' , $this->ltd ); ?>" />
				</p>
				
			</form>

		</div>
			
		<div class="postbox-container" id="postbox-container-2">
			
			<div class="stuffbox" style="border-color: #FFC426; border-width: 3px;">
				<h3 style="background: #FFF2D0; border-color: #FFC426;"><span class="hndle"><?php _e( 'Have you want to customize?' , $this->ltd ); ?></span></h3>
				<div class="inside">
					<p style="float: right;">
						<img src="<?php echo $this->Schema; ?>www.gravatar.com/avatar/7e05137c5a859aa987a809190b979ed4?s=46" width="46" /><br />
						<a href="<?php echo $this->AuthorUrl; ?>contact-us/?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank">gqevu6bsiz</a>
					</p>
					<p><?php _e( 'I am good at Admin Screen Customize.' , $this->ltd ); ?></p>
					<p><?php _e( 'Please consider the request to me if it is good.' , $this->ltd ); ?></p>
					<p>
						<a href="http://wpadminuicustomize.com/blog/category/example/?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank"><?php _e ( 'Example Customize' , $this->ltd ); ?></a> :
						<a href="<?php echo $this->AuthorUrl; ?>contact-us/?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank"><?php _e( 'Contact me' , $this->ltd ); ?></a></p>
				</div>
			</div>

			<?php $donatedKey = get_option( $this->ltd . '_donated' ); ?>

			<?php if( $donatedKey == $this->DonateKey ) : ?>

				<span class="description"><?php _e( 'Thank you for your donation.' , $this->ltd ); ?></span>

			<?php else: ?>

				<div class="stuffbox" id="donationbox">
					<div class="inside">
						<p style="color: #FFFFFF; font-size: 20px;"><?php _e( 'Please donation.' , $this->ltd ); ?></p>
						<p style="color: #FFFFFF;"><?php _e( 'You are contented with this plugin?<br />By the laws of Japan, Japan\'s new paypal user can not make a donation button.<br />So i would like you to buy this plugin as the replacement for the donation.' , $this->ltd ); ?></p>
						<p>&nbsp;</p>
						<p style="text-align: center;">
							<a href="<?php echo $this->AuthorUrl; ?>line-break-first-and-end/?utm_source=use_plugin&utm_medium=donate&utm_content=sohc&utm_campaign=1_2_2" class="button-primary" target="_blank">Line Break First and End</a>
						</p>
						<p>&nbsp;</p>
						<div class="donation_memo">
							<p><strong><?php _e( 'Features' , $this->ltd ); ?></strong></p>
							<p><?php _e( 'Line Break First and End plugin is In the visual editor TinyMCE, It is a plugin that will help when you will not be able to enter a line break.' , $this->ltd ); ?></p>
						</div>
						<div class="donation_memo">
							<p><strong><?php _e( 'The primary use of donations' , $this->ltd ); ?></strong></p>
							<ul>
								<li>- <?php _e( 'Liquidation of time and value' , $this->ltd ); ?></li>
								<li>- <?php _e( 'Additional suggestions feature' , $this->ltd ); ?></li>
								<li>- <?php _e( 'Maintain motivation' , $this->ltd ); ?></li>
								<li>- <?php _e( 'Ensure time as the father of Sunday' , $this->ltd ); ?></li>
							</ul>
						</div>
	
						<form id="donation_form" method="post" action="">
							<h4 style="color: #FFF;"><?php _e( 'If you have already donated to.' , $this->ltd ); ?></h4>
							<p style="color: #FFF;"><?php _e( 'Please enter the \'Donation delete key\' that have been described in the \'Line Break First and End download page\'.' , $this->ltd ); ?></p>
							<input type="hidden" name="<?php echo $this->UPFN; ?>" value="Y" />
							<?php wp_nonce_field(); ?>
							<label for="donate_key"><span style="color: #FFF; "><?php _e( 'Donation delete key' , $this->ltd ); ?></span></label>
							<input type="text" name="donate_key" id="donate_key" value="" class="small-text" />
							<input type="submit" class="button-secondary" name="update" value="<?php _e( 'Submit' ); ?>" />
						</form>
	
					</div>
					
				</div>

			<?php endif; ?>

			<div class="stuffbox" id="aboutbox">
				<h3><span class="hndle"><?php _e( 'About plugin' , $this->ltd ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Version checked' , $this->ltd ); ?> : 3.6.1 - 3.8</p>
					<ul>
						<li><a href="<?php echo $this->AuthorUrl; ?>?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank"><?php _e( 'Developer\'s site' , $this->ltd ); ?></a></li>
						<li><a href="http://wordpress.org/support/plugin/<?php echo $this->PluginSlug; ?>" target="_blank"><?php _e( 'Support Forums' ); ?></a></li>
						<li><a href="http://wordpress.org/support/view/plugin-reviews/<?php echo $this->PluginSlug; ?>" target="_blank"><?php _e( 'Reviews' , $this->ltd ); ?></a></li>
						<li><a href="https://twitter.com/gqevu6bsiz" target="_blank">twitter</a></li>
						<li><a href="http://www.facebook.com/pages/Gqevu6bsiz/499584376749601" target="_blank">facebook</a></li>
					</ul>
				</div>
			</div>

			<div class="stuffbox" id="usefulbox">
				<h3><span class="hndle"><?php _e( 'Useful plugins' , $this->ltd ); ?></span></h3>
				<div class="inside">
					<p><strong><a href="http://wpadminuicustomize.com/?utm_source=use_plugin&utm_medium=side&utm_content=<?php echo $this->ltd; ?>&utm_campaign=<?php echo str_replace( '.' , '_' , $this->Ver ); ?>" target="_blank">WP Admin UI Customize</a></strong></p>
					<p class="description"><?php _e( 'Customize a variety of screen management.' , $this->ltd ); ?></p>
					<p><strong><a href="http://wordpress.org/extend/plugins/post-lists-view-custom/" target="_blank">Post Lists View Custom</a></strong></p>
					<p class="description"><?php _e( 'Customize the list of the post and page. custom post type page, too. You can customize the column display items freely.' , $this->ltd ); ?></p>
					<p><strong><a href="http://wordpress.org/extend/plugins/announce-from-the-dashboard/" target="_blank">Announce from the Dashboard</a></strong></p>
					<p class="description"><?php _e( 'Announce to display the dashboard. Change the display to a different user role.' , $this->ltd ); ?></p>
					<p>&nbsp;</p>
				</div>
			</div>

		</div>

		<div class="clear"></div>

	</div>

	<p>&nbsp;</p>

	<div class="postbox widget">
		<div class="widget-top">
			<div class="widget-title"><h4><?php _e( 'Import and Export' , $this->ltd ); ?></h4></div>
		</div>
		<div class="inside">

			<p><strong><?php _e( 'Export' ); ?></strong></p>
			<p>
				<form id="file_export" method="get" action="">
					<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>
					<input type="hidden" name="page" value="<?php echo $this->PageSlug; ?>" />
					<input type="hidden" name="download" value="true" />
					<input type="submit" class="button-secondary" name="export" value="<?php _e( 'Download Export File' ); ?>" />
				</form>
			</p>

			<p><strong><?php _e( 'Import' ); ?></strong></p>
			<p>
				<form id="file_import" method="post" action="" enctype="multipart/form-data">
					<?php wp_nonce_field( $this->Nonces["value"] , $this->Nonces["field"] ); ?>
					<input type="hidden" name="upload" value="true" />
					<label for="import"><?php _e( 'Choose a file from your computer:' ); ?></label>
					<input type="file" name="import" size="25" />
					<input type="submit" class="button-secondary" name="submit" value="<?php _e( 'Upload file and import' ); ?>" />
				</form>
			</p>

		</div>
	</div>

</div>
