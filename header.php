<?php
/**
 * The header for the Law and Beyond theme.
 *
 * Displays the top bar, site branding, navigation, and mobile menu.
 *
 * @package LawAndBeyond
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'lawandbeyond' ); ?></a>

	<!-- Top Bar -->
	<div class="top-bar-menu">
		<div class="container">
			<div class="left-menu">
				<span class="header-date"></span>
				<span class="header-clock"></span>
			</div>
			<div class="right-menu">
				<button type="button" class="topbar-subscribe-btn lab-push-subscribe-btn" style="display:none;" data-state="idle" aria-label="<?php esc_attr_e( 'Subscribe to notifications', 'lawandbeyond' ); ?>">
					<i class="fa-solid fa-bell"></i>
					<span><?php esc_html_e( 'Subscribe', 'lawandbeyond' ); ?></span>
				</button>
				<?php if ( function_exists( 'lawandbeyond_social_links' ) ) : ?>
					<?php lawandbeyond_social_links(); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Site Header -->
	<header id="masthead" class="site-header" role="banner">

		<!-- Main Header Row (logo + nav + search in one line) -->
		<div class="main-header-row">
			<div class="container">
				<?php lawandbeyond_site_branding(); ?>

				<nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'lawandbeyond' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_class'     => 'menu',
							'container'      => false,
							'fallback_cb'    => false,
							'depth'          => 2,
						)
					);
					?>
				</nav>

				<div class="header-right">
					<button class="header-search-icon" aria-label="<?php esc_attr_e( 'Search', 'lawandbeyond' ); ?>">
						<i class="fa-solid fa-magnifying-glass"></i>
					</button>
					<div class="header-search-form">
						<?php get_search_form(); ?>
						<div class="live-search-results"></div>
					</div>
				</div>
			</div>
		</div>

		<!-- Trending Topics (below nav) -->
		<?php if ( function_exists( 'lawandbeyond_trending_topics' ) ) : ?>
			<div class="trending-bar">
				<div class="container">
					<?php lawandbeyond_trending_topics(); ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- Mobile Header -->
		<div class="mobile-header">
			<div class="container" style="display:flex;justify-content:space-between;align-items:center;padding:10px 15px;">
				<?php lawandbeyond_site_branding(); ?>
				<button id="menu-opener" aria-label="<?php esc_attr_e( 'Open Menu', 'lawandbeyond' ); ?>">
					<span></span>
					<span></span>
					<span></span>
				</button>
			</div>
		</div>
	</header>

	<!-- Mobile Menu Overlay -->
	<div class="mobile-menu-overlay"></div>

	<!-- Mobile Menu Drawer -->
	<div class="mobile-menu-wrapper">
		<button class="close-mobile-menu" aria-label="<?php esc_attr_e( 'Close Menu', 'lawandbeyond' ); ?>">&times;</button>
		<nav class="mobile-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Mobile Menu', 'lawandbeyond' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'mobile',
					'menu_class'     => 'menu',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 2,
				)
			);
			?>
		</nav>
	</div>

	<div id="content" class="site-content">
