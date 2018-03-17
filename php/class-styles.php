<?php

/**
 * Subclass for creating CSS stylesheet files
 */
class Functionality_Styles extends Functionality_File {

	/**
	 * Retrieve the default content for the file
	 * @return string
	 */
	public function get_default_content() {
		$current_user = wp_get_current_user();

		$file_header = array(
			'Site Name'  => get_bloginfo( 'name' ),
			'Site URI'   => home_url(),
			'Author'     => $current_user->display_name,
			'Author URI' => $current_user->user_url,
			'Version'    => date( 'Y.m.d' ),
			'License'    => 'GPL',
		);

		$plugin_header = apply_filters( 'functionality_css_header', $file_header, $this );

		return "\n" . $this->build_header_comment( $plugin_header ) . "\n";
	}

	/**
	 * Retrieve the handle passed to wp_enqueue_style()
	 */
	public function get_style_handle() {
		$filename = $this->get_filename();

		if ( '.css' === substr( $filename, -4 ) ) {
			$filename = substr( $filename, 0, -4 );
		}

		return 'functionality-' . $filename;
	}

	/**
	 * Enqueue this file as a stylesheet
	 *
	 * @param array $deps Stylesheet dependencies
	 */
	public function enqueue_style( $deps = array() ) {

		wp_enqueue_style(
			$this->get_style_handle(),
			plugins_url( $this->get_file() ),
			$deps,
			date( 'Y.m.d' )
		);
	}

}
