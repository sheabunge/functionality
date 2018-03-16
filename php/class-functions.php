<?php

/**
 * Subclass uses for creating a new functionality plugin file
 */
class Functionality_Functions extends Functionality_File {

	public function create_file() {

		if ( ! file_exists( $this->get_full_path() ) ) {

			/* Create the file using the parent function */
			if ( ! $result = parent::create_file() ) {
				return false;
			}

			/* Run an action hook */
			do_action( 'functionality_plugin_created', $this->get_full_path() );
		}

		if ( ! is_plugin_active( $this->get_file() ) ) {

			/* Activate the plugin */
			if ( ! function_exists( 'activate_plugin' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}

			activate_plugin( $this->get_file() );
		}

		return true;
	}

	/**
	 * Retrieve the default content for the file
	 * @return string
	 */
	public function get_default_content() {
		return "<?php\n\n" . $this->get_plugin_header() .
		       "\n// uncomment the below line to enable CSS functionality\n// add_filter( 'functionality_enable_styles', '__return_true' );\n\n";
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
