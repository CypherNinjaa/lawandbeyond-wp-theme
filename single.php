<?php
/**
 * The template for displaying single posts.
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

				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<!-- Post Header -->
						<header class="single-post-header">
							<?php
							$categories = get_the_category();
							if ( ! empty( $categories ) ) :
								?>
								<div class="post-widget-categories">
									<a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
										<?php echo esc_html( $categories[0]->name ); ?>
									</a>
								</div>
							<?php endif; ?>

							<h1 class="entry-title"><?php the_title(); ?></h1>

							<div class="item-metadata">
								<?php echo get_avatar( get_the_author_meta( 'ID' ), 30 ); ?>
								<span class="author">
									<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
										<?php the_author(); ?>
									</a>
								</span>
								<span class="post-date">
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
										<?php echo esc_html( get_the_date() ); ?>
									</time>
								</span>
								<span class="reading-time">
									<i class="fa-regular fa-clock"></i>
									<?php echo lawandbeyond_reading_time(); ?>
								</span>
							</div>
						</header>

						<!-- Featured Image -->
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="post-thumbnail-single">
								<?php
								the_post_thumbnail(
									'lawandbeyond-featured',
									array( 'alt' => the_title_attribute( array( 'echo' => false ) ) )
								);
								?>
							</div>
						<?php endif; ?>

						<!-- Post Content -->
						<div class="entry-content">
							<?php
							the_content(
								sprintf(
									/* translators: %s: Post title. */
									esc_html__( 'Continue reading %s', 'lawandbeyond' ),
									'<span class="screen-reader-text">' . get_the_title() . '</span>'
								)
							);

							wp_link_pages(
								array(
									'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'lawandbeyond' ),
									'after'  => '</div>',
								)
							);
							?>
						</div>

						<!-- Post Footer / Tags -->
						<footer class="entry-footer">
							<?php
							$tags_list = get_the_tag_list( '', ', ' );
							if ( $tags_list ) :
								?>
								<div class="tag-links">
									<span class="tags-title"><?php esc_html_e( 'Tags:', 'lawandbeyond' ); ?></span>
									<?php echo wp_kses_post( $tags_list ); ?>
								</div>
							<?php endif; ?>

							<?php lawandbeyond_share_buttons(); ?>
						</footer>

					</article>

					<!-- Author Box -->
					<div class="author-box">
						<div class="author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
						</div>
						<div class="author-info">
							<h4><?php the_author(); ?></h4>
							<p><?php the_author_meta( 'description' ); ?></p>
						</div>
					</div>

					<!-- Post Navigation -->
					<?php
					the_post_navigation(
						array(
							'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'lawandbeyond' ) . '</span> <span class="nav-title">%title</span>',
							'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'lawandbeyond' ) . '</span> <span class="nav-title">%title</span>',
						)
					);
					?>

					<!-- Related Posts -->
					<?php
					$related_cats = wp_get_post_categories( get_the_ID() );
					if ( ! empty( $related_cats ) ) :
						$related_query = new WP_Query(
							array(
								'category__in'   => $related_cats,
								'post__not_in'   => array( get_the_ID() ),
								'posts_per_page' => 4,
								'no_found_rows'  => true,
								'post_status'    => 'publish',
							)
						);
						if ( $related_query->have_posts() ) :
							?>
							<section class="category-section related-posts">
								<div class="mag-sec-title">
									<h2 class="post-widget-title"><?php esc_html_e( 'Related Posts', 'lawandbeyond' ); ?></h2>
								</div>
								<div class="post-grid">
									<?php
									while ( $related_query->have_posts() ) :
										$related_query->the_post();
										get_template_part( 'template-parts/content' );
									endwhile;
									?>
								</div>
							</section>
							<?php
							wp_reset_postdata();
						endif;
					endif;
					?>

					<!-- Comments -->
					<?php
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
					?>

				<?php endwhile; ?>

			</main>

			<?php get_sidebar(); ?>
		</div>
	</div>
</div>

<?php
get_footer();
