<?php
/**
 * The footer for the Law and Beyond theme.
 *
 * @package LawAndBeyond
 */
?>
	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="footer-overlay">
			<div class="container">
				<!-- Footer Menu -->
				<div class="footer-bottom-menu">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'menu_class'     => 'footer-bottom-links',
							'container'      => false,
							'fallback_cb'    => false,
							'depth'          => 1,
						)
					);
					?>
				</div>

				<!-- Footer Info -->
				<div class="footer-info">
					<div class="site-info">
						<?php
						$copyright = get_theme_mod( 'lawandbeyond_copyright', '&copy; ' . date( 'Y' ) . ' Law and Beyond. All rights reserved.' );
						echo wp_kses_post( $copyright );
						?>
					</div>
					<?php if ( function_exists( 'lawandbeyond_social_links' ) ) : ?>
						<?php lawandbeyond_social_links(); ?>
					<?php endif; ?>
				</div>

				<!-- Developer Credit -->
				<div class="developer-credit">
					<?php
					printf(
						/* translators: %s: developer link */
						esc_html__( 'Designed & Developed by %s', 'lawandbeyond' ),
						'<a href="https://github.com/CypherNinjaa" target="_blank" rel="noopener">CypherNinjaa</a>'
					);
					?>
				</div>
			</div>
		</div>
	</footer>

	<!-- Back to Top -->
	<button class="backtotop" aria-label="<?php esc_attr_e( 'Back to top', 'lawandbeyond' ); ?>">
		<i class="fa-solid fa-arrow-up"></i>
	</button>

	<!-- Mobile Bottom Navigation -->
	<nav class="mobile-bottom-nav" role="navigation" aria-label="<?php esc_attr_e( 'Mobile Bottom Navigation', 'lawandbeyond' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-bottom-nav__item <?php echo is_front_page() ? 'active' : ''; ?>">
			<i class="fa-solid fa-house"></i>
			<span><?php esc_html_e( 'Home', 'lawandbeyond' ); ?></span>
		</a>
		<?php
		$latest_cat = get_category_by_slug( 'latest-news' );
		$latest_url = $latest_cat ? get_category_link( $latest_cat->term_id ) : home_url( '/?cat=latest-news' );
		?>
		<a href="<?php echo esc_url( $latest_url ); ?>" class="mobile-bottom-nav__item <?php echo is_category( 'latest-news' ) ? 'active' : ''; ?>">
			<i class="fa-solid fa-bolt"></i>
			<span><?php esc_html_e( 'Latest', 'lawandbeyond' ); ?></span>
		</a>
		<button class="mobile-bottom-nav__item mobile-bottom-search-btn" aria-label="<?php esc_attr_e( 'Search', 'lawandbeyond' ); ?>">
			<i class="fa-solid fa-magnifying-glass"></i>
			<span><?php esc_html_e( 'Search', 'lawandbeyond' ); ?></span>
		</button>
		<?php
		$sc_cat = get_category_by_slug( 'supreme-court' );
		$sc_url = $sc_cat ? get_category_link( $sc_cat->term_id ) : '#';
		?>
		<a href="<?php echo esc_url( $sc_url ); ?>" class="mobile-bottom-nav__item <?php echo is_category( 'supreme-court' ) ? 'active' : ''; ?>">
			<i class="fa-solid fa-gavel"></i>
			<span><?php esc_html_e( 'SC', 'lawandbeyond' ); ?></span>
		</a>
		<button class="mobile-bottom-nav__item" id="mobile-bottom-menu-btn" aria-label="<?php esc_attr_e( 'Menu', 'lawandbeyond' ); ?>">
			<i class="fa-solid fa-bars"></i>
			<span><?php esc_html_e( 'Menu', 'lawandbeyond' ); ?></span>
		</button>
	</nav>

	<!-- Mobile Bottom Search Overlay -->
	<div class="mobile-search-overlay" style="display:none;">
		<div class="mobile-search-overlay__inner">
			<button class="mobile-search-overlay__close" aria-label="<?php esc_attr_e( 'Close Search', 'lawandbeyond' ); ?>">&times;</button>
			<div class="mobile-search-overlay__form">
				<?php get_search_form(); ?>
				<div class="live-search-results mobile-live-search-results"></div>
			</div>
		</div>
	</div>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
