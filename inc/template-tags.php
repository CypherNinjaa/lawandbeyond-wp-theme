<?php
/**
 * Template tags for Law and Beyond theme.
 *
 * @package LawAndBeyond
 */

/**
 * Display social links from Customizer settings.
 */
function lawandbeyond_social_links() {
	$socials = array(
		'youtube'  => array( 'icon' => 'fa-youtube',  'label' => 'YouTube' ),
		'twitter'  => array( 'icon' => 'fa-x-twitter', 'label' => 'Twitter' ),
		'linkedin' => array( 'icon' => 'fa-linkedin',  'label' => 'LinkedIn' ),
		'facebook' => array( 'icon' => 'fa-facebook-f','label' => 'Facebook' ),
	);

	echo '<ul class="social-links">';
	foreach ( $socials as $key => $data ) {
		$url = get_theme_mod( "lawandbeyond_social_{$key}", '' );
		if ( $url ) {
			printf(
				'<li><a href="%s" target="_blank" rel="nofollow noopener" aria-label="%s"><i class="fa-brands %s"></i></a></li>',
				esc_url( $url ),
				esc_attr( $data['label'] ),
				esc_attr( $data['icon'] )
			);
		}
	}
	echo '</ul>';
}

/**
 * Display post meta (author, date, category).
 *
 * @param bool $show_category Whether to show category.
 */
function lawandbeyond_post_meta( $show_category = true ) {
	?>
	<div class="item-metadata">
		<?php echo get_avatar( get_the_author_meta( 'ID' ), 16 ); ?>
		<span class="author">
			<i class="fa fa-user-o" aria-hidden="true"></i>
			<?php the_author(); ?>
		</span>
		<span class="posts-date">
			<i class="fa fa-clock-o" aria-hidden="true"></i>
			<?php echo esc_html( get_the_date( 'M d, Y' ) ); ?>
		</span>
		<?php if ( $show_category ) : ?>
			<span class="post-categories">
				<?php the_category( ', ' ); ?>
			</span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Pagination for archive pages.
 */
function lawandbeyond_pagination() {
	the_posts_pagination( array(
		'mid_size'  => 2,
		'prev_text' => '<i class="fa fa-chevron-left"></i> ' . esc_html__( 'Previous', 'lawandbeyond' ),
		'next_text' => esc_html__( 'Next', 'lawandbeyond' ) . ' <i class="fa fa-chevron-right"></i>',
	) );
}

/**
 * Display the site logo or title.
 */
function lawandbeyond_site_branding() {
	?>
	<div class="site-branding">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php endif; ?>
		<div class="site-title-logo">
			<?php if ( is_front_page() && is_home() ) : ?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<?php else : ?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Display breadcrumbs (Home > Category > Post Title).
 */
function lawandbeyond_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	?>
	<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'lawandbeyond' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'lawandbeyond' ); ?></a>
		<span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
		<?php
		if ( is_single() ) :
			$categories = get_the_category();
			if ( ! empty( $categories ) ) :
				$cat = $categories[0];
				?>
				<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
				<span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
				<?php
			endif;
			?>
			<span class="breadcrumb-current"><?php the_title(); ?></span>
		<?php elseif ( is_category() ) : ?>
			<span class="breadcrumb-current"><?php single_cat_title(); ?></span>
		<?php elseif ( is_tag() ) : ?>
			<span class="breadcrumb-current"><?php single_tag_title(); ?></span>
		<?php elseif ( is_search() ) : ?>
			<span class="breadcrumb-current"><?php printf( esc_html__( 'Search: %s', 'lawandbeyond' ), get_search_query() ); ?></span>
		<?php elseif ( is_page() ) : ?>
			<span class="breadcrumb-current"><?php the_title(); ?></span>
		<?php elseif ( is_404() ) : ?>
			<span class="breadcrumb-current"><?php esc_html_e( '404 Not Found', 'lawandbeyond' ); ?></span>
		<?php elseif ( is_archive() ) : ?>
			<span class="breadcrumb-current"><?php the_archive_title(); ?></span>
		<?php endif; ?>
	</nav>
	<?php
}

/**
 * Calculate estimated reading time for a post.
 *
 * @param  int $post_id Optional post ID. Falls back to current post.
 * @return string       Human-readable reading time.
 */
