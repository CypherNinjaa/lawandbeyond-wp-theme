<?php
/**
 * Template part: Top Stories section for the front page.
 *
 * Displays a hero grid with one main featured post and six side posts.
 *
 * @package LawAndBeyond
 */

$top_count = get_theme_mod( 'lawandbeyond_top_stories_count', 7 );

$top_query = new WP_Query(
	array(
		'posts_per_page' => intval( $top_count ),
		'post_status'    => 'publish',
		'no_found_rows'  => true,
		'meta_key'       => '_is_ns_featured_post',
		'meta_value'     => 'yes',
	)
);

// If no sticky/featured posts, fall back to latest.
if ( ! $top_query->have_posts() ) {
	$top_query = new WP_Query(
		array(
			'posts_per_page' => intval( $top_count ),
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		)
	);
}

if ( ! $top_query->have_posts() ) {
	return;
}

$posts_array = array();
while ( $top_query->have_posts() ) {
	$top_query->the_post();
	$posts_array[] = array(
		'id'        => get_the_ID(),
		'title'     => get_the_title(),
		'permalink' => get_the_permalink(),
		'thumb'     => get_the_post_thumbnail_url( get_the_ID(), 'lawandbeyond-featured' ),
		'thumb_sm'  => get_the_post_thumbnail_url( get_the_ID(), 'lawandbeyond-side-thumb' ),
	);
}
wp_reset_postdata();

$main_post  = $posts_array[0];
$side_posts = array_slice( $posts_array, 1 );
?>

<section class="top-news-today">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'Top Stories', 'lawandbeyond' ); ?></h2>

		<div class="top-stories-grid">
			<!-- Main Featured Post -->
			<div class="top-news-main">
				<?php if ( ! empty( $main_post['thumb'] ) ) : ?>
					<a href="<?php echo esc_url( $main_post['permalink'] ); ?>" class="main-thumb-link">
						<img src="<?php echo esc_url( $main_post['thumb'] ); ?>"
							 alt="<?php echo esc_attr( $main_post['title'] ); ?>"
							 class="main-thumb-img" loading="lazy">
					</a>
				<?php endif; ?>
				<h3 class="main-title">
					<a href="<?php echo esc_url( $main_post['permalink'] ); ?>">
						<?php echo esc_html( $main_post['title'] ); ?>
					</a>
				</h3>
			</div>

			<!-- Side Stories -->
			<?php if ( ! empty( $side_posts ) ) : ?>
				<div class="top-news-side-wrap">
					<?php
					$half  = ceil( count( $side_posts ) / 2 );
					$col_1 = array_slice( $side_posts, 0, $half );
					$col_2 = array_slice( $side_posts, $half );
					?>

					<div class="side-col">
						<?php foreach ( $col_1 as $sp ) : ?>
							<div class="top-news-side-item">
								<a href="<?php echo esc_url( $sp['permalink'] ); ?>" class="top-thumb-link">
									<?php if ( ! empty( $sp['thumb_sm'] ) ) : ?>
										<img src="<?php echo esc_url( $sp['thumb_sm'] ); ?>"
											 alt="<?php echo esc_attr( $sp['title'] ); ?>"
											 class="top-thumb-img" loading="lazy">
									<?php endif; ?>
									<span class="side-title-text"><?php echo esc_html( $sp['title'] ); ?></span>
								</a>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="side-col">
						<?php foreach ( $col_2 as $sp ) : ?>
							<div class="top-news-side-item">
								<a href="<?php echo esc_url( $sp['permalink'] ); ?>" class="top-thumb-link">
									<?php if ( ! empty( $sp['thumb_sm'] ) ) : ?>
										<img src="<?php echo esc_url( $sp['thumb_sm'] ); ?>"
											 alt="<?php echo esc_attr( $sp['title'] ); ?>"
											 class="top-thumb-img" loading="lazy">
									<?php endif; ?>
									<span class="side-title-text"><?php echo esc_html( $sp['title'] ); ?></span>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
