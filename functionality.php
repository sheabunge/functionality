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
 * Enable autoloading of plugin classes
 * @param $class_name
 */
function functionality_autoload( $class_name ) {

	/* Only autoload classes from this plugin */
	if ( 'Functionality' !== substr( $class_name, 0, 13 ) ) {
		return;
	}

	/* Remove namespace from class name */
	$class_file = str_replace( 'Functionality_', '', $class_name );

	/* Convert class name format to file name format */
	$class_file = strtolower( $class_file );
	$class_file = str_replace( '_', '-', $class_file );


	/* Load the class */
	require_once dirname( __FILE__ ) . "/php/class-{$class_file}.php";
}

spl_autoload_register( 'functionality_autoload' );


/**
 * Create an instance of the class
 *
 * @since 1.0
 * @uses apply_filters() to allow changing of the filename without hacking
 * @return Functionality_Controller
 */
function functionality() {
	static $controller;

	if ( ! isset( $controller ) ) {
		$controller = new Functionality_Controller();
	}

	return $controller;
}

functionality()->run();
