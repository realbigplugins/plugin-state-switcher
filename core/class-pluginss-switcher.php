<?php
/**
 * Loads the switcher.
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die;

/**
 * Loads the switcher.
 *
 * @since 1.0.0
 */
class PluginSS_Switcher {

	/**
	 * PluginSS_Switcher constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		add_action( 'admin_init', array( $this, 'load_state' ) );
		add_action( 'pre_current_active_plugins', array( $this, 'output_switcher' ) );
		add_action( 'wp_ajax_pluginss_add_state', array( $this, 'ajax_add_state' ) );
		add_action( 'wp_ajax_pluginss_delete_state', array( $this, 'ajax_delete_state' ) );
	}

	/**
	 * Loads a state.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function load_state() {

		if ( ! isset( $_REQUEST['pluginss_nonce'] ) ||
		     ! wp_verify_nonce( $_REQUEST['pluginss_nonce'], 'load_state' )
		) {

			return;
		}

		if ( ! ( $state_ID = $_REQUEST['state_selector'] ) ) {

			return;
		}

		if ( ! ( $state = PLUGINSS_DB()->get_state( $state_ID ) ) ) {

			return;
		}

		switch ( $_REQUEST['pluginss_action'] ) {

			case 'deactivate':

				// Get current plugins, sans self
				$current = get_option( 'active_plugins' );
				unset( $current[ array_search( basename( PLUGINSS_DIR ) . '/plugin-state-switcher.php', $current ) ] );

				// Deactivate current set and then activate new set
				deactivate_plugins( $current );
				wp_redirect( add_query_arg(
					array(
						'pluginss_action' => 'activate',
						'pluginss_nonce'  => $_REQUEST['pluginss_nonce'],
						'state_selector'  => $state_ID,
					),
					admin_url( 'plugins.php' )
				) );

				break;

			case 'activate':

				activate_plugins( $state->active );
				wp_redirect( admin_url( 'plugins.php' ) );
				break;
		}

		exit();
	}

	/**
	 * Outputs the switcher HTML.
	 */
	function output_switcher() {

		$states       = PLUGINSS_DB()->get_states();
		$active_state = pluginss_get_currently_active_state();

		/**
		 * Filters the switcher notices.
		 *
		 * @since 1.0.0
		 */
		$notices = apply_filters( 'pluginss_switcher_notices', get_transient( 'pluginss_switcher_notices' ) );

		include PLUGINSS_DIR . 'core/views/switcher-notices.php';
		include PLUGINSS_DIR . 'core/views/switcher-output.php';
	}

	/**
	 * Adds a state from AJAX.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function ajax_add_state() {

		check_ajax_referer( 'pluginss_nonce', 'nonce' );

		$active = get_option( 'active_plugins' );

		// Remove self
		if ( ( $key = array_search( basename( PLUGINSS_DIR ) . '/plugin-state-switcher.php', $active ) ) ) {

			unset( $active[ $key ] );
		}

		$active = array_values( $active );

		$state_name = esc_attr( $_POST['name'] );

		if ( $ID = PLUGINSS_DB()->add_state( $state_name, $active ) ) {

			wp_send_json_success( array(
				'message' => sprintf(
					__( 'Successfully added new state "%s" with current active plugins!', 'plugin-state-switcher' ),
					"<strong>$state_name</strong>"
				),
				'id'      => $ID,
				'name'    => sprintf(
					__( '%s - Active', 'plugin-state-switcher' ),
					$state_name
				),
			) );

		} else {

			wp_send_json_error( array(
				'message' => __( 'Could not add new state.', 'plugin-state-switcher' ),
			) );
		}
	}

	/**
	 * Deletes a state from AJAX.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function ajax_delete_state() {

		check_ajax_referer( 'pluginss_nonce', 'nonce' );

		$state_ID = esc_attr( $_POST['id'] );

		if ( PLUGINSS_DB()->delete_state( $state_ID ) ) {

			wp_send_json_success( array(
				'message' => __( 'Successfully deleted state!', 'plugin-state-switcher' ),
			) );

		} else {

			wp_send_json_error( array(
				'message' => __( 'Could not delete state.', 'plugin-state-switcher' ),
			) );
		}
	}
}