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
	jQuery('#newsletterCategory').each(function() {
		jQuery(this).validate();
	});
	jQuery('.sendEmail').each(function() {
		jQuery(this).validate();
	});

		//Validation for item delete before submit
	var removeItem = function(event, link) {
		event.preventDefault();
		jQuery('.delete').attr('action', link);
		jQuery('.delete').submit();
	}

	jQuery('form.delete').click(function(event) {
		event.preventDefault();
		var link = jQuery(this).attr('action');
		var itemDeleteMessage = confirm(deleteMessage);
		if (itemDeleteMessage == true) {
			removeItem(event, link);
		}
	});


	jQuery('#staticList input[type="submit"]').click(function(event) {
		if ((jQuery('#staticList #title').val() != '') && (jQuery('#staticList #recipientList').val() != '')) {
		var emails = jQuery('#recipientList').val().split(",");
		jQuery.each(emails, function(i) {
			var email = jQuery.trim(emails[i]);
			var valid = isValidEmailAddress(email);
			if (valid == 1) {
				jQuery('.email-validation').remove();
				if (jQuery('.image-error').length == 0) {
					jQuery('<label class="error email-validation">Please enter valid email address</label>').insertAfter('#recipientList');
				}
				event.preventDefault();
			}
		});
		} else {
	if (jQuery("#staticList").length > 0) {
		jQuery("#staticList").validate();
			//validations
		notEmptyValidation("#staticList .not-empty", 'Please enter this field');
	}
		}
	});

	jQuery('.add-more').click(function() {
		var inputLength = jQuery('.attachments').length;
		var inputVal = jQuery('.attachments:first').clone();
		jQuery(inputVal).find('input').each(function() {
			jQuery(this).attr('name', jQuery(this).attr('name').replace('0', inputLength));
			jQuery(this).val('');
			jQuery(inputVal).append('<a onclick="deleteAttachment()" class="delete" rel="tooltip" title="'+deleteAttachmentTitle+'"><i class="icon-trash"></i></a>');
		});
		jQuery(inputVal).insertAfter('.attachments:last');
	});
});

var notEmptyValidation = function(selector, message) {
	var errMessage;
	jQuery(selector).each(function() {
		var msg = jsTranslate(this, 'notEmpty');
		if (msg) {
			errMessage = msg;
		} else {
			errMessage = message;
		}
		jQuery(this).rules("add", {
			required: true,
			messages: {
				required: errMessage
			}
		});
	});
};

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    if (pattern.test(emailAddress) == false) {
		return 1;
	}
};
function deleteAttachment() {
	jQuery('.attachments:last').remove();
};
