jQuery(document).ready(function($) {

	$('.sohc #postbox-container-1 .toggle-width').on('click', function( ev ) {
		
		var Action = 'sohc_donation_toggle';
		$('.sohc').toggleClass('full-width');

		if( $('.sohc').hasClass('full-width') ) {
			$.post(ajaxurl, {
				'action': Action,
				'f': 1,
			});
		} else {
			$.post(ajaxurl, {
				'action': Action,
				'f': 0,
			});
		}
		
		return false;

	});

});