function lawandbeyond_reading_time( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$content    = get_post_field( 'post_content', $post_id );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	$minutes    = floor( $word_count / 200 );
	$seconds    = floor( ( $word_count % 200 ) / ( 200 / 60 ) );

	if ( $minutes < 1 ) {
		return esc_html__( 'Less than 1 min read', 'lawandbeyond' );
	}

	if ( $seconds > 0 ) {
		return sprintf(
			/* translators: 1: minutes, 2: seconds */
			esc_html__( '%1$d min, %2$d sec read', 'lawandbeyond' ),
			$minutes,
			$seconds
		);
	}
	return sprintf(
		/* translators: %d: minutes */
		esc_html( _n( '%d min read', '%d min read', $minutes, 'lawandbeyond' ) ),
		$minutes
	);
}

/**
 * Display share buttons for the current post (Twitter/X and Facebook).
 */
function lawandbeyond_share_buttons() {
	$url   = urlencode( get_the_permalink() );
	$title = urlencode( get_the_title() );
	?>
	<div class="share-buttons">
		<span class="share-label"><?php esc_html_e( 'Share this:', 'lawandbeyond' ); ?></span>
		<a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>"
		   target="_blank" rel="nofollow noopener" class="share-btn share-twitter"
		   aria-label="<?php esc_attr_e( 'Share on X (Twitter)', 'lawandbeyond' ); ?>">
			<i class="fa-brands fa-x-twitter"></i>
		</a>
		<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>"
		   target="_blank" rel="nofollow noopener" class="share-btn share-facebook"
		   aria-label="<?php esc_attr_e( 'Share on Facebook', 'lawandbeyond' ); ?>">
			<i class="fa-brands fa-facebook-f"></i>
		</a>
		<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>"
		   target="_blank" rel="nofollow noopener" class="share-btn share-linkedin"
		   aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'lawandbeyond' ); ?>">
			<i class="fa-brands fa-linkedin-in"></i>
		</a>
		<a href="https://api.whatsapp.com/send?text=<?php echo $title . '%20' . $url; ?>"
		   target="_blank" rel="nofollow noopener" class="share-btn share-whatsapp"
		   aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'lawandbeyond' ); ?>">
			<i class="fa-brands fa-whatsapp"></i>
		</a>
	</div>
	<?php
}

/**
 * Display a sidebar section with thumbnail posts (e.g., Opinion, Monthly Recap).
 *
 * @param string $cat_slug  Category slug.
 * @param string $title     Section title.
 * @param int    $count     Number of posts to show.
 */
function lawandbeyond_sidebar_section( $cat_slug, $title, $count = 3 ) {
	if ( empty( $cat_slug ) ) {
		return;
	}

	$cat_obj = get_category_by_slug( $cat_slug );
	if ( ! $cat_obj ) {
		return;
	}

	$section_query = new WP_Query( array(
		'category_name'  => $cat_slug,
		'posts_per_page' => intval( $count ),
		'post_status'    => 'publish',
		'no_found_rows'  => true,
	) );

	if ( ! $section_query->have_posts() ) {
		wp_reset_postdata();
		return;
	}

	$cat_link = get_category_link( $cat_obj->term_id );
	?>
	<section class="widget sidebar-section-widget">
		<h3 class="widget-title">
			<a href="<?php echo esc_url( $cat_link ); ?>">
				<?php echo esc_html( $title ); ?> <span class="widget-title-arrow">&rsaquo;</span>
			</a>
		</h3>
		<ul class="sidebar-post-list">
			<?php while ( $section_query->have_posts() ) : $section_query->the_post(); ?>
				<li class="sidebar-post-item">
					<a href="<?php the_permalink(); ?>" class="sidebar-post-link">
						<?php if ( has_post_thumbnail() ) : ?>
							<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>" alt="<?php the_title_attribute(); ?>" class="sidebar-post-thumb">
						<?php else : ?>
							<span class="sidebar-post-no-thumb"><i class="fa-solid fa-newspaper"></i></span>
						<?php endif; ?>
						<span class="sidebar-post-title"><?php the_title(); ?></span>
					</a>
				</li>
			<?php endwhile; ?>
		</ul>
	</section>
	<?php
	wp_reset_postdata();
}

/**
 * Display "Discover more" trending topics bar in the header.
 */
function lawandbeyond_trending_topics() {
	$categories = get_categories( array(
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 5,
		'hide_empty' => true,
	) );

	if ( empty( $categories ) ) {
		return;
	}
	?>
	<div class="trending-topics">
		<span class="trending-label"><?php esc_html_e( 'Discover more', 'lawandbeyond' ); ?></span>
		<?php foreach ( $categories as $cat ) : ?>
			<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
		<?php endforeach; ?>
	</div>
	<?php
}
