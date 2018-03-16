<?php

/**
 * A class to create a functionality plugin
 */
class Functionality_File {

	/**
	 * The filename of the functionality plugin
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $filename = '';

	/**
	 * The filesystem location of the plugin file
	 *
	 * @var string
	 * @since 1.0
	 */
	protected $directory = '';

	/**
	 * Base location to store the created file in
	 * @var string
	 */
	protected $base_location;

	/**
	 * Constructor
	 *
	 * @since 1.0
	 *
	 * @param string $filename The filename of the functionality plugin
	 */
	public function __construct( $filename ) {
		$this->base_location = WP_PLUGIN_DIR;
		$this->directory = trailingslashit( apply_filters( 'functionality_plugin_directory', 'functions' ) );
		$this->set_filename( $filename );
	}

	/**
	 * Set the filename to use when creating the file
	 *
	 * @since 1.0
	 * @param string $filename
	 */
	public function set_filename( $filename ) {
		$this->filename = sanitize_file_name( $filename );
	}

	/**
	 * Retrieve the filename
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_filename() {
		return $this->filename;
	}

	/**
	 * Get the filename along with the relative directory
	 * @return string
	 */
	public function get_file() {
		return $this->directory . $this->get_filename();
	}

	/**
	 * Retrieve the filesystem location of the file
	 * @return string
	 */
	public function get_base_location() {
		return trailingslashit( $this->base_location );
	}

	/**
	 * Retrieve the full filesystem file path and filename
	 * @return string
	 */
	public function get_full_path() {
		return $this->get_base_location() . $this->get_file();
	}

	/**
	 * Retrieve the default content for the file
	 * @return string
	 */
	public function get_default_content() {
		return '';
	}

	/**
	 * Retrieve the administration URL to the plugin editor for this file
	 * @return string
	 */
	public function get_edit_url() {
		return add_query_arg( 'file', urlencode( $this->get_file() ), admin_url( 'plugin-editor.php' ) );
	}

	/**
	 * Load the WP Filesystem API
	 * @return bool
	 */
	private function load_wp_filesystem() {
		$url = wp_nonce_url( add_query_arg( 'page', $this->get_filename(), 'plugins.php' ), 'functionality' );
		$extra_fields = array(  'create_plugin' );

		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, $extra_fields ) ) ) {
			return true;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( $url, '', false, false, $extra_fields );
			return true;
		}

		return false;
	}

	/**
	 * Create file in the plugins directory if it does not already exist
	 *
	 * @return bool If the file creation was successful
	 *
	 * @since 1.0
	 */
	public function create_file() {
		$file = $this->get_full_path();

		/* Bail early if the file already exists */
		if ( file_exists( $file ) ) {
			return true;
		}

		/* Load the WP Filesystem API */
		$this->load_wp_filesystem();

		/** @var WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		/* Create the containing directory if it does not exist */
		$full_directory = $this->get_base_location() . $this->directory;

		if ( ! $wp_filesystem->exists( $full_directory ) ) {
			$wp_filesystem->mkdir( $full_directory );
		}

		/* Write the plugin file contents */
		$file_contents = $this->get_default_content();

		if ( ! $wp_filesystem->put_contents( $file, $file_contents, FS_CHMOD_FILE ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Add a link to edit this file to the Plugins admin menu for easy access
	 *
	 * @param string $label      Text to use for the menu label
	 *
	 * @return string hook name for the newly created page
	 */
	public function register_edit_menu( $label ) {

		$hook = add_plugins_page(
			$label, $label,
			'edit_plugins',
			'functionality-' . $this->get_filename(),
			array( $this, 'redirect_edit_menu' )
		);

		add_action( 'load-' . $hook, array( $this, 'redirect_edit_menu' ) );

		return $hook;
	}

	/**
	 * Redirect the user to edit the page after loading the edit menu
	 */
	public function redirect_edit_menu() {

		/* create the file if it does not exist */
		$this->create_file();

		$file = $this->get_file();
		$editor_url = $this->get_edit_url();

		if ( class_exists( 'WPEditor' )  ) {

			$editor_url = add_query_arg(
				array( 'page' => 'wpeditor_plugin', 'plugin' => $file ),
				admin_url( 'admin.php' )
			);
		}

		wp_redirect( esc_url_raw( $editor_url ) );
	}

	/**
	 * Transform a list of fields into a PHP/CSS style header comment
	 *
	 * @param array $fields
	 *
	 * @return string
	 */
	protected function build_header_comment( $fields ) {

		/* Start the header comment */
		$plugin_header_comment = "/*\n";

		foreach ( $fields as $i => $v ) {

			/* Add the headers to the comment */
			$plugin_header_comment .= "{$i}: {$v}\n";
		}

		/* Finish it off by closing the comment and adding a new line */
		$plugin_header_comment .= "*/\n";
		return $plugin_header_comment;
	}
}
