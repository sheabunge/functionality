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

class Functionality_Plugin {

	/**
	 * The filename of the functionality plugin
	 *
	 * @var    string
	 * @since  1.0
	 * @access protected
	 */
	protected $plugin_filename = '';

	/**
	 * The filesystem location of the plugin file
	 *
	 * @var    string
	 * @since  1.0
	 * @access protected
	 */
	protected $plugin_location = '';

	/**
	 * Constructor
	 *
	 * @param  string $plugin_filename The filename of the functionality plugin
	 * @param  string $plugin_location The filesystem location of the plugin file
	 *
	 * @since  1.0
	 * @access public
	 */
	public function __construct( $plugin_filename, $plugin_location = WP_PLUGIN_DIR ) {
		$this->set_plugin_filename( $plugin_filename );
		$this->set_plugin_location( $plugin_location );
	}

	/**
	 * Retrieve the filename of the functionality plugin
	 *
	 * @return string
	 * @since  1.0
	 * @access public
	 */
	public function get_plugin_filename() {
		return $this->plugin_filename;
	}

	/**
	 * Set the filename of the functionality plugin.
	 * Note that this only changes the variable, it does not to any renaming
	 *
	 * @param  string $filename
	 * @return void
	 *
	 * @since  1.0
	 * @access public
	 */
	public function set_plugin_filename( $filename ) {
		$this->plugin_filename = sanitize_file_name( $filename );
	}

	/**
	 * Retrieve the filesystem location of the functionality plugin
	 *
	 * @return string
	 * @since  1.0
	 * @access public
	 */
	public function get_plugin_location() {
		return $this->plugin_location;
	}

	/**
	 * Set the filesystem location of the functionality plugin.
	 * Note that this only changes the variable, it does not to any moving
	 *
	 * @param  string $dir
	 * @return void
	 *
	 * @since  1.0
	 * @access public
	 */
	public function set_plugin_location( $dir ) {
		$this->plugin_location = trailingslashit( $dir );
	}


	/**
	 * Build the header comment for the plugin
	 *
	 * @param  array $plugin_header The headers for the plugin
	 * @return string
	 *
	 * @since  1.0
	 * @access public
	 */
	public function get_plugin_header( $plugin_header = array() ) {
		$plugin = $this->plugin_location . $this->plugin_filename;

		get_currentuserinfo();

		$default_plugin_header = array(
			'Plugin Name' => get_bloginfo( 'name' ),
			'Plugin URI'  => home_url(),
			'Description' => sprintf ( __( "A site-specific functionality plugin for %s where you can paste your code snippets instead of using the theme's functions.php file", 'functionality' ), get_bloginfo( 'name' ) ),
			'Author'      => $user_identity,
			'Author URI'  => $user_url,
			'Version'     => date( 'Y.m.d' ),
			'License'     => 'GPL',
		);

		$plugin_header = wp_parse_args( $plugin_header, $default_plugin_header );
		$plugin_header = apply_filters( 'functionality_plugin_header', $plugin_header, $plugin );

		/* Start the header comment */
		$plugin_header_comment = "/*\n";

		foreach ( $plugin_header as $i => $v ) {

			/* Add the headers to the comment */
			$plugin_header_comment .= "{$i}: {$v}\n";
		}

		/* Finish it off by closing the comment and adding a new line */
		$plugin_header_comment .= "*/\n";

		return apply_filters( 'functionality_plugin_header_comment', $plugin_header_comment, $plugin );
	}

	/**
	 * Create the functions.php plugin file in the plugin
	 * directory if it does not already exist
	 *
	 * @param  boolean $activate Activate the plugin after creation?
	 * @return void
	 *
	 * @since  1.0
	 * @access public
	 */
	public function create_plugin( $activate = true ) {
		$file = $this->plugin_location . $this->plugin_filename;

		/* Bail early if the file already exists */
		if ( file_exists( $file ) )
			return;

		/* Create the file */
		touch( $file );

		/* Open the file for writing */
		if ( null != ( $handle = @fopen( $file, 'w' ) ) ) {

			/* Attempt to write the contents of the string */
			if ( null != fwrite( $handle, $file_contents, strlen( $file_contents ) ) ) {

				/* Relinquish the resource */
				fclose( $template_handle );
			}
		}

		/* Activate the plugin after creation */
		if ( $activate ) {
			$this->activate_plugin();
		}
	}

	/**
	 * Activate the functionality plugin
	 *
	 * @return void
	 * @since  1.0
	 * @access public
	 */
	public function activate_plugin() {
		$current = get_option( 'active_plugins' );
		$plugin = plugin_basename( trim( $this->plugin_filename ) );

		if ( ! in_array( $plugin, $current ) ) {
			$current[] = $plugin;
			sort( $current );
			do_action( 'activate_plugin', $plugin );
			update_option( 'active_plugins', $current );
			do_action( 'activate_' . $plugin );
			do_action( 'activated_plugin', $plugin );
		}
	}
}

/**
 * Create an instance of the class
 *
 * @return void
 * @uses   apply_filters() To allow changing of the filename without hacking
 * @since  1.0
 * @access public
 */
function functionality_plugin_init() {
	$filename = apply_filters( 'functionality_plugin_filename', 'functions.php' );
	$GLOBALS['functionality_plugin_controller'] = new Functionality_Plugin( $filename );
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
