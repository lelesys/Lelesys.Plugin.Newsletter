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
	jQuery('.sendEmail').each(function() {
		jQuery(this).validate();
	});
	//Validation for News form
	jQuery('#recipientList').focusout(function() {
		var email = jQuery('#recipientList').val(), emailRegex = new RegExp(/(([a-zA-Z0-9\-?\.?]+)@(([a-zA-Z0-9\-_]+\.)+)([a-z]{2,3})(\W?[,]\W?(?!$))?)+$/i);
		if (!jQuery(this).val()) {
			if (jQuery(this).next('.error').length < 1) {
			}
			return false;
		} else {
			jQuery(this).next('.error').remove();
		}
		if (emailRegex.test(email)) {
			// Check for white space
			var str = email;
			var reWhiteSpace = /\s/g;
			var result = str.match(reWhiteSpace);
			if (result) {
				jQuery(this).after('<div class="neos-alert neos-alert-error form-error error"><button type="button" class="neos-close" data-dismiss="alert">×</button>' + whitespaceCheck + '</div>');
				return false;
			} else {
				jQuery(this).next('.error').remove();
			}
		} else {
			if (jQuery(this).next('.error').length < 1) {
				jQuery(this).after('<div class="neos-alert neos-alert-error form-error error"><button type="button" class="neos-close" data-dismiss="alert">×</button>' + validEmail + '</div>');
			}
			return false;
		}
	});
	//Validation for News form
	jQuery('.createGroupStatic').click(function() {
		var email = jQuery('#recipientList').val(), emailRegex = new RegExp(/(([a-zA-Z0-9\-?\.?]+)@(([a-zA-Z0-9\-_]+\.)+)([a-z]{2,3})(\W?[,]\W?(?!$))?)+$/i);
		if (!jQuery(this).val()) {
			if (jQuery(this).next('.error').length < 1) {
			}
			return false;
		} else {
			jQuery(this).next('.error').remove();
		}
		if (emailRegex.test(email)) {
			// Check for white space
			var str = email;
			var reWhiteSpace = /\s/g;
			var result = str.match(reWhiteSpace);
			if (result) {
				jQuery(".checkvalid").css('display', 'block');
				jQuery(".checkvalid").html('<button data-dismiss="alert" class="neos-close" type="button">×</button>' + whitespaceCheck + '');
				return false;
			} else {
				jQuery(this).next('.error').remove();
			}
		} else {
			if (jQuery(this).next('.error').length < 1) {
				jQuery(".checkvalid").css('display', 'block');
				jQuery(".checkvalid").html('<button data-dismiss="alert" class="neos-close" type="button">×</button>' + validEmail + '');
			}
			return false;
		}
	});
});