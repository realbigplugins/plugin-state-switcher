<?php
/**
 * Helper functions.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die;

/**
 * Gets the currently active state, if any.
 *
 * @since {{VERSION}}
 */
function pluginss_get_currently_active_state() {

	$active = pluginss_get_active();

	if ( $result = PLUGINSS_DB()->get_state_by_active( $active ) ) {

		return $result->id;

	} else {

		return false;
	}
}

/**
 * Gets active plugins, minus self.
 *
 * @since {{VERSION}}
 *
 * @return false|array
 */
function pluginss_get_active() {

	if ( ! ( $active = get_option( 'active_plugins' ) ) ) {

		return false;
	}

	// Remove self
	if ( ( $key = array_search( basename( PLUGINSS_DIR ) . '/plugin-state-switcher.php', $active ) ) ) {

		unset( $active[ $key ] );
	}

	$active = array_values( $active );

	return $active;
}