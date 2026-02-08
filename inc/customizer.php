<?php
/**
 * Customizer settings for Law and Beyond theme.
 *
 * @package LawAndBeyond
 */

function lawandbeyond_customize_register( $wp_customize ) {

	// --- Theme Colors Section ---
	$wp_customize->add_section( 'lawandbeyond_colors', array(
		'title'    => __( 'Theme Colors', 'lawandbeyond' ),
		'priority' => 30,
	) );

	// Primary Color.
	$wp_customize->add_setting( 'lawandbeyond_primary_color', array(
		'default'           => '#e52525',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'lawandbeyond_primary_color', array(
		'label'   => __( 'Primary Color', 'lawandbeyond' ),
		'section' => 'lawandbeyond_colors',
	) ) );

	// Footer Background Color.
	$wp_customize->add_setting( 'lawandbeyond_footer_bg', array(
		'default'           => '#000000',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'lawandbeyond_footer_bg', array(
		'label'   => __( 'Footer Background', 'lawandbeyond' ),
		'section' => 'lawandbeyond_colors',
	) ) );

	// --- Social Links Section ---
	$wp_customize->add_section( 'lawandbeyond_social', array(
		'title'    => __( 'Social Links', 'lawandbeyond' ),
		'priority' => 35,
	) );

	$social_links = array(
		'youtube'   => __( 'YouTube URL', 'lawandbeyond' ),
		'twitter'   => __( 'Twitter/X URL', 'lawandbeyond' ),
		'linkedin'  => __( 'LinkedIn URL', 'lawandbeyond' ),
		'facebook'  => __( 'Facebook URL', 'lawandbeyond' ),
		'instagram' => __( 'Instagram URL', 'lawandbeyond' ),
	);

	foreach ( $social_links as $key => $label ) {
		$wp_customize->add_setting( "lawandbeyond_social_{$key}", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( "lawandbeyond_social_{$key}", array(
			'label'   => $label,
			'section' => 'lawandbeyond_social',
			'type'    => 'url',
		) );
	}

	// --- Front Page Sections ---
	$wp_customize->add_section( 'lawandbeyond_front_page', array(
		'title'    => __( 'Front Page Sections', 'lawandbeyond' ),
		'priority' => 40,
	) );

	// Number of top stories.
	$wp_customize->add_setting( 'lawandbeyond_top_stories_count', array(
		'default'           => 7,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'lawandbeyond_top_stories_count', array(
		'label'   => __( 'Number of Top Stories', 'lawandbeyond' ),
		'section' => 'lawandbeyond_front_page',
		'type'    => 'number',
		'input_attrs' => array( 'min' => 1, 'max' => 13 ),
	) );

	// Category sections (up to 5).
	for ( $i = 1; $i <= 5; $i++ ) {
		// Section title.
		$wp_customize->add_setting( "lawandbeyond_section_{$i}_title", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( "lawandbeyond_section_{$i}_title", array(
			'label'       => sprintf( __( 'Section %d Title (optional)', 'lawandbeyond' ), $i ),
			'description' => __( 'Leave blank to use the category name.', 'lawandbeyond' ),
			'section'     => 'lawandbeyond_front_page',
			'type'        => 'text',
		) );

		// Section category — stored as slug.
		$wp_customize->add_setting( "lawandbeyond_section_{$i}_category", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( "lawandbeyond_section_{$i}_category", array(
			'label'   => sprintf( __( 'Section %d Category', 'lawandbeyond' ), $i ),
			'section' => 'lawandbeyond_front_page',
			'type'    => 'select',
			'choices' => lawandbeyond_get_categories_choices(),
		) );

		$wp_customize->add_setting( "lawandbeyond_section_{$i}_count", array(
			'default'           => 8,
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( "lawandbeyond_section_{$i}_count", array(
			'label'       => sprintf( __( 'Section %d Post Count', 'lawandbeyond' ), $i ),
			'section'     => 'lawandbeyond_front_page',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 2, 'max' => 12, 'step' => 2 ),
		) );
	}

	// --- Sidebar Sections ---
	$wp_customize->add_section( 'lawandbeyond_sidebar_sections', array(
		'title'    => __( 'Sidebar Sections', 'lawandbeyond' ),
		'priority' => 45,
	) );

	// Opinion Section
	$wp_customize->add_setting( 'lawandbeyond_sidebar_opinion_title', array(
		'default'           => 'Opinion',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'lawandbeyond_sidebar_opinion_title', array(
		'label'   => __( 'Opinion Section Title', 'lawandbeyond' ),
		'section' => 'lawandbeyond_sidebar_sections',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'lawandbeyond_sidebar_opinion_category', array(
		'default'           => 'opinion',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'lawandbeyond_sidebar_opinion_category', array(
		'label'   => __( 'Opinion Category', 'lawandbeyond' ),
		'section' => 'lawandbeyond_sidebar_sections',
		'type'    => 'select',
		'choices' => lawandbeyond_get_categories_choices(),
	) );

	$wp_customize->add_setting( 'lawandbeyond_sidebar_opinion_count', array(
		'default'           => 3,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'lawandbeyond_sidebar_opinion_count', array(
		'label'       => __( 'Opinion Posts Count', 'lawandbeyond' ),
		'section'     => 'lawandbeyond_sidebar_sections',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 1, 'max' => 6 ),
	) );

	// Monthly Recap Section
	$wp_customize->add_setting( 'lawandbeyond_sidebar_recap_title', array(
		'default'           => 'Monthly Recap',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'lawandbeyond_sidebar_recap_title', array(
		'label'   => __( 'Monthly Recap Section Title', 'lawandbeyond' ),
		'section' => 'lawandbeyond_sidebar_sections',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'lawandbeyond_sidebar_recap_category', array(
		'default'           => 'monthly-recap',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'lawandbeyond_sidebar_recap_category', array(
		'label'   => __( 'Monthly Recap Category', 'lawandbeyond' ),
		'section' => 'lawandbeyond_sidebar_sections',
		'type'    => 'select',
		'choices' => lawandbeyond_get_categories_choices(),
	) );

	$wp_customize->add_setting( 'lawandbeyond_sidebar_recap_count', array(
		'default'           => 3,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'lawandbeyond_sidebar_recap_count', array(
		'label'       => __( 'Monthly Recap Posts Count', 'lawandbeyond' ),
		'section'     => 'lawandbeyond_sidebar_sections',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 1, 'max' => 6 ),
	) );

	// --- Copyright Text ---
	$wp_customize->add_setting( 'lawandbeyond_copyright', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'lawandbeyond_copyright', array(
		'label'   => __( 'Copyright Text', 'lawandbeyond' ),
		'section' => 'title_tagline',
		'type'    => 'textarea',
	) );
}
add_action( 'customize_register', 'lawandbeyond_customize_register' );

/**
 * Get category choices for Customizer dropdowns.
 */
function lawandbeyond_get_categories_choices() {
	$choices    = array( '' => __( '— Select —', 'lawandbeyond' ) );
	$categories = get_categories( array( 'hide_empty' => false ) );
	foreach ( $categories as $cat ) {
		$choices[ $cat->slug ] = $cat->name;
	}
	return $choices;
}

/**
 * Output custom CSS variables from Customizer.
 */
function lawandbeyond_customizer_css() {
	$primary   = get_theme_mod( 'lawandbeyond_primary_color', '#e52525' );
	$footer_bg = get_theme_mod( 'lawandbeyond_footer_bg', '#000000' );
	?>
	<style type="text/css" id="lawandbeyond-custom-colors">
		:root {
			--lab-primary-color: <?php echo esc_attr( $primary ); ?>;
			--lab-footer-bg: <?php echo esc_attr( $footer_bg ); ?>;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'lawandbeyond_customizer_css', 25 );
