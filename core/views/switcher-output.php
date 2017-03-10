<?php
/**
 * HTML for the switcher.
 *
 * @since 1.0.0
 *
 * @var array $states Saved states.
 * @var int|false $active_state The currently active state.
 */

defined( 'ABSPATH' ) || die();
?>

<div id="pluginss-container">

	<p class="pluginss-switcher-title">
		<?php _e( 'Plugin States', 'plugin-state-switcher' ); ?>
	</p>

	<form method="post" class="pluginss-form">

		<?php wp_nonce_field( 'load_state', 'pluginss_nonce' ); ?>

		<input type="hidden" name="pluginss_action" value="deactivate" />

		<fieldset class="pluginss-manage-states">

			<select name="state_selector">
				<?php foreach ( $states as $state ): ?>
					<option value="<?php echo esc_attr( $state->id ); ?>"
						<?php selected( $active_state, $state->id ); ?>
						<?php echo $active_state == $state->id ? 'data-active="1"' : ''; ?>>

						<?php
						if ( $active_state == $state->id ) {

							printf(
								__( '%s - Active', 'plugin-state-switcher' ),
								$state->name
							);

						} else {

							echo $state->name;
						}
						?>

					</option>
				<?php endforeach; ?>
			</select>

			<button type="submit" class="button" name="load_state">
				<?php _e( 'Load State', 'plugin-state-switcher' ); ?>
			</button>

			<button type="button" class="button pluginss-delete-state" name="delete_state">
				<?php _e( 'Delete State', 'plugin-state-switcher' ); ?>
			</button>

		</fieldset>

		<fieldset class="pluginss-add-state">

			<input type="text" name="state_name" placeholder="<?php _e( 'New state name', 'plugin-state-switcher' ); ?>"/>

			<button type="button" class="button button-primary" name="add_state">
				<?php _e( 'Add New State', 'plugin-state-switcher' ); ?>
			</button>

		</fieldset>
	</form>
</div>