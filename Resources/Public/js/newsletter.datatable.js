jQuery(document).ready(function($) {
	if (jQuery('#newsletter-dataTable').length > 0) {
		var oTable;
			// Add the events etc before DataTables hides a column
		jQuery("thead input").keyup(function() {
			//Filter on the column (the index) of this element
			oTable.fnFilter(this.value, oTable.oApi._fnVisibleToColumnIndex(
					oTable.fnSettings(), $("thead input").index(this)));
		});

		oTable = jQuery('#newsletter-dataTable').dataTable({
			"fnDrawCallback": function(oSettings) {
				if (jQuery('#newsletter-dataTable tr').length < 10) {
					jQuery('.dataTables_length').hide();
				} else {
					jQuery('.dataTables_length').show();
					jQuery('.neos-controls create-button').removeClass("create-button");
				}
			}
		});
	}
});