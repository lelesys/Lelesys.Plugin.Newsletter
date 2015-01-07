jQuery(document).ready(function() {
		function log( message ) {
			jQuery( "<div>" ).html( message ).prependTo( "#log" );
			jQuery( "#log" ).scrollTop( 0 );
		}
		var url = $(recipientUrl).attr("href");
		jQuery( "#category-recipient" ).autocomplete({
			source: url,
			minLength: 2,
			select: function( event, ui ) {
				if(jQuery('#'+ui.item.id).length <= 0 ) {
					log(ui.item.value + "<span class='icon-trash icon-white remove-recipient'></span><input id='"+ui.item.id+"' type='hidden' name='"+inputName+"' value='"+ui.item.id+"'/>");
					$('.remove-recipient').bind('click', function() {
						$(this).closest('div').remove();
					});
				}
				ui.item.value = "";
			}
		});
		$('.remove-recipient').bind('click', function() {
			$(this).closest('div').remove();
		});
		$('#category-recipient').attr('type','text');
		$('#category-recipient').attr("placeholder","Enter recipient..");
});
