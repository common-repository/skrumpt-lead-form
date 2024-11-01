=== Skrumpt Lead Form ===
Contributors: michaelpanco
Donate link: https://crm.skrumpt.com
Tags: skrumpt, skrumpt lead, skrumpt lead form
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 0.1
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Skrumpt Lead Form is a plugin developed by Skrumpt CRM that helps you to collect all the leads coming from your visitor of your site. All the leads that has been submitted will automatically added to your Skrumpt account.

== Description ==

Skrumpt Lead Form is a plugin developed by Skrumpt CRM that helps you to collect all the leads coming from your visitor of your site. All the leads that has been submitted will automatically added to your Skrumpt account leads.

Skrumpt Lead Form Features

* Render a lead form in your site 
* You can use shortcode to easily add it in your wordpress site
* Skrumpt Lead Form setting is added automatically in your wordpress plugin

PS: You'll be prompted to get an skrumpt.com API key to make this plugin works.

== Installation ==

Upload the Skrumpt Lead Form plugin to your website, activate it, and then enter your Skrumpt API key provided by Skrumpt CRM. Put the the shortcode **[skrumpt_lead_form]** any where in your wordpress post/page, this creates a modal. To trigger the modal you need to have an input text and a button.


* You need to have an input text with an ID **skrumpt_input** (This input text handles the postcode)
* You need to have a button with an ID **skrumpt_search** (This button handle the submission of the postcode that the user entered)

if you have multiple postcode search you can use Class instead of ID

**IMPORTANT:** Without these elements, there is no trigger for form modal to show up.


== Frequently Asked Questions ==

= Where can I get the Skrumpt API Key? =

Go to Skrumpt CRM https://crm.skrumpt.com/account and click Generate New API Key

= I can't see Generate New API Key =

Only Investors Account are allowed to generate an API Key

= How do I add campaigns? =

You need to login to Skrumpt CRM website to add new campaigns.

= How do I use different campaigns if I have different form in each page? =

Our shortcode allows you to add a parameter to use a campaign. Please keep in mind that the campaign you set in Skrumpt Lead Form setting will be overriden by the campaign in shortcode.

e.g. [skrumpt_lead_form campaign=444] where the 444 is the campaign ID in your Skrumpt account.

== Screenshots ==
 
1. You will need to enter your API Key to fully work with the plugin
2. Once you've entered your API Key, Some of your account details will show up about your account and you're good to go.

== Changelog ==
 
= 1.0 =
* Initial Version

== Upgrade Notice ==
 
= 1.0 =
Enjoy the plugin

