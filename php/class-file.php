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
	public function get_editor_url() {
		$file = urlencode( $this->get_file() );

		/* link to WP Editor if it is installed */
		if ( class_exists( 'WPEditor' ) ) {
			return add_query_arg( array( 'page' => 'wpeditor_plugin', 'plugin' => $file ), admin_url( 'admin.php' ) );

		} else {
			/* otherwise link to the default WordPress editor */
			return add_query_arg( 'file', $file, admin_url( 'plugin-editor.php' ) );
		}
	}

	/**
	 * Retrieve the URL to the administration menu
	 * @return string
	 */
	public function get_menu_url() {
		return add_query_arg( 'page', $this->get_menu_slug(), 'plugins.php' );
	}

	/**
	 * Load the WP Filesystem API
	 *
	 * @param bool $silent Prevent a credential form from being displayed
	 *
	 * @return bool true on success; false on failure
	 */
	private function load_wp_filesystem( $silent = false ) {

		if ( $silent ) {

			/* if the FS method is direct, we can just call the function and load the class */
			if ( 'direct' === get_filesystem_method() ) {
				$creds = request_filesystem_credentials( self_admin_url() );
				return WP_Filesystem( $creds );
			}

			/* otherwise, attempt to call the function, but catch all of its output */
			ob_start();
			$creds = request_filesystem_credentials( self_admin_url() );
			ob_end_clean();

			if ( false !== $creds ) {
				return WP_Filesystem( $creds );
			}

			return false;
		}

		/* otherwise, we can request filesystem credentials using a form if necessary */
		$url = wp_nonce_url( $this->get_menu_url(), 'functionality' );
		$creds = request_filesystem_credentials( $url );

		/* if the credentials are false, more information is needed */
		if ( false === $creds ) {
			return false;
		}

		/* test the provided credentials, and prompt again if necessary */
		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( $url, '', true );
			return false;
		}

		return true;
	}

	/**
	 * Create file in the plugins directory if it does not already exist
	 *
	 * @param bool $silent Attempt to create the file without prompting for filesystem credentials
	 *
	 * @return bool true if file now exists
	 *
	 * @since 1.0
	 */
	public function create_file( $silent ) {
		$file = $this->get_full_path();

		/* Bail early if the file already exists */
		if ( file_exists( $file ) ) {
			return true;
		}

		/* Load the WP Filesystem API */
		if ( ! $this->load_wp_filesystem( $silent ) ) {
			return false;
		}

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
	 * Retrieve the WordPress admin menu slug for this file
	 * @return string
	 */
	public function get_menu_slug() {
		return 'functionality-' . $this->get_filename();
	}

	/**
	 * Add a link to edit this file to the Plugins admin menu for easy access
	 *
	 * @param string $label Text to use for the menu label
	 *
	 * @return string hook name for the newly created page
	 */
	public function register_admin_menu( $label ) {

		$hook = add_plugins_page(
			$label, $label,
			'edit_plugins',
			$this->get_menu_slug(),
			array( $this, 'render_admin_menu' )
		);

		add_action( 'load-' . $hook, array( $this, 'load_admin_menu' ) );

		return $hook;
	}

	/**
	 * Create the file and redirect the user to the edit menu, if possible
	 */
	public function load_admin_menu() {

		/* don't redirect if the file cannot be created */
		if ( ! $this->create_file( true ) ) {
			return;
		}

		wp_redirect( esc_url_raw( $this->get_editor_url() ) );
		exit;
	}

	/**
	 * Render the edit menu, possibly displaying a filesystem credentials form
	 */
	public function render_admin_menu() {
		/* if we have reached this point, we need to display the credential form */
		$this->create_file( false );
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

		$padding_length = max( array_map( 'strlen', array_keys( $fields ) ) );

		foreach ( $fields as $i => $v ) {

			/* Add the headers to the comment */
			$plugin_header_comment .= "{$i}: " . str_repeat( ' ', $padding_length - strlen( $i ) ) . "{$v}\n";
		}

		/* Finish it off by closing the comment and adding a new line */
		$plugin_header_comment .= "*/\n";
		return $plugin_header_comment;
	}
}
