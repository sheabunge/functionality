<?php

/**
 * A class to create a functionality plugin
 */
class Functionality_Plugin {

	/**
	 * The filename of the functionality plugin
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $plugin_filename = '';

	/**
	 * The filesystem location of the plugin file
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $plugin_location = '';

	/**
	 * Constructor
	 *
	 * @since 1.0
	 *
	 * @param string $plugin_filename The filename of the functionality plugin
	 * @param string $plugin_location The filesystem location of the plugin file
	 */
	public function __construct( $plugin_filename, $plugin_location = WP_PLUGIN_DIR ) {
		$this->set_plugin_filename( $plugin_filename );
		$this->set_plugin_location( $plugin_location );
	}

	/**
	 * Retrieve the filename of the functionality plugin
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_plugin_filename() {
		return $this->plugin_filename;
	}

	/**
	 * Set the filename of the functionality plugin.
	 * Note that this only changes the variable, it does not to any renaming
	 *
	 * @since 1.0
	 * @param string $filename
	 */
	public function set_plugin_filename( $filename ) {
		$this->plugin_filename = sanitize_file_name( $filename );
	}

	/**
	 * Retrieve the filesystem location of the functionality plugin
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_plugin_location() {
		return $this->plugin_location;
	}

	/**
	 * Set the filesystem location of the functionality plugin.
	 * Note that this only changes the variable, it does not to any moving
	 *
	 * @since 1.0
	 * @param string $dir
	 */
	public function set_plugin_location( $dir ) {
		$this->plugin_location = trailingslashit( $dir );
	}


	/**
	 * Build the header comment for the plugin
	 *
	 * @since  1.0
	 *
	 * @param  array  $plugin_header The headers for the plugin
	 * @return string
	 */
	public function get_plugin_header( $plugin_header = array() ) {

		/* Fetch the current user information */
		global $user_identity, $user_url;
		get_currentuserinfo();

		/* Build the plugin header */
		$plugin = $this->plugin_location . $this->plugin_filename;

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
	 * @since 1.0
	 */
	public function create_plugin() {
		$file = $this->plugin_location . $this->plugin_filename;

		/* Bail early if the file already exists */
		if ( file_exists( $file ) ) {
			return;
		}

		/* Create the plugin file contents */
		$file_contents = "<?php\n\n" . $this->get_plugin_header() . "\n";

		/* Open the file for writing, implicitly creating a file */
		if ( null != ( $handle = @fopen( $file, 'w' ) ) ) {

			/* Attempt to write the contents of the string */
			if ( null != fwrite( $handle, $file_contents, strlen( $file_contents ) ) ) {

				/* Relinquish the resource */
				fclose( $handle );
			}
		}

		do_action( 'functionality_plugin_created', $file );
	}
}
