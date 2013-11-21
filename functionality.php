<?php

/*
 * Plugin Name: Functionality
 * Plugin URI: https://github.com/bungeshea/functionality
 * Description: Makes it easy to create and edit your own functionality plugin for pasting snippets instead of in the theme's functions.php
 * Author: Shea Bunge
 * Author URI: http://bungeshea.com
 * Version: 1.1
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: functionality
 * Domain Path: /languages
 */

/**
 * Makes it easy to create and edit your own functionality plugin
 * for pasting snippets instead of in the theme's functions.php
 *
 * To minimize confusion, throughout this file when I refer to
 * 'functionality plugin', I mean the functions.php file that is
 * created by this plugin in your plugins folder. When I refer to
 * 'this plugin', I'm talking about, you guessed it, this plugin
 *
 * @version   1.1
 * @author    Shea Bunge <info@bungeshea.com>
 * @copyright Copyright (c) 2013, Shea Bunge
 * @license   http://opensource.org/licenses/MIT
 */

/**
 * Create an instance of the class
 *
 * @return void
 * @uses   apply_filters() To allow changing of the filename without hacking
 * @since  1.0
 * @access public
 */
function functionality_plugin_init() {
	if ( ! isset( $GLOBALS['functionality_plugin_controller'] ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'class-functionality-plugin.php';
		$filename = apply_filters( 'functionality_plugin_filename', 'functions.php' );
		$GLOBALS['functionality_plugin_controller'] = new Functionality_Plugin( $filename );
	}
}

add_action( 'plugins_loaded', 'functionality_plugin_init' );

/**
 * Add a link to edit the functionality plugin
 * to the Plugins admin menu for easy access
 *
 * @uses   add_plugins_page() To register the new submenu page
 * @return void
 * @since  1.0
 * @access public
 */
function functionality_plugin_admin_menu() {
	$plugin_file = $GLOBALS['functionality_plugin_controller']->get_plugin_filename();

	add_plugins_page(
		__( 'Edit Functions', 'functionality' ),
		__( 'Edit Functions', 'functionality' ),
		'edit_plugins',
		add_query_arg( 'file', $plugin_file, 'plugin-editor.php' )
	);
}

add_action( 'admin_menu', 'functionality_plugin_admin_menu' );

/**
 * Create the functionality plugin when this plugin is activated
 *
 * @return void
 * @since  1.0
 * @access public
 */
function create_functionality_plugin() {
	functionality_plugin_init();
	$GLOBALS['functionality_plugin_controller']->create_plugin();
}

register_activation_hook( __FILE__, 'create_functionality_plugin' );

/**
 * Load the plugin textdomain
 *
 * @return void
 * @since  1.1
 * @access public
 */
function load_functionality_textdomain() {
	load_plugin_textdomain( 'functionality', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'load_functionality_textdomain' );
