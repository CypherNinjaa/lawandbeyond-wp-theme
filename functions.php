<?php
/**
 * Law and Beyond - functions and definitions.
 *
 * @package LawAndBeyond
 * @since 1.0.0
 */

if ( ! defined( 'LAWANDBEYOND_VERSION' ) ) {
	define( 'LAWANDBEYOND_VERSION', '1.0.0' );
}

/**
 * Theme setup.
 */
function lawandbeyond_setup() {
	// Make theme available for translation.
	load_theme_textdomain( 'lawandbeyond', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 720, 530, true );
	add_image_size( 'lawandbeyond-featured', 960, 540, true );
	add_image_size( 'lawandbeyond-thumb', 150, 94, true );

	// Register navigation menus.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'lawandbeyond' ),
		'footer'  => esc_html__( 'Footer Menu', 'lawandbeyond' ),
		'mobile'  => esc_html__( 'Mobile Menu', 'lawandbeyond' ),
	) );

	// HTML5 support.
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Custom logo support.
	add_theme_support( 'custom-logo', array(
		'height'      => 112,
		'width'       => 112,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Custom background support.
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff',
	) );

	// Editor styles.
	add_editor_style( 'assets/css/editor-style.css' );

	// Post formats.
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio' ) );
}
add_action( 'after_setup_theme', 'lawandbeyond_setup' );

/**
 * Set content width.
 */
function lawandbeyond_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'lawandbeyond_content_width', 1280 );
}
add_action( 'after_setup_theme', 'lawandbeyond_content_width', 0 );

/**
 * Register widget areas.
 */
function lawandbeyond_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'lawandbeyond' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here to appear in the right sidebar.', 'lawandbeyond' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget Area', 'lawandbeyond' ),
		'id'            => 'footer-1',
		'description'   => esc_html__( 'Add widgets here to appear in the footer.', 'lawandbeyond' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'lawandbeyond_widgets_init' );

/**
 * Enqueue styles and scripts.
 */
function lawandbeyond_scripts() {
	// Google Fonts: PT Serif + Nunito Sans.
	wp_enqueue_style(
		'lawandbeyond-google-fonts',
		'https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&family=Nunito+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&display=swap',
		array(),
		null
	);

	// Font Awesome 6.
	wp_enqueue_style(
		'font-awesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
		array(),
		'6.4.0'
	);

	// Main theme stylesheet.
	wp_enqueue_style(
		'lawandbeyond-main',
		get_template_directory_uri() . '/assets/css/theme.css',
		array( 'lawandbeyond-google-fonts', 'font-awesome' ),
		LAWANDBEYOND_VERSION
	);

	// style.css for theme identification.
	wp_enqueue_style(
		'lawandbeyond-style',
		get_stylesheet_uri(),
		array( 'lawandbeyond-main' ),
		LAWANDBEYOND_VERSION
	);

	// Theme JS.
	wp_enqueue_script(
		'lawandbeyond-script',
		get_template_directory_uri() . '/assets/js/theme.js',
		array( 'jquery' ),
		LAWANDBEYOND_VERSION,
		true
	);

	// Comment reply script.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'lawandbeyond_scripts' );

/**
 * Localize script data for AJAX live search.
 */
function lawandbeyond_localize_scripts() {
	wp_localize_script( 'lawandbeyond-script', 'lawandbeyondAjax', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'lawandbeyond_search_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'lawandbeyond_localize_scripts', 20 );

/**
 * AJAX handler for realtime search.
 */
function lawandbeyond_live_search() {
	check_ajax_referer( 'lawandbeyond_search_nonce', 'nonce' );

	$query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';

	if ( strlen( $query ) < 2 ) {
		wp_send_json_success( array( 'html' => '' ) );
	}

	$search_query = new WP_Query( array(
		's'              => $query,
		'posts_per_page' => 6,
		'post_status'    => 'publish',
		'post_type'      => 'post',
		'no_found_rows'  => true,
	) );

	ob_start();

	if ( $search_query->have_posts() ) {
		while ( $search_query->have_posts() ) {
			$search_query->the_post();
			$thumb = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
			$cats  = get_the_category();
			$cat_name = ! empty( $cats ) ? $cats[0]->name : '';
			?>
			<a href="<?php the_permalink(); ?>" class="live-search-item">
				<?php if ( $thumb ) : ?>
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" class="live-search-thumb">
				<?php else : ?>
					<span class="live-search-no-thumb"><i class="fa-solid fa-newspaper"></i></span>
				<?php endif; ?>
				<div class="live-search-info">
					<?php if ( $cat_name ) : ?>
						<span class="live-search-cat"><?php echo esc_html( $cat_name ); ?></span>
					<?php endif; ?>
					<span class="live-search-title"><?php the_title(); ?></span>
				</div>
			</a>
			<?php
		}
		wp_reset_postdata();

		// "View all" link
		echo '<a href="' . esc_url( home_url( '/?s=' . urlencode( $query ) ) ) . '" class="live-search-view-all">View all results &rarr;</a>';
	} else {
		echo '<div class="live-search-no-results">No results found for &ldquo;' . esc_html( $query ) . '&rdquo;</div>';
	}

	$html = ob_get_clean();
	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_lawandbeyond_live_search', 'lawandbeyond_live_search' );
add_action( 'wp_ajax_nopriv_lawandbeyond_live_search', 'lawandbeyond_live_search' );

/**
 * Custom excerpt length.
 */
function lawandbeyond_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'lawandbeyond_excerpt_length' );

/**
 * Custom excerpt more text.
 */
function lawandbeyond_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'lawandbeyond_excerpt_more' );

/**
 * Custom comment walker is not needed; use default.
 * Add body classes.
 */
function lawandbeyond_body_classes( $classes ) {
	if ( is_singular() ) {
		$classes[] = 'singular';
	}
	if ( is_active_sidebar( 'sidebar-1' ) && ! is_page_template( 'page-full-width.php' ) ) {
		$classes[] = 'rightsidebar';
	}
	return $classes;
}
add_filter( 'body_class', 'lawandbeyond_body_classes' );

/**
 * Include Customizer settings.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Include template tags.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Default menus, categories, and pages on theme activation.
 */
require get_template_directory() . '/inc/default-setup.php';

/**
 * Demo content importer (admin page).
 */
require get_template_directory() . '/inc/demo-content.php';
