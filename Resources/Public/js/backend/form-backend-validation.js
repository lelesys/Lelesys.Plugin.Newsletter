jQuery(document).ready(function() {
	//Validate newNewsletter
	jQuery('#newNewsletter').each(function() {
		jQuery(this).validate();
	});
	jQuery('#editNewsletter').each(function() {
		jQuery(this).validate();
	});
	jQuery('#newRecipient').each(function() {
		jQuery(this).validate();
	});
	jQuery('#party').each(function() {
		jQuery(this).validate();
	});
	jQuery('#staticList').each(function() {
		jQuery(this).validate();
	});
	jQuery('#newsletterCategory').each(function() {
		jQuery(this).validate();
	});
});