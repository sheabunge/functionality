<?php


/**
 * Class Functionality_Controller
 */
class Functionality_Controller {

	/**
	 * @var Functionality_Functions
	 */
	public $functions;

	/**
	 * @var Functionality_Styles
	 */
	public $styles;

	/**
	 * Full filesystem path to main plugin file
	 * @var string
	 */
	public $plugin_file;

	/**
	 * Determines whether the CSS styles component is enabled
	 * @var bool
	 */
	public $styles_enabled = false;

	/**
	 * Class constructor
	 * @param string $plugin_file Full filesystem path to main plugin file
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Load the class
	 */
	public function load() {
		$this->styles_enabled = apply_filters( 'functionality_enable_styles', false );

		$filename = apply_filters( 'functionality_plugin_filename', 'functions.php' );
		$this->functions = new Functionality_Functions( $filename );

		if ( $this->styles_enabled ) {
			$filename = apply_filters( 'functionality_css_filename', 'style.css' );
			$this->styles = new Functionality_Styles( $filename );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		}

		add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );

		$this->load_textdomain();
	}

	/**
	 * Register the edit menus for both files
	 */
	public function add_admin_menus() {

		if ( $this->styles_enabled ) {
			$this->styles->register_admin_menu( __( 'Edit Styles', 'functionality' ) );
		}

		$this->functions->register_admin_menu( __( 'Edit Functions', 'functionality' ) );
	}

	/**
	 * Enqueue the stylesheet on the front-end if enabled
	 */
	public function enqueue_styles() {
		if ( $this->styles_enabled && apply_filters( 'functionality_enqueue_styles', true ) ) {
			$this->styles->enqueue_style();
		}
	}

	/**
	 * Load the plugin translation files
	 *
	 * @since 1.1
	 */
	function load_textdomain() {
		$rel_path = dirname( plugin_basename( $this->plugin_file ) );
		load_plugin_textdomain( 'functionality', false,  $rel_path . '/languages/' );
	}
}

