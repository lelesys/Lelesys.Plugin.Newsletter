Lelesys Plugin Newsletter
=========================

This plugin adds Newsletter to your websites.

Warning: This plugin is experimental.

Quick start
-----------

* Include the plugin's TypoScript definitions to your own one's (located in, for example, `Packages/Sites/Your.Site/Resources/Private/TypoScripts/Library/ContentElements.ts2`) with:

```
include: resource://Lelesys.Plugin.Newsletter/Private/TypoScript/Root.ts2
```



* Add the following code in settings of your package, Newsletter plugin sender email configuration:

```
	Lelesys:
	  Plugin:
		Newsletter:
		  email:
			admin: 'no-reply@yourdomain'
			subject: 'Confirm Subscription'
			# You can add your email address as per requirement
			replyTo: ''
			senderEmail: 'no-reply@yourdomain'
			senderName: 'Your Organization name'
			# These are optional. You can add your email addresses as per requirement
			ccAddresses: ''
			bccAddresses: ''
			baseUrl : 'Base url of your site(e.g. http://demosite.local.com')
          flashMessage:
            packageKey: 'Lelesys.Plugin.Newsletter'
```

* Create required database tables by running doctrine migrate command:

```
./flow doctrine:migrate
```

Usage
-----
* add the plugin content element "Newsletter Subscription Form" to the position of your choice.

* this will display form in frontend

* add the plugin content element "Newsletter Subscribtion Confirmation" at site node (the first page of your site) for confirmation of subscribtion  and to view confirmation message of newsletter .

* add the plugin content element "Lelesys Newsletter Node" to the position of your choice.

* this will display content element with plus icon to create new newsletter node.
The new node will be created under the page. Enter the text to be sent in the newsletter email
* All role Lelesys.Plugin.Newsletter:NewsletterAdmin so that administrator can see all backend modules related to newsletter.

***Note:-**
	*	Configure cron for command './flow newsletter:sendout' of newsletter plugin
		-The command './flow newsletter:sendout' will fire through cron
		 It will send news letters to subscribers