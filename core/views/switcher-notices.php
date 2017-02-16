<?php
/**
 * HTML for the switcher notices.
 *
 * @since 1.0.0
 *
 * @var array $notices Notices to show.
 */

defined( 'ABSPATH' ) || die();
?>

<div id="pluginss-switcher-notices">
	<div class="notice pluginss-notice inline pluginss-notice-dummy" style="display: none;">
		<p></p>
	</div>

	<?php if ( $notices ) : ?>
		<?php foreach ( $notices as $notice ) : ?>
			<div class="notice pluginss-notice inline <?php echo esc_attr( $notice['type'] ); ?>">
				<p>
					<?php echo esc_attr( $notice ); ?>
				</p>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>