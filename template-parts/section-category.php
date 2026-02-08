<?php
/**
 * Template part: Reusable category section block.
 *
 * Expected variables set before get_template_part():
 *   $args['title']    - Section heading text.
 *   $args['slug']     - Category slug to query.
 *   $args['count']    - Number of posts to display.
 *
 * @package LawAndBeyond
 */

if ( empty( $args['slug'] ) ) {
	return;
}

$cat_obj = get_category_by_slug( $args['slug'] );
if ( ! $cat_obj ) {
	return;
}

$display_title = ! empty( $args['title'] ) ? $args['title'] : $cat_obj->name;
$post_count    = ! empty( $args['count'] ) ? intval( $args['count'] ) : 4;

$sec_query = new WP_Query(
	array(
		'category_name'  => $args['slug'],
		'posts_per_page' => $post_count,
		'post_status'    => 'publish',
		'no_found_rows'  => true,
	)
);

if ( ! $sec_query->have_posts() ) {
	return;
}
?>

<section class="category-section">
	<div class="mag-sec-title">
		<h2 class="post-widget-title">
			<a href="<?php echo esc_url( get_category_link( $cat_obj->term_id ) ); ?>">
				<?php echo esc_html( $display_title ); ?>
			</a>
		</h2>
	</div>

	<div class="post-grid">
		<?php
		while ( $sec_query->have_posts() ) :
			$sec_query->the_post();
			get_template_part( 'template-parts/content' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
