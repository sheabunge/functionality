<?php


/**
 * Class Functionality_Controller
 */
class Functionality_Controller {

	/**
	 * @var Functionality_Plugin
	 */
	public $plugin;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$filename = apply_filters( 'functionality_plugin_filename', 'functions.php' );

		$this->plugin = new Functionality_Plugin( $filename );
	}

	public function run() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'create_plugin' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation_hook') );
	}

	/**
	 * Add a link to edit the functionality plugin
	 * to the Plugins admin menu for easy access
	 *
	 * @since 1.0
	 * @uses add_plugins_page() To register the new submenu page
	 */
	public function admin_menu() {
		$plugin_file = $this->plugin->get_plugin_filename();
		$page_title = __( 'Edit Functions', 'functionality' );
		$page_url = add_query_arg( 'file', $plugin_file, 'plugin-editor.php' );

		if ( class_exists( 'WPEditor' )  ) {

			$page_url = add_query_arg(
				array(
					'page' => 'wpeditor_plugin',
					'plugin' => $plugin_file,
				),
				'admin.php'
			);

		}

		add_plugins_page( $page_title, $page_title, 'edit_plugins', $page_url );
	}
	
	/**
	 * Callback runs when this plugin is activated
	 *
	 * @since 1.1
	 */
	public static function activation_hook() {
		add_option( 'functionality_plugin_activated', true );
	}

	/**
	 * Create and activate the functionality plugin
	 * after this plugin is activated
	 *
	 * @since 1.1
	 * @uses activate_plugin() to activate the functionality plugin
	 */
	function create_plugin() {

		/* Check if this plugin has just been activated */
		if ( ! get_option( 'functionality_plugin_activated', false ) ) {
			return;
		}

		/* Create the plugin */
		$this->plugin->create_plugin();

		/* Activate the plugin */
		if ( ! function_exists( 'activate_plugin' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$filename = $this->plugin->get_plugin_filename();
		activate_plugin( $filename );

		delete_option( 'functionality_plugin_activated' );
	}

	/**
	 * Load the plugin textdomain
	 *
	 * @since 1.1
	 */
	function load_textdomain() {
		load_plugin_textdomain( 'functionality', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

