jQuery(document).ready(function($) {
	if (jQuery('#paid-course-subscription').length > 0) {
		var oTable;
			// Add the events etc before DataTables hides a column
		jQuery("thead input").keyup(function() {
			//Filter on the column (the index) of this element
			oTable.fnFilter(this.value, oTable.oApi._fnVisibleToColumnIndex(
					oTable.fnSettings(), $("thead input").index(this)));
		});

		oTable = jQuery('#paid-course-subscription').dataTable({
			"fnDrawCallback": function(oSettings) {
				if (jQuery('#paid-course-subscription tr').length < 11) {
					jQuery('.dataTables_paginate').hide();
					jQuery('.dataTables_length').hide();
				} else {
					jQuery('.dataTables_paginate').show();
					jQuery('.dataTables_length').show();
					jQuery('.neos-controls create-button').removeClass("create-button");
				}
			}
		});
	}
});