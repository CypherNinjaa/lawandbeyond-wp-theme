<?php
/**
 * The main template file (fallback).
 *
 * Used when no more-specific template matches. Displays a standard
 * blog listing with sidebar.
 *
 * @package LawAndBeyond
 */

get_header();
?>

<div id="primary" class="content-area">
	<div class="container">
		<div class="page-grid">
			<main class="site-main" role="main">

				<?php if ( have_posts() ) : ?>

					<?php if ( is_home() && ! is_front_page() ) : ?>
						<header class="archive-header">
							<h1 class="page-title"><?php single_post_title(); ?></h1>
						</header>
					<?php endif; ?>

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
