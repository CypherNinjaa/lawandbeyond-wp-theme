<?php
/**
 * The template for displaying pages.
 *
 * @package LawAndBeyond
 */

get_header();
?>

<div id="primary" class="content-area">
	<div class="container">

		<?php lawandbeyond_breadcrumbs(); ?>

		<div class="page-grid no-sidebar">
			<main class="site-main" role="main">

				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content' ); ?>>

						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
						</header>

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="post-thumbnail-single">
								<?php the_post_thumbnail( 'lawandbeyond-featured' ); ?>
							</div>
						<?php endif; ?>

						<div class="entry-content">
							<?php
							the_content();

							wp_link_pages(
								array(
									'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'lawandbeyond' ),
									'after'  => '</div>',
								)
							);
							?>
						</div>

					</article>

					<?php
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
					?>

				<?php endwhile; ?>

			</main>
		</div>
	</div>
</div>

<?php
get_footer();
