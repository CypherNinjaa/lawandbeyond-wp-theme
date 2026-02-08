<?php
/**
 * Template part: Post card used in grids.
 *
 * Displays a post card with thumbnail, category badge, title, and meta.
 *
 * @package LawAndBeyond
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>

	<!-- Post Thumbnail -->
	<div class="post-img">
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php the_permalink(); ?>">
				<?php
				the_post_thumbnail(
					'lawandbeyond-card',
					array( 'alt' => the_title_attribute( array( 'echo' => false ) ) )
				);
				?>
			</a>
		<?php endif; ?>

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
	</div>

	<!-- Post Content -->
	<div class="portfolio-content">
		<h4>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h4>

		<div class="item-metadata">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), 16 ); ?>
			<span class="author">
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
					<?php the_author(); ?>
				</a>
			</span>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
		</div>
	</div>

</article>
