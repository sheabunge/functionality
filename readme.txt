=== Functionality ===
Contributors: bungeshea
Donate link: http://bungeshea.com/donate/
Tags: functionality, functions.php
Tested up to: 3.6
Stable tag: 1.1
License: MIT
License URI: http://opensource.org/licenses/MIT

Creates a functionality plugin where you can add your functions.php code snippets.

== Description ==

A functionality plugin is a way to separate what you might normally place in a theme's `functions.php` file, and put it in a plugin instead. It works the same way as a theme `functions.php` file, but is separate from the theme and so not affected by theme upgrades, or tied to the theme so you loose all of your functions if you choose to switch themes.

This plugin automates the process of creating a functionality plugin. Simply install and activate this plugin, and your very own functionality plugin will be created for you. You can then edit your functionality plugin and add snippets to it using the quick link in the admin menu.

See [this WP Daily post](http://wpdaily.co/functionality-plugin/) for more information on functionality plugins.

This plugin's code is [available on GitHub](https://github.com/bungeshea/functionality). Please feel free to fork the repository and send a pull request. If you find a bug in the plugin, open an issue.

== Installation ==

1. Upload `functionality.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. A functionality plugin will be created for you in `wp-content/plugins/functions.php`
4. Use the built-in WordPress file editor to edit your functionality plugin

This plugin may be removed at any time, and your functionality plugin will remain working and intact. You will, however, loose the quick edit link in the admin menu.

== Changelog ==

= 1.1 =
* Fixed a spelling error when initializing the class
* Only initialize the class on plugin activation

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.1 =
Fixed all errors
