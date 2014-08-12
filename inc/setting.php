<?php

global $Sohc;

$all_user_roles = $Sohc->ClassConfig->get_all_user_roles();
?>
<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<h2>
		<?php echo $Sohc->Plugin['name']; ?>
		<a href="<?php echo add_query_arg( array( 'page_tab' => 'other' ) , $this->get_action_link() );?>" class="add-new-h2"><?php _e( 'Other Settings' , $Sohc->Plugin['ltd'] ); ?></a>
	</h2>

	<?php if( $Sohc->Current['multisite'] && $Sohc->Current['network_admin'] ) : ?>
		<p><strong><?php _e ( 'Data set in the network management screen is applied to all site management screen.' , $Sohc->Plugin['ltd'] ); ?></strong></p>
	<?php endif; ?>

	<h3 class="nav-tab-wrapper">
		<?php foreach( $all_user_roles as $user_role_name => $user_role ) : ?>
			<?php $class = 'nav-tab'; if( $user_role_name == 'administrator' ) $class .= ' nav-tab-active'; ?>
			<a href="javascript:void(0);" class="<?php echo $class; ?>" title="<?php echo $user_role_name; ?>"><?php echo $user_role['label']; ?></a>
		<?php endforeach; ?>
	</h3>

	<?php $class = $Sohc->ClassInfo->get_width_class(); ?>
	<div class="metabox-holder columns-2 <?php echo $class; ?>">

		<div id="postbox-container-1" class="postbox-container">

			<?php include_once $Sohc->Plugin['dir'] . 'inc/information.php'; ?>
		
		</div>

		<div id="postbox-container-2" class="postbox-container">

			<form id="<?php echo $Sohc->Plugin['ltd']; ?>_user_role_form" class="<?php echo $Sohc->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
				<input type="hidden" name="<?php echo $Sohc->Plugin['ltd']; ?>_settings" value="Y">
				<?php wp_nonce_field( $Sohc->Plugin['nonces']['value'] , $Sohc->Plugin['nonces']['field'] ); ?>
				<input type="hidden" name="record_field" value="<?php echo $Sohc->Plugin['record']['option_help']; ?>" />

				<?php foreach( $all_user_roles as $user_role_name => $user_role ) : ?>

					<div class="<?php echo $Sohc->Plugin['ltd']; ?>_list_table" id="<?php echo $user_role_name; ?>_lists">

						<table class="wp-list-table widefat fixed">
							<?php $arr = array( 'thead' , 'tfoot' ); ?>
							<?php foreach( $arr as $tag ) : ?>
								<<?php echo $tag; ?>>
									<tr>
										<th>
											<?php echo $user_role['label']; ?>
										</th>
										<th>
											<label>
												<input type="checkbox" name="all_check" value="screenoptions" />
												<?php _e( 'Screen Options' ); ?>
											</label>
										</th>
										<th>
											<label>
												<input type="checkbox" name="all_check" value="help" />
												<?php _e( 'Help' ); ?>
											</label>
										</th>
									</tr>
								</<?php echo $tag; ?>>
							<?php endforeach; ?>
							<tbody>
								<?php $Sohc->setting_list( $user_role_name ); ?>
							</tbody>
						</table>
						
					</div>

				<?php endforeach; ?>

				<?php submit_button( __( 'Save' ) ); ?>

			</form>
			
			<p>&nbsp;</p>

			<form id="<?php echo $Sohc->Plugin['ltd']; ?>_user_role_reset_form" class="<?php echo $Sohc->Plugin['ltd']; ?>_form" method="post" action="<?php echo $this->get_action_link(); ?>">
				<input type="hidden" name="<?php echo $Sohc->Plugin['ltd']; ?>_settings" value="Y">
				<?php wp_nonce_field( $Sohc->Plugin['nonces']['value'] , $Sohc->Plugin['nonces']['field'] ); ?>
				<input type="hidden" name="record_field" value="<?php echo $Sohc->Plugin['record']['option_help']; ?>" />
				<input type="hidden" name="reset" value="1" />
				<p class="description"><?php _e( 'Reset all settings?' , $Sohc->Plugin['ltd'] ); ?></p>
				<?php submit_button( __( 'Reset settings' , $Sohc->Plugin['ltd'] ) , 'delete' ); ?>
	
			</form>
			
		</div>

		<div class="clear"></div>

	</div>

	<?php $class = $Sohc->ClassInfo->get_width_class(); ?>
	<div class="metabox-holder columns-1 <?php echo $class; ?>">

		<div class="stuffbox">
			<h3><span class="hndle"><?php _e( 'Import and Export' , $Sohc->Plugin['ltd'] ); ?></span></h3>
			<div class="inside">
	
				<h4><?php _e( 'Export' ); ?></h4>
				<div>
					<form id="<?php echo $Sohc->Plugin['ltd']; ?>_export_form" class="<?php echo $Sohc->Plugin['ltd']; ?>_form" method="get" action="<?php echo $this->get_action_link(); ?>">
						<input type="hidden" name="<?php echo $Sohc->Plugin['ltd']; ?>_settings" value="Y">
						<?php wp_nonce_field( $Sohc->Plugin['ltd'] . '_export_value' , $Sohc->Plugin['ltd'] . '_export_field' ); ?>
						<input type="hidden" name="record_field" value="<?php echo $Sohc->Plugin['record']['option_help']; ?>" />
						<input type="hidden" name="<?php echo $Sohc->Plugin['ltd']; ?>_export" value="1" />
						<input type="hidden" name="page" value="<?php echo $Sohc->Plugin['page_slug']; ?>">
						<?php submit_button( __( 'Download Export File' ) ); ?>
					</form>
				</div>
	
				<h4><?php _e( 'Import' ); ?></h4>
				<p>
					<form id="<?php echo $Sohc->Plugin['ltd']; ?>_import_form" class="<?php echo $Sohc->Plugin['ltd']; ?>_form" method="post" enctype="multipart/form-data" action="<?php echo $this->get_action_link(); ?>">
						<input type="hidden" name="<?php echo $Sohc->Plugin['ltd']; ?>_settings" value="Y">
						<?php wp_nonce_field( $Sohc->Plugin['ltd'] . '_import_value' , $Sohc->Plugin['ltd'] . '_import_field' ); ?>
						<input type="hidden" name="record_field" value="<?php echo $Sohc->Plugin['record']['option_help']; ?>" />
						<input type="hidden" name="<?php echo $Sohc->Plugin['ltd']; ?>_import" value="1" />
						<label for="<?php echo $Sohc->Plugin['ltd']; ?>_import_file"><?php _e( 'Choose a file from your computer:' ); ?></label>
						<input type="file" name="<?php echo $Sohc->Plugin['ltd']; ?>_import_file" id="<?php echo $Sohc->Plugin['ltd']; ?>_import_file" size="25" />
						<input type="submit" class="button button-primary" name="submit" value="<?php _e( 'Upload file and import' ); ?>" />
					</form>
				</p>
	
			</div>
		</div>

	</div>

</div>

<script>
jQuery(document).ready(function($) {

	$('.nav-tab-wrapper a').on('click', function( ev ) {
		
		$('.nav-tab-wrapper a').each(function ( i , el ) {
			$(el).removeClass('nav-tab-active');
		});
		
		$(ev.target).addClass('nav-tab-active');
		
		var user_role = $(ev.target).prop('title');

		$('.sohc_list_table').hide();
		$('.sohc_list_table#' + user_role + '_lists').show();
		
		return false;

	});
	
	$('.sohc_list_table table thead th input[name=all_check], .sohc_list_table table tfoot th input[name=all_check]').on('click', function( ev ) {
		
		var check_val = $(ev.target).val();
		var checked = $(ev.target).prop('checked');
		
		var $Table = $(ev.target).parent().parent().parent().parent().parent();
		
		$Table.find('thead th input[value=' + check_val + '], tfoot th input[value=' + check_val + ']').prop('checked' , checked);
		
		$Table.find('tbody tr:not(.parent_screen)').each( function( tr_key , tr_el ) {
			
			$(tr_el).find('td input[value=' + check_val + ']').prop('checked', checked);

		});
		
	});
	
});
</script>
