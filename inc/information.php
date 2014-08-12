<?php if( $Sohc->ClassInfo->is_donated() ) : ?>

	<p class="donated_message description"><?php _e( 'Thank you for your donation.' , $Sohc->Plugin['ltd'] ); ?></p>
	<div class="toggle-width">
		<a href="javascript:void(0);" class="collapse-sidebar button-secondary">
			<span class="collapse-sidebar-arrow"></span>
			<span class="collapse-sidebar-label"><?php echo esc_html__( 'Collapse' ); ?></span>
		</a>
	</div>

<?php else: ?>

	<div class="stuffbox" id="donationbox">
		<h3><span class="hndle"><?php _e( 'Please consider making a donation.' , $Sohc->Plugin['ltd'] ); ?></span></h3>
		<div class="inside">
			<p><?php _e( 'Thank you very much for your support.' , $Sohc->Plugin['ltd'] ); ?></p>
			<p><a href="<?php echo $Sohc->ClassInfo->author_url( array( 'donate' => 1 , 'tp' => 'use_plugin' , 'lc' => 'donate' ) ); ?>" class="button button-primary" target="_blank"><?php _e( 'Donate' , $Sohc->Plugin['ltd'] ); ?></a></p>
			<p><?php _e( 'Please enter the \'Donation delete key\' that have been described in the \'Line Break First and End download page\'.' , $Sohc->Plugin['ltd'] ); ?></p>
			<form id="<?php echo $Sohc->Plugin['ltd']; ?>_donation_form" class="<?php echo $Sohc->Plugin['ltd']; ?>_form" method="post" action="<?php echo $Sohc->ClassManager->get_action_link(); ?>">
				<input type="hidden" name="<?php echo $Sohc->Plugin['ltd']; ?>_settings" value="Y">
				<?php wp_nonce_field( $Sohc->Plugin['nonces']['value'] , $Sohc->Plugin['nonces']['field'] ); ?>
				<input type="hidden" name="record_field" value="donate" />
				<label for="donate_key"><?php _e( 'Donation delete key' , $Sohc->Plugin['ltd'] ); ?></label>
				<input type="text" name="donate_key" id="donate_key" value="" class="large-text" />
				<?php submit_button( __( 'Submit' ) , 'secondary' ); ?>
			</form>

			<h4><?php _e( 'I\'m looking forward to your proposal.' , $Sohc->Plugin['ltd'] ); ?></h4>
			<p><?php _e( 'Please contact me if you have any good idea.' , $Sohc->Plugin['ltd'] ); ?></p>
		</div>
	</div>

<?php endif; ?>

<div class="stuffbox" id="considerbox">
	<h3><span class="hndle"><?php _e( 'Have you want to customize?' , $Sohc->Plugin['ltd'] ); ?></span></h3>
	<div class="inside">
		<p style="float: right;">
			<a href="<?php echo $Sohc->ClassInfo->author_url( array( 'contact' => 1 , 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank">
				<img src="<?php echo $Sohc->ClassInfo->get_gravatar_src( '46' ); ?>" width="46" />
			</a>
		</p>
		<p><?php _e( 'I am good at Admin Screen Customize.' , $Sohc->Plugin['ltd'] ); ?></p>
		<p><?php _e( 'Please consider the request to me if it is good.' , $Sohc->Plugin['ltd'] ); ?></p>
		<p>
			<a href="<?php echo $Sohc->ClassInfo->author_url( array( 'contact' => 1 , 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank"><?php _e( 'Contact' , $Sohc->Plugin['ltd'] ); ?></a>
			| 
			<a href="http://wpadminuicustomize.com/blog/category/example/<?php echo $Sohc->ClassInfo->get_utm_link( array( 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank"><?php _e( 'Example Customize' , $Sohc->Plugin['ltd'] ); ?></a>
	</div>
</div>

<div class="stuffbox" id="aboutbox">
	<h3><span class="hndle"><?php _e( 'About plugin' , $Sohc->Plugin['ltd'] ); ?></span></h3>
	<div class="inside">
		<p><?php _e( 'Version checked' , $Sohc->Plugin['ltd'] ); ?> : <?php echo $Sohc->ClassInfo->version_checked(); ?></p>
		<ul>
			<li><a href="<?php echo $Sohc->ClassInfo->author_url( array( 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank"><?php _e( 'Developer\'s site' , $Sohc->Plugin['ltd'] ); ?></a></li>
			<li><a href="<?php echo $Sohc->ClassInfo->links['forum']; ?>" target="_blank"><?php _e( 'Support Forums' ); ?></a></li>
			<li><a href="<?php echo $Sohc->ClassInfo->links['review']; ?>" target="_blank"><?php _e( 'Reviews' , $Sohc->Plugin['ltd'] ); ?></a></li>
			<li><a href="https://twitter.com/gqevu6bsiz" target="_blank">twitter</a></li>
			<li><a href="http://www.facebook.com/pages/Gqevu6bsiz/499584376749601" target="_blank">facebook</a></li>
		</ul>
		<h4>Translate Help</h4>
		<p><a href="<?php echo $Sohc->ClassInfo->author_url( array( 'translate' => 1 , 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank"><?php _e( 'Would you like to translate?' , $Sohc->Plugin['ltd'] ); ?></a></p>
		<p><?php echo sprintf( __( 'Do you have a proposal you want to improve? Please contact to %s if it is necessary.' , $Sohc->Plugin['ltd'] ) , '<a href="' . $Sohc->ClassInfo->links['forum'] . '" target="_blank">' . __( 'Support Forums' ) . '</a>' ); ?></p>
	</div>
</div>

<div class="stuffbox" id="usefulbox">
	<h3><span class="hndle"><?php _e( 'Useful plugins' , $Sohc->Plugin['ltd'] ); ?></span></h3>
	<div class="inside">
		<p><strong><a href="http://wpadminuicustomize.com/<?php echo $Sohc->ClassInfo->get_utm_link( array( 'tp' => 'use_plugin' , 'lc' => 'side' ) ); ?>" target="_blank">WP Admin UI Customize</a></strong></p>
		<p class="description"><?php _e( 'Customize a variety of screen management.' , $Sohc->Plugin['ltd'] ); ?></p>
		<p><strong><a href="http://wordpress.org/extend/plugins/announce-from-the-dashboard/" target="_blank">Announce from the Dashboard</a></strong></p>
		<p class="description"><?php _e( 'Announce to display the dashboard. Change the display to a different user role.' , $Sohc->Plugin['ltd'] ); ?></p>
		<p><strong><a href="http://wordpress.org/extend/plugins/custom-options-plus-post-in/" target="_blank">Custom Options Plus Post in</a></strong></p>
		<p class="description"><?php _e( 'The plugin that allows you to add the value of the options. Option value that you have created, can be used in addition to the template tag, Short code can be used in the body of the article.' , $Sohc->Plugin['ltd'] ); ?></p>
		<p><strong><a href="http://wordpress.org/extend/plugins/post-lists-view-custom/" target="_blank">Post Lists View Custom</a></strong></p>
		<p class="description"><?php _e( 'Customize the list of the post and page. custom post type page, too. You can customize the column display items freely.' , $Sohc->Plugin['ltd'] ); ?></p>
		<p>&nbsp;</p>
		<p><a href="<?php echo $Sohc->ClassInfo->links['profile']; ?>" target="_blank"><?php _e( 'Plugins' ); ?></a></p>
	</div>
</div>
