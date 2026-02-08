<?php
/**
 * The sidebar for the Law and Beyond theme.
 *
 * Displays the Opinion section, Monthly Recap section,
 * and any widgets registered to sidebar-1.
 *
 * @package LawAndBeyond
 */
?>

<aside id="secondary" class="sidebar-area widget-area" role="complementary">

	<?php
	// Opinion Section
	$opinion_cat = get_theme_mod( 'lawandbeyond_sidebar_opinion_category', 'opinion' );
	if ( $opinion_cat ) {
		if ( function_exists( 'lawandbeyond_sidebar_section' ) ) {
			lawandbeyond_sidebar_section(
				$opinion_cat,
				get_theme_mod( 'lawandbeyond_sidebar_opinion_title', __( 'Opinion', 'lawandbeyond' ) ),
				intval( get_theme_mod( 'lawandbeyond_sidebar_opinion_count', 3 ) )
			);
		}
	}

	// Monthly Recap Section
	$recap_cat = get_theme_mod( 'lawandbeyond_sidebar_recap_category', 'monthly-recap' );
	if ( $recap_cat ) {
		if ( function_exists( 'lawandbeyond_sidebar_section' ) ) {
			lawandbeyond_sidebar_section(
				$recap_cat,
				get_theme_mod( 'lawandbeyond_sidebar_recap_title', __( 'Monthly Recap', 'lawandbeyond' ) ),
				intval( get_theme_mod( 'lawandbeyond_sidebar_recap_count', 3 ) )
			);
		}
	}
	?>

	<!-- Push Subscribe Card -->
	<section class="widget subscribe-widget" style="display:none;">
		<div class="subscribe-card">
			<div class="subscribe-card__icon">
				<i class="fa-solid fa-bell"></i>
			</div>
			<h3 class="subscribe-card__title"><?php esc_html_e( 'Stay Updated', 'lawandbeyond' ); ?></h3>
			<p class="subscribe-card__text"><?php esc_html_e( 'Get instant push notifications when we publish new legal articles and updates.', 'lawandbeyond' ); ?></p>
			<button type="button" class="lab-push-subscribe-btn" data-state="idle">
				<i class="fa-solid fa-bell"></i>
				<span><?php esc_html_e( 'Subscribe Now', 'lawandbeyond' ); ?></span>
			</button>
			<p class="subscribe-card__note">
				<i class="fa-solid fa-shield-halved"></i>
				<?php esc_html_e( 'Free &bull; No spam &bull; Unsubscribe anytime', 'lawandbeyond' ); ?>
			</p>
		</div>
	</section>

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php endif; ?>

</aside>
