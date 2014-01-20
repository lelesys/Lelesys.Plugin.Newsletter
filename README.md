Lelesys Plugin Newsletter
=========================

This plugin adds Newsletter to your websites.

Warning: This plugin is experimental.

Quick start
-----------

* include the plugin's TypoScript definitions to your own one's (located in, for example, `Packages/Sites/Your.Site/Resources/Private/TypoScripts/Library/ContentElements.ts2`) with:

```
include: resource://Lelesys.Plugin.Newsletter/Private/TypoScripts/Library/NodeTypes.ts2
```



* Add the following code in settings of your package, Newsletter plugin sender email configuration:

```
	Lelesys:
	  Plugin:
		Newsletter:
		  email:
            admin: 'info@lelesys.com'
            subject: 'Confirm Subscription'
            replyTo: 'gauri.shirodkar@lelesys.com'
            senderEmail: 'info@lelesys.com'
            senderName: 'Lelesys'
          flashMessage:
            packageKey: 'Lelesys.Plugin.Newsletter'
```

Usage
-----
* add the plugin content element "Newsletter Subscription Form" to the position of your choice.

* this will display form in frontend

* add the plugin content element "Lelesys Newsletter Node" to the position of your choice.

* this will display content element with plus icon to create new newsletter node.
The new node will be created under the page. Enter the text to be sent in the newsletter email

* All role Lelesys.Plugin.Newsletter:NewsletterAdmin so that administrator can see all backend modules related to newsletter.
