<?php
/**
 * Plugin Name: Plugin State Switcher
 * Description: Helps quickly switching between plugin sets.
 * Version: 1.0.2
 * Author: Joel Worsham
 * Author URI: http://realbigmarketing.com
 * Text Domain: pluginss
 */

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'PluginSS' ) ) {

	define( 'PLUGINSS_VERSION', '1.0.2' );
	define( 'PLUGINSS_DIR', plugin_dir_path( __FILE__ ) );
	define( 'PLUGINSS_URI', plugins_url( '', __FILE__ ) );

	/**
	 * Class PluginSS
	 *
	 * The main plugin class.
	 *
	 * @since 1.0.0
	 */
	final class PluginSS {

		/**
		 * Database functions.
		 *
		 * @since 1.0.0
		 *
		 * @var PluginSS_DB
		 */
		public $db;

		/**
		 * Loads the switcher.
		 *
		 * @since 1.0.0
		 *
		 * @var PluginSS_Switcher
		 */
		public $switcher;

		protected function __wakeup() {
		}

		protected function __clone() {
		}

		/**
		 * Call this method to get singleton
		 *
		 * @since 1.0.0
		 *
		 * @return PluginSS()
		 */
		public static function instance() {

			static $instance = null;

			if ( $instance === null ) {

				$instance = new PluginSS();
			}

			return $instance;
		}

		/**
		 * PluginSS constructor.
		 *
		 * @since 1.0.0
		 */
		function __construct() {

			$this->require_necessities();

			add_action( 'admin_init', array( $this, 'register_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}

		/**
		 * Requires and loads required files.
		 *
		 * @since 1.0.0
		 * @access private
		 */
		private function require_necessities() {

			require_once PLUGINSS_DIR . 'core/pluginss-functions.php';
			require_once PLUGINSS_DIR . 'core/class-pluginss-switcher.php';
			require_once PLUGINSS_DIR . 'core/class-pluginss-db.php';

			$this->switcher = new PluginSS_Switcher();
			$this->db       = new PluginSS_DB();
		}

		/**
		 * Registers plugin assets.
		 *
		 * @since 1.0.0
		 * @access private
		 */
		function register_assets() {

			wp_register_script(
				'plugin-state-switcher',
				PLUGINSS_URI . '/assets/dist/js/plugin-state-switcher.min.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PLUGINSS_VERSION,
				true
			);

			wp_register_style(
				'plugin-state-switcher',
				PLUGINSS_URI . '/assets/dist/css/plugin-state-switcher.min.css',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PLUGINSS_VERSION
			);

			wp_localize_script( 'plugin-state-switcher', 'PluginSS_Data', array(
				'nonce'        => wp_create_nonce( 'pluginss_nonce' ),
				'active_state' => pluginss_get_currently_active_state(),
			) );
		}

		/**
		 * Enqueues plugin assets.
		 *
		 * @since 1.0.0
		 * @access private
		 */
		function enqueue_assets() {

			wp_enqueue_script( 'plugin-state-switcher' );
			wp_enqueue_style( 'plugin-state-switcher' );
		}
	}

	// Load the bootstrapper
	require_once PLUGINSS_DIR . 'plugin-state-switcher-bootstrapper.php';
	new PluginSS_BootStrapper();

	// Installation
	require_once PLUGINSS_DIR . 'core/class-pluginss-install.php';
	register_activation_hook( __FILE__, array( 'PluginSS_Install', 'install' ) );


	/**
	 * Gets/loads the main plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @return PluginSS
	 */
	function PLUGINSS() {

		return PluginSS::instance();
	}
}