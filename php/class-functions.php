<?php

/**
 * Subclass uses for creating a new functionality plugin file
 */
class Functionality_Functions extends Functionality_File {

	/**
	 * Create file in the plugins directory if it does not already exist
	 *
	 * @param bool $silent Attempt to create the file without prompting for filesystem credentials
	 * @param bool $activate_plugin Activate the plugin after creation
	 *
	 * @return bool If the file now exists
	 */
	public function create_file( $silent, $activate_plugin = true ) {

		/* No need to do anything here if the plugin has already been created */
		if ( file_exists( $this->get_full_path() ) ) {
			return true;
		}

		/* Create the new file using the parent function */
		if ( ! $result = parent::create_file( $silent ) ) {
			return false;
		}

		do_action( 'functionality_plugin_created', $this->get_full_path() );

		/* Clean up the previous version of the plugin if it exists */
		global $wp_filesystem; /** @var WP_Filesystem_Base $wp_filesystem */

		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( file_exists( WP_PLUGIN_DIR . '/functions.php' ) ) {

			/* Deactivate the old version of this plugin if is active */
			if ( is_plugin_active( 'functions.php' ) ) {
				deactivate_plugins( 'functions.php' );
			} else {
				/* Don't automatically activate the new plugin if this one was inactive */
				$activate_plugin = false;
			}

			/* Delete the file */
			if ( $wp_filesystem ) {
				$wp_filesystem->delete( WP_PLUGIN_DIR . '/functions.php' );
			}
		}

		/* Activate the newly-created plugin */
		if ( $activate_plugin && ! is_plugin_active( $this->get_file() ) ) {
			activate_plugin( $this->get_file() );
		}

		return true;
	}

	/**
	 * Retrieve the default content for the file
	 * @return string
	 */
	public function get_default_content() {
		$content = '';

		/** @var WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		/* copy over content from previous location if existing */
		$previous_file = WP_PLUGIN_DIR . '/functions.php';
		if ( $wp_filesystem && $wp_filesystem->exists( $previous_file ) && $wp_filesystem->is_file( $previous_file ) ) {
			$content = $wp_filesystem->get_contents( $previous_file );
		}

		/* otherwise build default content */
		if ( empty( $content ) ) {
			$content = "<?php\n\n" . $this->get_plugin_header();
		}

		/* add code for enabling the CSS feature */
		$content .= "\n// uncomment the below line to enable CSS functionality\n";
		$content .= "// add_filter( 'functionality_enable_styles', '__return_true' );\n\n";

		return $content;
	}

	/**
	 * Build the header comment for the plugin
	 *
	 * @since 1.0
	 *
	 * @param array $plugin_header The headers for the plugin
	 *
	 * @return string
	 */
	public function get_plugin_header( $plugin_header = array() ) {

		/* Build the plugin header */
		$current_user = wp_get_current_user();

		$default_plugin_header = array(
			'Plugin Name' => get_bloginfo( 'name' ),
			'Plugin URI'  => home_url(),
			'Description' => sprintf ( __( "A site-specific functionality plugin for %s where you can paste your code snippets instead of using the theme's functions.php file", 'functionality' ), get_bloginfo( 'name' ) ),
			'Author'      => $current_user->display_name,
			'Author URI'  => $current_user->user_url,
			'Version'     => date( 'Y.m.d' ),
			'License'     => 'GPL',
		);

		$plugin_header = wp_parse_args( $plugin_header, $default_plugin_header );
		$plugin_header = apply_filters( 'functionality_plugin_header', $plugin_header, $this );

		return $this->build_header_comment( $plugin_header );
	}
}
