<?php

/**
 * A class to create a functionality plugin
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
