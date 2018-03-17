<?php

/**
 * Makes it easy to create and edit your own functionality plugin
 * for pasting snippets instead of in the theme's functions.php
 *
 * @version   2.0.0
 * @author    Shea Bunge <shea@bungeshea.com>
 * @copyright Copyright (c) 2013-2018, Shea Bunge
 * @license   https://opensource.org/licenses/MIT
 */

/*
Plugin Name: Functionality
Plugin URI:  https://github.com/sheabunge/functionality
Description: Makes it easy to create and edit your own functionality plugin for pasting snippets instead of in the theme's functions.php
Author:      Shea Bunge
Author URI:  https://bungeshea.com
Version:     2.0.0
License:     MIT
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
 * @return Functionality_Controller
 */
function functionality() {
	static $controller;

	if ( ! isset( $controller ) ) {
		$controller = new Functionality_Controller( __FILE__ );
	}

	return $controller;
}

add_action( 'plugins_loaded', array( functionality(), 'load' ) );
