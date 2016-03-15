=== Functionality ===
Contributors: bungeshea
Donate link: https://bungeshea.com/donate/
Tags: functionality, functions.php
Tested up to: 4.4.2
Stable tag: 1.1.1
License: MIT
License URI: https://opensource.org/licenses/MIT

Creates a functionality plugin where you can add your functions.php code snippets.

== Description ==

A functionality plugin is a way to separate what you might normally place in a theme's `functions.php` file, and put it in a plugin instead. It works the same way as a theme `functions.php` file, but is separate from the theme and so not affected by theme upgrades, or tied to the theme so you loose all of your functions if you choose to switch themes.

This plugin automates the process of creating a functionality plugin. Simply install and activate this plugin, and your very own functionality plugin will be created for you. You can then edit your functionality plugin and add snippets to it using the quick link in the admin menu.

See [this WP Daily post](http://wpdaily.co/functionality-plugin/) for more information on functionality plugins.

This plugin's code is [available on GitHub](https://github.com/sheabunge/functionality). Please feel free to fork the repository and send a pull request. If you find a bug in the plugin, open an issue.

Serbian translation provided by [Ogi Djuraskovic from FirstSiteGuide.com](http://firstsiteguide.com).

== Installation ==

1. Upload the `functionality` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. A functionality plugin will be created for you in `wp-content/plugins/functions.php`
4. Use the built-in WordPress file editor to edit your functionality plugin

This plugin may be removed at any time, and your functionality plugin will remain working and intact. You will, however, loose the quick edit link in the admin menu.

== Frequently Asked Questions ==

= My functionality plugin isn't working! =
First of all, backup all of the code in your functionality plugin. Then deactivate both this plugin and your functionality plugin from the WordPress admin. Delete your functionality plugin (it will be called your site's name), and then reactivate this plugin.

= Can I get rid of this plugin once my functionality plugin has been created? =
With pleasure! Once this plugin has been activated and the functionality plugin created, all it does is create a link in the WordPress admin menu for easily editing the functionality plugin. If you don't want this, feel free to delete this plugin.

= When would I use this plugin over the Code Snippets plugin? =
This plugin is more suited to people who only have a few snippets and prefer editing a file to using a graphical interface. If you have a few snippets and like to be organized, you might feel more at home using the [Code Snippets](http://wordpress.org/plugins/code-snippets) plugin, which is also created by me.

= Isn't this just like the Pluginception plugin? =
Yes, in that they are both a plugin for creating plugins. However, this plugin is a bit more easier to use and specific than Pluginception.

== Screenshots ==
1. Editing the functionality plugin in the WordPress plugin editor

== Changelog ==

= 1.1.1 =
* Added Serbian translation thanks to [Ogi Djuraskovic from FirstSiteGuide.com](http://firstsiteguide.com)

= 1.1 =
* Fixed a spelling error when initializing the class
* Only initialize the class on plugin activation
* Move class to separate file
* Used a more reliable method of functionality plugin activation

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.1 =
Fixed all errors
