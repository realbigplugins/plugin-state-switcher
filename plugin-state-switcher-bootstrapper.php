<?php
/**
 * Bootstrapper for the plugin.
 *
 * Makes sure everything is good to go for loading the plugin, and then loads it.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die;

/**
 * Class PluginSS_BootStrapper
 *
 * Bootstrapper for the plugin.
 *
 * Makes sure everything is good to go for loading the plugin, and then loads it.
 *
 * @since {{VERSION}}
 */
class PluginSS_BootStrapper {

	/**
	 * Notices to show if cannot load.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * PluginSS_BootStrapper constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'maybe_load' ) );
	}

	/**
	 * Maybe loads the plugin.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function maybe_load() {

		// Only load on admin.
		if ( ! is_admin() ) {

			return;
		}

		$php_version = phpversion();
		$wp_version  = get_bloginfo( 'version' );

		// Minimum PHP version
		if ( version_compare( $php_version, '5.6.0' ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum PHP version of 5.6.0 required. Current version is %s. Please contact your system administrator to upgrade PHP to its latest version.', 'pluginss' ),
				$php_version
			);
		}

		// Minimum WordPress version
		if ( version_compare( $wp_version, '4.6.0' ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum WordPress version of 4.6.0 required. Current version is %s. Please contact your system administrator to upgrade WordPress to its latest version.', 'pluginss' ),
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
	 * @since {{VERSION}}
	 * @access private
	 */
	private function load() {

		PluginSS();
	}

	/**
	 * Shows notices on failure to load.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function notices() {
		?>
		<div class="notice error">
			<p>
				<?php
				printf(
					__( '%sPlugin State Switcher%s could not load because of the following errors:', 'pluginss' ),
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