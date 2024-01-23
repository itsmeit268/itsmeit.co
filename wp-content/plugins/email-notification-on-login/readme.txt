=== Email Notification on Login ===
Contributors: apasionados
Donate link: https://apasionados.es/
Author URI: https://apasionados.es/
Tags: login notification, admin login notification, email notification, email notify on admin login, email notify on login
Requires at least: 4.0.1
Tested up to: 6.1
Requires PHP: 5.5
Stable tag: 1.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Receive an email after each successful login with the user information

== Description ==

This plugin sends an email to the WordPress System email (Settings / General / Email Address) or any other configured email address each time somebody logs into WordPress. This is handy if there are not many logins each day or week to keep track of all of them and being able to detect non authorized logins.

The email contains the username, the user id and the user role (administrator, editor, author or contributor). Other data included is the Date & Time and the IP Address.

This plugin is an enhanced version of the plugin [Email notification on admin login](https://wordpress.org/plugins/email-notification-on-admin-login/), that tracks all users, includes user role, translations and the lookup of the country of the IP from which the form is sent.

We created this enhanced version of the plugin, because we needed a translation to Spanish and wanted to know the country from which the uses send the contact form, whithout having to lookup the IP address.

In order to display the Country it needs the [Geolocation IP Detection (until March 2020: GeoIP Detection) plugin](https://wordpress.org/plugins/geoip-detect/) that can be found in the WordPress plugin repository. This plugin "provides geographic information detected by an IP adress". *This plugin auto-updates the GeoIP database once a week. This product includes GeoLite data created by MaxMind, available from [www.maxmind.com](http://www.maxmind.com).*

If the [Geolocation IP Detection (until March 2020: GeoIP Detection) plugin](https://wordpress.org/plugins/geoip-detect/) is not installed and enabled, you will only see the IP address, without the country of the IP adress.

We decided to use the Geolocation IP Detection (until March 2020: GeoIP Detection) plugin to handle the lookup of the country, because it's a plugin that is actively developed and we saw no advantage in implementing all this functionality, when we could use an existing one.

= What can I do with this plugin? =

This plugin sends an email to the WordPress System email (Settings / General / Email Address) or any other configured email address each time somebody logs into WordPress. This is handy if there are not many logins each day or week to keep track of all of them and being able to detect non authorized logins.

= What ideas is this plugin based on? =

This plugin is an enhanced version of the plugin [Email notification on admin login](https://wordpress.org/plugins/email-notification-on-admin-login/), that tracks all users, includes user role, translations and the lookup of the country of the IP from which the form is sent.

= System requirements =

PHP version 5.5 or greater.

= Email notification on Login Plugin in your Language! =
This first release is avaliable in English and Spanish. In the "languages" folder we have included the necessarry files to translate this plugin.

If you would like the plugin in your language and you're good at translating, please drop us a line at [Contact us](https://apasionados.es/contacto/index.php?desde=wordpress-org-email-notification-on-login-home).

= Further Reading =
You can access the description of the plugin in Spanish at: [Email notification on Login en espa&ntilde;ol](https://apasionados.es/blog/enviar-email-cuando-alguien-se-loguea-wordpress-plugin-7762/).

== Screenshots ==

1. Plugin configuration options.

== Installation ==

1. First you will have to upload the plugin to the `/wp-content/plugins/` folder.
2. Then activate the plugin in the plugin panel.
3. Go to SETTINGS / Email Notification on Login.
4. Configure settings.

== Frequently Asked Questions ==

= Why did you make this plugin?  =

We created this plugin to be able to track succesfull logins to some of our WordPress installations with a very low volume of logins each month.

> We also wanted to know the country from which the uses send the contact form, whithout having to lookup the IP address + a spanish translation.

= Does Email Notification on Login make changes to the database? =
Yes. It creates one entry in the options table. This entry are deleted if you deactivate and uninstall the plugin. If you only deactivate, settings are kept.

= How can I check out if the plugin works for me? =
Install and activate. Log out of WordPress. Log in. You should receive an email with the login data.

If you don't receive the emails it can be cause of the email server. Please install the plugin [Email Log](https://wordpress.org/plugins/email-log/) where you can check if the emails are sent correctly by WordPress.

= Is there anything to take into consideration? =
If you don't receive the emails it can be cause of the email server. Please install the plugin [Email Log](https://wordpress.org/plugins/email-log/) where you can check if the emails are sent correctly by WordPress.

= How can I remove Email notification on Login? =
You can simply activate, deactivate or delete it in your plugin management section. If you delete the plugin through the management section the configuration is deleted (entries in options table are removed). If you delete the plugin through FTP the configuration is not deleted.

= Are there any known incompatibilities? =
Please don't use it with *WordPress MultiSite*, as it has not been tested.

= Is this plugin compatible with WPML =
Yes. We are running the plugin on several sites with WPML 3.7.x and 3.8.x.

= Which PHP version do I need? =
This plugin has been tested and works with PHP versions 5.5 and greater. WordPress itself [recommends using PHP version 7 or greater](https://wordpress.org/about/requirements/). If you're using a PHP version lower than 5.5 please upgrade your PHP version or contact your Server administrator.

= Are there any server requirements? =
Yes. The plugin requires a PHP version 5.5 or higher and we recommend using PHP version 7 or higher.

= Do you make use of Email notification on Login yourself? = 
Of course we do. That's why we created it. ;-)

== Changelog ==

= 1.6.1 (16/05/2022) =
* Updated language file names from apa-enol-xx_XX to email-notification-on-login-xx_XX to make the text domain match the plugin URL.

= 1.6 (16/05/2022) =
* Changed text-domain to match plugin slug.

= 1.5 (24/02/2022) =
* Made changes to session_start() to prevent a notification in the health widget about it and an error in Rest API because of this.

= 1.4.0 (02/12/2020) =
* Removed deprecated "screen_icon()"

= 1.3.0 (04/06/2020) =
* Added non-standard user roles to the notification email

= 1.2.0 (07/01/2020) =
* Corrected multiple emails sent on login when using WordPress 5.2+

= 1.1.0 (18/11/2018) =
* Added browser information to the email.

= 1.0.2 (14/09/2018) =
* Solved PHP 7.2 notice: "Undefined variable: trackingInfo on line 76"

= 1.0.1 (17/10/2017) =
* Replaced PHP date() function with WordPress date_i18n() to show the time and date according to the time zone settings of the WordPress and not UTC.

= 1.0.0 (15/10/2017) =
* First official release.

== Upgrade Notice ==

= 1.6.1 =
UPDATED: Changed text-domain to match plugin slug.

== Contact ==

For further information please send us an [email](https://apasionados.es/contacto/index.php?desde=wordpress-org-email-notification-on-login).