<?php
/**
 * The template for displaying archive pages (category, tag, date, author).
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

					<header class="archive-header">
						<?php
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="archive-description">', '</div>' );
						?>
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
