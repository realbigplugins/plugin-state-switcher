<?php
/**
 * Database functions.
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die;

/**
 * Class PluginSS_DB
 *
 * Database functions.
 *
 * @since 1.0.0
 */
class PluginSS_DB {

	/**
	 * Gets the plugin states table name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function states_table() {

		global $wpdb;

		return "{$wpdb->prefix}plugin_states";
	}

	/**
	 * Gets a plugin state by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $ID ID of state.
	 *
	 * @return array|null|object|void
	 */
	public static function get_state( $ID ) {

		global $wpdb;

		$state_table = self::states_table();

		/**
		 * Filters the get_state results.
		 *
		 * @since 1.0.0
		 */
		$results = apply_filters( 'pluginss_get_state', $wpdb->get_row( "SELECT * FROM $state_table WHERE ID = $ID" ) );

		// Unserialize the state active plugins
		$results->active = unserialize( $results->active );

		return $results;
	}

	/**
	 * Gets a plugin state by name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name State name.
	 *
	 * @return array|null|object|void
	 */
	public static function get_state_by_name( $name ) {

		global $wpdb;

		$state_table = self::states_table();

		/**
		 * Filters the state name before retrieving state.
		 *
		 * @since 1.0.0
		 */
		$name = apply_filters( 'pluginss_get_state_name', $name );

		/**
		 * Filters the get_state by name results.
		 *
		 * @since 1.0.0
		 */
		$results = apply_filters(
			'pluginss_get_state_by_name',
			$wpdb->get_row( $wpdb->prepare( "SELECT * FROM $state_table WHERE name='%s'", $name ) )
		);

		return $results;
	}

	/**
	 * Gets a plugin state by active plugins.
	 *
	 * @since 1.0.0
	 *
	 * @param array $active Active plugins.
	 *
	 * @return array|null|object|void
	 */
	public static function get_state_by_active( $active ) {

		global $wpdb;

		$state_table = self::states_table();

		/**
		 * Filters the currently active plugins before retrieving state.
		 *
		 * @since 1.0.0
		 */
		$active = apply_filters( 'pluginss_get_state_active', serialize( $active ) );

		/**
		 * Filters the get_state by active results.
		 *
		 * @since 1.0.0
		 */
		$results = apply_filters(
			'pluginss_get_state_by_active',
			$wpdb->get_row( $wpdb->prepare( "SELECT * FROM $state_table WHERE active='%s'", $active ) )
		);

		return $results;
	}

	/**
	 * Adds a plugin state.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name State name.
	 * @param array|null $active_plugins Array of active plugins.
	 *
	 * @return array|null|object|void
	 */
	public static function add_state( $name, $active_plugins ) {

		global $wpdb;

		$state_table = self::states_table();

		/**
		 * Filters new state to add.
		 *
		 * @since 1.0.0
		 */
		$active_plugins = apply_filters( 'pluginss_add_state', $active_plugins, $name );

		// Don't allow duplicates
		if ( self::get_state_by_active( $active_plugins ) ||
		     self::get_state_by_name( $name )
		) {

			return false;
		}

		$result = $wpdb->insert(
			$state_table,
			array(
				'name'   => $name,
				'active' => serialize( $active_plugins ),
			),
			array(
				'%s',
				'%s',
			)
		);

		if ( $result ) {

			return $wpdb->insert_id;

		} else {

			return false;
		}
	}

	/**
	 * Updates a plugin state.
	 *
	 * @since 1.0.0
	 *
	 * @param int $ID State ID.
	 * @param string $name State name.
	 * @param array|null $active_plugins Array of active plugins.
	 *
	 * @return array|null|object|void
	 */
	public static function update_state( $ID, $name, $active_plugins ) {

		global $wpdb;

		$state_table = self::states_table();

		/**
		 * Filters the state to update.
		 *
		 * @since 1.0.0
		 */
		$state = apply_filters( 'pluginss_update_state', array(
			'name'   => $name,
			'active' => $active_plugins,
		), $ID );

		return $wpdb->update(
			$state_table,
			$state,
			array( 'ID' => $ID )
		);
	}

	/**
	 * Deletes a plugin state.
	 *
	 * @since 1.0.0
	 *
	 * @param int $ID State ID.
	 *
	 * @return array|null|object|void
	 */
	public static function delete_state( $ID ) {

		global $wpdb;

		$state_table = self::states_table();

		/**
		 * Allow short-ciruiting of deleting state.
		 *
		 * @since 1.0.0
		 */
		if ( apply_filters( 'pluginss_delete_state', false, $ID ) ) {

			return false;
		}

		return $wpdb->delete(
			$state_table,
			array( 'ID' => $ID )
		);
	}

	/**
	 * Retrieves all plugin states.
	 *
	 * @since 1.0.0
	 *
	 * @return array|null|object|void
	 */
	public static function get_states() {

		global $wpdb;

		$state_table = self::states_table();

		/**
		 * Filters the get_states results.
		 *
		 * @since 1.0.0
		 */
		$results = apply_filters( 'pluginss_get_states', $wpdb->get_results( "SELECT * FROM $state_table" ) );

		return $results;
	}
}

/**
 * Quick access to database class.
 *
 * @since 1.0.0
 */
function PLUGINSS_DB() {

	return PLUGINSS()->db;
}