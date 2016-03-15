<?php

/**
 * Makes it easy to create and edit your own functionality plugin
 * for pasting snippets instead of in the theme's functions.php
 *
 * To minimize confusion, throughout this file when I refer to
 * 'functionality plugin', I mean the functions.php file that is
 * created by this plugin in the WordPress plugins folder.
 * When I refer to 'this plugin', I'm talking about the plugin
 * whose code you're currently looking at.
 *
 * @version   1.1.1
 * @author    Shea Bunge <info@bungeshea.com>
 * @copyright Copyright (c) 2013-15, Shea Bunge
 * @license   https://opensource.org/licenses/MIT
 */

/*
Plugin Name: Functionality
Plugin URI: https://github.com/sheabunge/functionality
Description: Makes it easy to create and edit your own functionality plugin for pasting snippets instead of in the theme's functions.php
Author: Shea Bunge
Author URI: https://bungeshea.com
Version: 1.1.1
License: MIT
License URI: https://opensource.org/licenses/MIT
Text Domain: functionality
Domain Path: /languages
*/

/**
 * Create an instance of the class
 *
 * @since 1.0
 * @uses apply_filters() to allow changing of the filename without hacking
 * @return Functionality_Plugin
 */
function get_functionality_plugin() {
	static $plugin;

	if ( ! isset( $plugin ) ) {
		require_once dirname( __FILE__ ) . '/class-functionality-plugin.php';
		$filename = apply_filters( 'functionality_plugin_filename', 'functions.php' );
		$plugin = new Functionality_Plugin( $filename );
	}

	return $plugin;
}

/**
 * Add a link to edit the functionality plugin
 * to the Plugins admin menu for easy access
 *
 * @since 1.0
 * @uses add_plugins_page() To register the new submenu page
 */
function functionality_plugin_admin_menu() {
	$functionality = get_functionality_plugin();
	$plugin_file = $functionality->get_plugin_filename();

	add_plugins_page(
		__( 'Edit Functions', 'functionality' ),
		__( 'Edit Functions', 'functionality' ),
		'edit_plugins',
		add_query_arg( 'file', $plugin_file, 'plugin-editor.php' )
	);
}

add_action( 'admin_menu', 'functionality_plugin_admin_menu' );

/**
 * Callback runs when this plugin is activated
 *
 * @since 1.1
 */
function functionality_plugin_activate() {
	add_option( 'functionality_plugin_activated', true );
}

register_activation_hook( __FILE__, 'functionality_plugin_activate' );

/**
 * Create and activate the functionality plugin
 * after this plugin is activated
 *
 * @since 1.1
 * @uses activate_plugin() to activate the functionality plugin
 */
function create_functionality_plugin() {

	/* Check if this plugin has just been activated */
	if ( ! get_option( 'functionality_plugin_activated', false ) ) {
		return;
	}

	delete_option( 'functionality_plugin_activated' );

	$functionality = get_functionality_plugin();

	/* Create the plugin */
	$functionality->create_plugin();

	/* Activate the plugin */
	$plugin = $functionality->get_plugin_filename();

	if ( ! function_exists( 'activate_plugin' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	activate_plugin( $plugin );
}

add_action( 'init', 'create_functionality_plugin' );

/**
 * Load the plugin textdomain
 *
 * @since 1.1
 */
function load_functionality_textdomain() {
	load_plugin_textdomain( 'functionality', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'load_functionality_textdomain' );
