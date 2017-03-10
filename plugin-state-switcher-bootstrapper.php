<?php
/**
 * Bootstrapper for the plugin.
 *
 * Makes sure everything is good to go for loading the plugin, and then loads it.
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die;

/**
 * Class PluginSS_BootStrapper
 *
 * Bootstrapper for the plugin.
 *
 * Makes sure everything is good to go for loading the plugin, and then loads it.
 *
 * @since 1.0.0
 */
class PluginSS_BootStrapper {

	/**
	 * Notices to show if cannot load.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * PluginSS_BootStrapper constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'maybe_load' ) );
	}

	/**
	 * Maybe loads the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function maybe_load() {

		// Only load on admin.
		if ( ! is_admin() ) {

			return;
		}

		switch ( apply_filters( 'pluginss_dependencies_version_mode', 'recommended' ) ) {

			case 'minimum':

				$php_min_version = '5.3.0';
				$wp_min_version  = '3.5.0';

				break;

			case 'recommended':
			default:

				$php_min_version = '5.6.0';
				$wp_min_version  = '4.6.0';

				break;
		}

		$php_version = phpversion();
		$wp_version  = get_bloginfo( 'version' );

		// Minimum PHP version
		if ( version_compare( $php_version, $php_min_version ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum PHP version of %s required. Current version is %s. Please contact your system administrator to upgrade PHP to its latest version.', 'plugin-state-switcher' ),
				$php_min_version,
				$php_version
			);
		}

		// Minimum WordPress version
		if ( version_compare( $wp_version, $wp_min_version ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum WordPress version of %s required. Current version is %s. Please contact your system administrator to upgrade WordPress to its latest version.', 'plugin-state-switcher' ),
				$wp_min_version,
				$wp_version
			);
		}

		// Don't load and show errors if incompatible environment.
		if ( ! empty( $this->notices ) ) {

			add_action( 'admin_notices', array( $this, 'notices' ) );

			return;
		}

		$this->load();
	}

	/**
	 * Loads the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function load() {

		PluginSS();
	}

	/**
	 * Shows notices on failure to load.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function notices() {
		?>
		<div class="notice error">
			<p>
				<?php
				printf(
					__( '%sPlugin State Switcher%s could not load because of the following errors:', 'plugin-state-switcher' ),
					'<strong>',
					'</strong>'
				);
				?>
			</p>

			<ul>
				<?php foreach ( $this->notices as $notice ) : ?>
					<li>
						<?php echo $notice; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}