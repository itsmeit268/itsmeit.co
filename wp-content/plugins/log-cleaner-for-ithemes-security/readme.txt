=== Log cleaner for iThemes Security ===
Contributors:       mikeyott
Tags:               ithemes, security, log, cleaner
Requires at least:  4.9
Tested up to:       6.2
Stable tag:         1.3.6

Restores the ability to manually delete iThemes Security logs from the database.

== Description ==

In early 2018, iThemes removed the ability to manually delete the database logs (<a href="https://wordpress.org/support/topic/latest-update-missing-a-button-to-clear-logs/">see this thread</a>). This plugin gives you that control back.

== Installation ==

Install, activate, done.

== Support ==

<a href="https://wordpress.org/support/plugin/log-cleaner-for-ithemes-security">Log cleaner for iThemes Security Support</a> at the official Wordpress repository.

== How to use ==

* Go to <strong>Tools</strong> -> <strong>ITSec Log Cleaner</strong>
* Select which logs to delete (or select 'All')
* Hit the <strong>Clear logs</strong> button
* Get on with the rest of your day (optional)

Note: This plugin comes with no warranty of any kind.

== Uninstall ==

Deactivate the plugin, delete if desired.

== Changelog ==

= 1.3.6 =

* Fix: Issue where event logs could not be deleted.

= 1.3.5 =

* Removed broken sidebar.

= 1.3.4 =

* Removed handling of deprecated itsec_log table.

= 1.3.3 =

* Changed text domain to match plugin slug.

= 1.3.2 =

* Added option to clear the itsec_distributed_storage table.

= 1.3.1 =

* Removed source map reference that was causing 404 errors to be logged in iThemes Security.

= 1.3 =

* Fixed 'table x doesn’t exist' error.
* Removed check for iThemes plugin activation.
* Minor presentation tweaks.

= 1.2.1 =

* Minor, non-important UI tweak.

= 1.2 =

* Corrected older reference to ITSec Log Cleaner location.
* Fixed CSS cache issue.

= 1.1 =

* Spanish translation update.

= 1.0.9 =

* Hide the warning message when the logs are clear.
* Fixed minor responsive issue.

= 1.0.8 =

* Added support for deleting dashboard events (iThemes Security Pro).
* UI reduction.
* Sidebar promoting my other WordPress tools.

= 1.0.7 =

* Spanish translation (translation by @borrockalari - thanks!).

= 1.0.6 =

* Changed menu item label (suggested by @jetxpert - thanks!).
* Removed individual links to the logs, replaced with single link to log page.
* Language updates.

= 1.0.5 =

* Tested on WordPress 5.0.2.

= 1.0.4 =

* Added multisite support.

= 1.0.3 =

* Prevent direct access to plugin file.

= 1.0.2 =

* Updated readme.

= 1.0.1 =

* Updated readme.

= 1.0 =

* Initial release.