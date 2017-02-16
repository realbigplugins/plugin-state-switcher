<?php
/**
 * Installs the plugin.
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die;

/**
 * Class PluginSS_Install
 *
 * Installs the plugin.
 *
 * @since 1.0.0
 */
class PluginSS_Install {

	/**
	 * Loads the install functions.
	 *
	 * @since 1.0.0
	 */
	static function install() {

		add_option( 'pluginss_db_version', '1.0.0' );

		self::setup_tables();
	}

	/**
	 * Sets up the tables.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @global wpdb $wpdb
	 */
	private static function setup_tables() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}plugin_states (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  name text NOT NULL,
		  active longtext NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}