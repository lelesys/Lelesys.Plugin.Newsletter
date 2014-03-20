jQuery(document).ready(function() {
		//Newsletter Subscription form validation
	jQuery("#newRecipient").validate({
		// Specify the validation rules
		rules: {
			'--lelesys_plugin_newsletter-subscribtion[newPerson][name][firstName]': {
				required: true
			},
			'--lelesys_plugin_newsletter-subscribtion[newPerson][name][lastName]': {
				required: true
			},
			'--lelesys_plugin_newsletter-subscribtion[newPerson][primaryElectronicAddress][identifier]': {
				required: true,
				email: true
			},
			'--lelesys_plugin_newsletter-subscribtion[newPerson][gender]': {
				required: true
			}
		},
			// Specify the validation error messages
		messages: {
			'--lelesys_plugin_newsletter-subscribtion[newPerson][name][firstName]': {
				required: "This field is required"
			},
			'--lelesys_plugin_newsletter-subscribtion[newPerson][name][lastName]': {
				required: "This field is required"
			},
			'--lelesys_plugin_newsletter-subscribtion[newPerson][primaryElectronicAddress][identifier]': {
				required: "This field is required",
				email: "Please specify valid email address"
			},
			'--lelesys_plugin_newsletter-subscribtion[newPerson][gender]': {
				required: "This field is required"
			}
		},
		submitHandler: function(form) {
			form.submit();
		}
	});
});


