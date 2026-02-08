<?php
/**
 * GitHub Theme Updater for Law and Beyond.
 *
 * Checks GitHub releases for a newer version and integrates with
 * the WordPress core update system so admins can update from
 * Dashboard → Updates or the Themes screen.
 *
 * @package LawAndBeyond
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LawAndBeyond_GitHub_Updater {

	/**
	 * GitHub username.
	 *
	 * @var string
	 */
	private $github_username = 'CypherNinjaa';

	/**
	 * GitHub repository name.
	 *
	 * @var string
	 */
	private $github_repo = 'lawandbeyond-wp-theme';

	/**
	 * Theme slug (directory name).
	 *
	 * @var string
	 */
	private $theme_slug = 'lawandbeyond';

	/**
	 * Current theme version from style.css.
	 *
	 * @var string
	 */
	private $current_version;

	/**
	 * Transient key for caching GitHub data.
	 *
	 * @var string
	 */
	private $transient_key = 'lawandbeyond_github_update';

	/**
	 * Cache duration in seconds (6 hours).
	 *
	 * @var int
	 */
	private $cache_duration = 21600;

	/**
	 * Constructor — register WordPress hooks.
	 */
	public function __construct() {
		$theme = wp_get_theme( $this->theme_slug );
		$this->current_version = $theme->get( 'Version' );

		// Inject update data into the themes update transient.
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );

		// Supply theme info for the "View version details" popup.
		add_filter( 'themes_api', array( $this, 'theme_info' ), 20, 3 );

		// Clear cache after an update completes.
		add_action( 'upgrader_process_complete', array( $this, 'clear_cache' ), 10, 2 );

		// Admin page under Appearance.
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );

		// AJAX handler for manual update check.
		add_action( 'wp_ajax_lawandbeyond_check_update', array( $this, 'ajax_check_update' ) );
		add_action( 'wp_ajax_lawandbeyond_clear_cache', array( $this, 'ajax_clear_cache' ) );
	}

	/**
	 * Fetch latest release data from GitHub API.
	 *
	 * @param bool $force_refresh Ignore cache if true.
	 * @return object|false Release data or false on failure.
	 */
	private function get_github_release( $force_refresh = false ) {
		if ( ! $force_refresh ) {
			$cached = get_transient( $this->transient_key );
			if ( false !== $cached ) {
				return $cached;
			}
		}

		$url = sprintf(
			'https://api.github.com/repos/%s/%s/releases/latest',
			$this->github_username,
			$this->github_repo
		);

		$response = wp_remote_get( $url, array(
			'timeout'    => 15,
			'user-agent' => 'LawAndBeyond-WP-Theme/' . $this->current_version,
			'headers'    => array( 'Accept' => 'application/vnd.github.v3+json' ),
		) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			// Cache a failure for 30 minutes to avoid hammering the API.
			set_transient( $this->transient_key, false, 1800 );
			return false;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $data ) || empty( $data->tag_name ) ) {
			set_transient( $this->transient_key, false, 1800 );
			return false;
		}

		set_transient( $this->transient_key, $data, $this->cache_duration );

		return $data;
	}

	/**
	 * Parse a version string from a GitHub tag (strips leading "v").
	 *
	 * @param string $tag_name e.g. "v1.2.0"
	 * @return string e.g. "1.2.0"
	 */
	private function parse_version( $tag_name ) {
		return ltrim( $tag_name, 'vV' );
	}

	/**
	 * Get the download URL for the release zip.
	 *
	 * Prefers the uploaded .zip asset; falls back to the auto-generated zipball.
	 *
	 * @param object $release GitHub release object.
	 * @return string Download URL.
	 */
	private function get_download_url( $release ) {
		// Prefer the manually-uploaded .zip asset.
		if ( ! empty( $release->assets ) ) {
			foreach ( $release->assets as $asset ) {
				if ( substr( $asset->name, -4 ) === '.zip' ) {
					return $asset->browser_download_url;
				}
			}
		}

		// Fallback to GitHub's auto-generated source zip.
		return $release->zipball_url;
	}

	/**
	 * Inject update data into the WordPress core themes transient.
	 *
	 * @param object $transient The update_themes transient data.
	 * @return object Modified transient.
	 */
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$release = $this->get_github_release();

		if ( ! $release || empty( $release->tag_name ) ) {
			return $transient;
		}

		$latest_version = $this->parse_version( $release->tag_name );

		if ( version_compare( $latest_version, $this->current_version, '>' ) ) {
			$transient->response[ $this->theme_slug ] = array(
				'theme'       => $this->theme_slug,
				'new_version' => $latest_version,
				'url'         => $release->html_url,
				'package'     => $this->get_download_url( $release ),
			);
		}

		return $transient;
	}

	/**
	 * Supply theme information for the "View version details" overlay.
	 *
	 * @param false|object|array $result Default result.
	 * @param string             $action API action.
	 * @param object             $args   Arguments.
	 * @return false|object
	 */
	public function theme_info( $result, $action, $args ) {
		if ( 'theme_information' !== $action ) {
			return $result;
		}

		if ( ! isset( $args->slug ) || $args->slug !== $this->theme_slug ) {
			return $result;
		}

		$release = $this->get_github_release();

		if ( ! $release ) {
			return $result;
		}

		$theme = wp_get_theme( $this->theme_slug );

		$info                = new stdClass();
		$info->name          = $theme->get( 'Name' );
		$info->slug          = $this->theme_slug;
		$info->version       = $this->parse_version( $release->tag_name );
		$info->author        = $theme->get( 'Author' );
		$info->homepage      = $theme->get( 'ThemeURI' );
		$info->requires      = $theme->get( 'RequiresWP' );
		$info->requires_php  = $theme->get( 'RequiresPHP' );
		$info->download_link = $this->get_download_url( $release );
		$info->sections      = array(
			'description' => $theme->get( 'Description' ),
			'changelog'   => $this->format_changelog( $release ),
		);
		$info->last_updated  = ! empty( $release->published_at )
			? date_i18n( 'Y-m-d', strtotime( $release->published_at ) )
			: '';

		return $info;
	}

	/**
	 * Format release body as changelog HTML.
	 *
	 * @param object $release GitHub release object.
	 * @return string HTML changelog.
	 */
	private function format_changelog( $release ) {
		$body = ! empty( $release->body ) ? $release->body : 'No changelog provided.';

		// Basic Markdown → HTML for bullet lists.
		$body = esc_html( $body );
		$body = preg_replace( '/^[\-\*] (.+)$/m', '<li>$1</li>', $body );
		$body = preg_replace( '/(<li>.*<\/li>)/s', '<ul>$1</ul>', $body );
		$body = preg_replace( '/^### (.+)$/m', '<h4>$1</h4>', $body );
		$body = nl2br( $body );

		return $body;
	}

	/**
	 * Clear the cached GitHub data after a theme update.
	 *
	 * @param WP_Upgrader $upgrader Upgrader instance.
	 * @param array       $options  Update options.
	 */
	public function clear_cache( $upgrader, $options ) {
		if ( 'update' === $options['action'] && 'theme' === $options['type'] ) {
			delete_transient( $this->transient_key );
		}
	}

	/* =================================================================
	   Admin Page — Appearance → Theme Update
	   ================================================================= */

	/**
	 * Register the admin page.
	 */
	public function add_admin_page() {
		add_theme_page(
			__( 'Theme Update', 'lawandbeyond' ),
			__( 'Theme Update', 'lawandbeyond' ),
			'update_themes',
			'lawandbeyond-update',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Render the admin update page.
	 */
	public function render_admin_page() {
		$release        = $this->get_github_release();
		$latest_version = $release ? $this->parse_version( $release->tag_name ) : null;
		$has_update     = $latest_version && version_compare( $latest_version, $this->current_version, '>' );
		$repo_url       = sprintf( 'https://github.com/%s/%s', $this->github_username, $this->github_repo );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Law and Beyond — Theme Update', 'lawandbeyond' ); ?></h1>

			<div class="card" style="max-width: 620px; margin-bottom: 20px;">
				<h2 style="margin-top:0;"><?php esc_html_e( 'Update Status', 'lawandbeyond' ); ?></h2>

				<table class="widefat" style="border:none;">
					<tr>
						<td style="width:180px;"><strong><?php esc_html_e( 'Installed Version', 'lawandbeyond' ); ?></strong></td>
						<td><code><?php echo esc_html( $this->current_version ); ?></code></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Latest Version (GitHub)', 'lawandbeyond' ); ?></strong></td>
						<td>
							<?php if ( $latest_version ) : ?>
								<code><?php echo esc_html( $latest_version ); ?></code>
								<?php if ( $has_update ) : ?>
									<span style="color:#dba617;margin-left:8px;">
										<span class="dashicons dashicons-warning"></span>
										<?php esc_html_e( 'Update available!', 'lawandbeyond' ); ?>
									</span>
								<?php else : ?>
									<span style="color:#00a32a;margin-left:8px;">
										<span class="dashicons dashicons-yes-alt"></span>
										<?php esc_html_e( 'You are up to date!', 'lawandbeyond' ); ?>
									</span>
								<?php endif; ?>
							<?php else : ?>
								<span style="color:#d63638;">
									<span class="dashicons dashicons-dismiss"></span>
									<?php esc_html_e( 'Could not fetch version info', 'lawandbeyond' ); ?>
								</span>
							<?php endif; ?>
						</td>
					</tr>
					<?php if ( $release && ! empty( $release->published_at ) ) : ?>
					<tr>
						<td><strong><?php esc_html_e( 'Release Date', 'lawandbeyond' ); ?></strong></td>
						<td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $release->published_at ) ) ); ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><strong><?php esc_html_e( 'Repository', 'lawandbeyond' ); ?></strong></td>
						<td>
							<a href="<?php echo esc_url( $repo_url ); ?>" target="_blank">
								<?php echo esc_html( $this->github_username . '/' . $this->github_repo ); ?>
								<span class="dashicons dashicons-external"></span>
							</a>
						</td>
					</tr>
				</table>

				<p style="margin-top:15px;display:flex;gap:10px;flex-wrap:wrap;">
					<?php if ( $has_update ) : ?>
						<a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>" class="button button-primary">
							<?php printf( esc_html__( 'Update to %s', 'lawandbeyond' ), esc_html( $latest_version ) ); ?>
						</a>
					<?php endif; ?>
					<button type="button" id="lab-check-update" class="button">
						<span class="dashicons dashicons-update" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Check for Updates', 'lawandbeyond' ); ?>
					</button>
					<button type="button" id="lab-clear-cache" class="button">
						<span class="dashicons dashicons-trash" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Clear Cache', 'lawandbeyond' ); ?>
					</button>
				</p>
				<p class="description">
					<?php esc_html_e( 'Updates are fetched from GitHub releases.', 'lawandbeyond' ); ?>
					<a href="<?php echo esc_url( $repo_url . '/releases' ); ?>" target="_blank">
						<?php esc_html_e( 'View all releases →', 'lawandbeyond' ); ?>
					</a>
				</p>
			</div>

			<?php if ( $release && ! empty( $release->body ) ) : ?>
			<div class="card" style="max-width: 620px;">
				<h2 style="margin-top:0;"><?php esc_html_e( 'Changelog', 'lawandbeyond' ); ?></h2>
				<div style="font-size:14px;line-height:1.6;">
					<?php echo wp_kses_post( $this->format_changelog( $release ) ); ?>
				</div>
			</div>
			<?php endif; ?>

			<div id="lab-update-notice" style="display:none;margin-top:10px;"></div>
		</div>

		<script>
		(function($) {
			var ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
			var nonce   = '<?php echo esc_js( wp_create_nonce( 'lawandbeyond_update_nonce' ) ); ?>';

			function showNotice(msg, type) {
				$('#lab-update-notice').html(
					'<div class="notice notice-' + type + ' is-dismissible"><p>' + msg + '</p></div>'
				).show();
			}

			$('#lab-check-update').on('click', function() {
				var $btn = $(this);
				$btn.prop('disabled', true).find('.dashicons').addClass('spin');
				$.post(ajaxUrl, { action: 'lawandbeyond_check_update', nonce: nonce }, function(res) {
					$btn.prop('disabled', false).find('.dashicons').removeClass('spin');
					if (res.success) {
						showNotice(res.data, 'info');
						setTimeout(function() { location.reload(); }, 1500);
					} else {
						showNotice(res.data || 'Error checking for updates.', 'error');
					}
				});
			});

			$('#lab-clear-cache').on('click', function() {
				var $btn = $(this);
				$btn.prop('disabled', true);
				$.post(ajaxUrl, { action: 'lawandbeyond_clear_cache', nonce: nonce }, function(res) {
					$btn.prop('disabled', false);
					showNotice(res.success ? 'Cache cleared.' : 'Error.', res.success ? 'success' : 'error');
				});
			});
		})(jQuery);
		</script>
		<style>
			.dashicons.spin { animation: dashicons-spin 1s linear infinite; }
			@keyframes dashicons-spin { 100% { transform: rotate(360deg); } }
		</style>
		<?php
	}

	/**
	 * AJAX: force-check for update.
	 */
	public function ajax_check_update() {
		check_ajax_referer( 'lawandbeyond_update_nonce', 'nonce' );

		if ( ! current_user_can( 'update_themes' ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		// Clear cache first.
		delete_transient( $this->transient_key );

		// Force a fresh check.
		$release = $this->get_github_release( true );

		if ( ! $release ) {
			wp_send_json_error( __( 'Could not fetch release info from GitHub.', 'lawandbeyond' ) );
		}

		$latest = $this->parse_version( $release->tag_name );

		// Also refresh the core themes transient.
		delete_site_transient( 'update_themes' );
		wp_update_themes();

		if ( version_compare( $latest, $this->current_version, '>' ) ) {
			wp_send_json_success( sprintf(
				/* translators: %s: new version */
				__( 'Update available! Version %s is ready. Reloading…', 'lawandbeyond' ),
				$latest
			) );
		}

		wp_send_json_success( sprintf(
			/* translators: %s: current version */
			__( 'You are running the latest version (%s). Reloading…', 'lawandbeyond' ),
			$this->current_version
		) );
	}

	/**
	 * AJAX: clear cached GitHub data.
	 */
	public function ajax_clear_cache() {
		check_ajax_referer( 'lawandbeyond_update_nonce', 'nonce' );

		if ( ! current_user_can( 'update_themes' ) ) {
			wp_send_json_error();
		}

		delete_transient( $this->transient_key );
		wp_send_json_success();
	}
}
