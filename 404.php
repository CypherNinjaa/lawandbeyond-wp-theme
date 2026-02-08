<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package LawAndBeyond
 */

get_header();
?>

<div id="primary" class="content-area">
	<div class="container">
		<div class="page-grid no-sidebar">
			<main class="site-main" role="main">

				<section class="error-404 not-found">
					<h1>404</h1>
					<h2><?php esc_html_e( 'Page Not Found', 'lawandbeyond' ); ?></h2>
					<p><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'lawandbeyond' ); ?></p>

					<?php get_search_form(); ?>
				</section>

			</main>
		</div>
	</div>
</div>

<?php
get_footer();
