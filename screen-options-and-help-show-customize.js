jQuery(document).ready(function($) {

	var $Form = $("#sohc");

	$('.handlediv' , $Form).click(function() {
		$(this).parent().toggleClass('closed');
	});

});
