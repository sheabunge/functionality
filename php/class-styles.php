<?php

class Functionality_Styles extends Functionality_File {

	/**
	 * Retrieve the default content for the file
	 * @return string
	 */
	public function get_default_content() {

		/* Build the plugin header */
		$current_user = wp_get_current_user();

		$file_header = array(
			'Site Name'   => get_bloginfo( 'name' ),
			'Site URI'    => home_url(),
			'Author'      => $current_user->display_name,
			'Author URI'  => $current_user->user_url,
			'Version'     => date( 'Y.m.d' ),
			'License'     => 'GPL',
		);

		$plugin_header = apply_filters( 'functionality_css_header', $file_header, $this );

		$plugin_header_comment = $this->build_header_comment( $plugin_header );

		return "\n" .apply_filters( 'functionality_plugin_header_comment', $plugin_header_comment, $this ) . "\n";
	}

}
