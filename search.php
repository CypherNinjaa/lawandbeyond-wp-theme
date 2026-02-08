<?php
/**
 * The template for displaying search results.
 *
 * @package LawAndBeyond
 */

get_header();
?>

<div id="primary" class="content-area">
	<div class="container">

		<?php lawandbeyond_breadcrumbs(); ?>

		<div class="page-grid">
			<main class="site-main" role="main">

				<?php if ( have_posts() ) : ?>

					<header class="search-results-header archive-header">
						<h1 class="page-title">
							<?php
							printf(
								/* translators: %s: search query */
								esc_html__( 'Search Results for: %s', 'lawandbeyond' ),
								'<span>' . get_search_query() . '</span>'
							);
							?>
						</h1>
					</header>

					<div class="post-grid">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content' );
						endwhile;
						?>
					</div>

					<?php lawandbeyond_pagination(); ?>

				<?php else : ?>

					<?php get_template_part( 'template-parts/content', 'none' ); ?>

				<?php endif; ?>

			</main>

			<?php get_sidebar(); ?>
		</div>
	</div>
</div>

<?php
get_footer();
