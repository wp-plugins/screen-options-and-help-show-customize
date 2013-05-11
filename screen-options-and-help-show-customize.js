jQuery(document).ready(function($) {

	var $Form = $("#sohc_form");

	$('.handlediv, h3' , $Form).click(function() {
		$(this).parent().toggleClass('closed');
	});

	$('input[name=all_checked]' , $Form).click(function() {

		var $Checked = $(this).is(':checked');
		var $Type = $(this).attr("class");
		var $Screen_id = $(this).attr("title");
		var $Table = $(this).parent().parent().parent().parent().parent();

		$Table.children("tbody").children("tr").each(function() {
			checkbox_id = $("input[type=checkbox][value=" + $Type + "][name*=" + $Screen_id + "]" , $(this) ).attr( "checked" , $Checked );
		});

	});

});
