<?php
/**
 * Default theme setup — runs on theme activation.
 *
 * Creates default categories, menus, and pages so the admin sees
 * a ready-to-use site matching the intended structure.
 *
 * @package LawAndBeyond
 */

/**
 * Run all first-time setup tasks on theme switch.
 */
function lawandbeyond_theme_activation() {
	lawandbeyond_create_default_categories();
	lawandbeyond_create_default_pages();
	lawandbeyond_create_default_menus();
	lawandbeyond_set_default_customizer_options();

	// Set front page to show latest posts (blog-style).
	update_option( 'show_on_front', 'posts' );
}
add_action( 'after_switch_theme', 'lawandbeyond_theme_activation' );

/**
 * Create the categories that match the original site structure.
 */
function lawandbeyond_create_default_categories() {
	$categories = array(
		array(
			'name' => 'Latest News',
			'slug' => 'latest-news',
			'desc' => 'Breaking legal news and recent updates.',
		),
		array(
			'name' => 'High Court',
			'slug' => 'high-court',
			'desc' => 'News and rulings from various High Courts across India.',
		),
		array(
			'name' => 'Supreme Court',
			'slug' => 'supreme-court',
			'desc' => 'Supreme Court of India judgments, orders, and analysis.',
		),
		array(
			'name' => 'Know Your Courts',
			'slug' => 'know-your-courts',
			'desc' => 'Educational content about Indian courts and legal system.',
		),
		array(
			'name' => 'Legal Updates',
			'slug' => 'legal-updates',
			'desc' => 'Important legal updates, amendments, and notifications.',
		),
		array(
			'name' => 'Monthly Recap',
			'slug' => 'monthly-recap',
			'desc' => 'Monthly summary of significant legal developments.',
		),
		array(
			'name' => 'Blog',
			'slug' => 'blog',
			'desc' => 'Opinion pieces, analysis, and legal commentary.',
		),
		array(
			'name' => 'Other Courts',
			'slug' => 'other-courts',
			'desc' => 'News from District Courts, Tribunals, and other judicial bodies.',
		),
		array(
			'name' => 'Opinion',
			'slug' => 'opinion',
			'desc' => 'Opinion pieces, analysis, and legal commentary.',
		),
	);

	foreach ( $categories as $cat ) {
		if ( ! term_exists( $cat['slug'], 'category' ) ) {
			wp_insert_term(
				$cat['name'],
				'category',
				array(
					'slug'        => $cat['slug'],
					'description' => $cat['desc'],
				)
			);
		}
	}
}

/**
 * Create default pages that appear in the footer menu.
 */
function lawandbeyond_create_default_pages() {
	$pages = array(
		array(
			'title'   => 'About Us',
			'slug'    => 'about-us',
			'content' => '<!-- wp:paragraph --><p>Welcome to Law and Beyond — your trusted source for Indian legal news, Supreme Court and High Court updates, and expert legal analysis.</p><!-- /wp:paragraph -->',
		),
		array(
			'title'   => 'Contact Us',
			'slug'    => 'contact-us',
			'content' => '<!-- wp:paragraph --><p>Have a question or tip? Reach out to us.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p><strong>Email:</strong> contact@lawandbeyond.in</p><!-- /wp:paragraph -->',
		),
		array(
			'title'   => 'Privacy Policy',
			'slug'    => 'privacy-policy',
			'content' => '<!-- wp:paragraph --><p>Your privacy is important to us. This page explains what data we collect and how we use it.</p><!-- /wp:paragraph -->',
		),
		array(
			'title'   => 'Terms and Conditions',
			'slug'    => 'terms-and-conditions',
			'content' => '<!-- wp:paragraph --><p>By using this website, you agree to the following terms and conditions.</p><!-- /wp:paragraph -->',
		),
		array(
			'title'   => 'Disclaimer',
			'slug'    => 'disclaimer',
			'content' => '<!-- wp:paragraph --><p>The information provided on this website is for general informational purposes only and should not be construed as legal advice.</p><!-- /wp:paragraph -->',
		),
		array(
			'title'   => 'Refunds and Cancellation Policy',
			'slug'    => 'refunds-and-cancellation-policy',
			'content' => '<!-- wp:paragraph --><p>Our refund and cancellation policy details.</p><!-- /wp:paragraph -->',
		),
	);

	foreach ( $pages as $page_data ) {
		// Skip if a page with this slug already exists.
		$existing = get_page_by_path( $page_data['slug'] );
		if ( $existing ) {
			continue;
		}

		wp_insert_post( array(
			'post_title'   => $page_data['title'],
			'post_name'    => $page_data['slug'],
			'post_content' => $page_data['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
		) );
	}
}

/**
 * Create and assign default navigation menus.
 *
 * Primary Menu:  Latest News | High Court | Supreme Court | Know Your Courts (sub: Legal Updates, Monthly Recap, Blog)
 * Footer  Menu:  Refunds Policy | About Us | Contact Us | Disclaimer | Terms and Conditions | Privacy Policy
 * Mobile  Menu:  Same as Primary.
 */
function lawandbeyond_create_default_menus() {
	// ── Primary Menu ───────────────────────────────────────────────
	$primary_menu_name = 'Primary Menu';
	$primary_menu      = wp_get_nav_menu_object( $primary_menu_name );

	if ( ! $primary_menu ) {
		$primary_menu_id = wp_create_nav_menu( $primary_menu_name );

		// Category items.
		$cat_items = array(
			'latest-news'     => 'Latest News',
			'high-court'      => 'High Court',
			'supreme-court'   => 'Supreme Court',
		);

		foreach ( $cat_items as $slug => $title ) {
			$cat = get_category_by_slug( $slug );
			if ( $cat ) {
				wp_update_nav_menu_item( $primary_menu_id, 0, array(
					'menu-item-title'     => $title,
					'menu-item-object'    => 'category',
					'menu-item-object-id' => $cat->term_id,
					'menu-item-type'      => 'taxonomy',
					'menu-item-status'    => 'publish',
				) );
			}
		}

		// Parent: Know Your Courts.
		$kyc_cat = get_category_by_slug( 'know-your-courts' );
		$kyc_menu_item_id = 0;
		if ( $kyc_cat ) {
			$kyc_menu_item_id = wp_update_nav_menu_item( $primary_menu_id, 0, array(
				'menu-item-title'     => 'Know Your Courts',
				'menu-item-object'    => 'category',
				'menu-item-object-id' => $kyc_cat->term_id,
				'menu-item-type'      => 'taxonomy',
				'menu-item-status'    => 'publish',
			) );
		}

		// Sub-items under Know Your Courts.
		$sub_cats = array(
			'legal-updates' => 'Legal Updates',
			'monthly-recap' => 'Monthly Recap',
			'blog'          => 'Blog',
		);

		foreach ( $sub_cats as $slug => $title ) {
			$cat = get_category_by_slug( $slug );
			if ( $cat && $kyc_menu_item_id ) {
				wp_update_nav_menu_item( $primary_menu_id, 0, array(
					'menu-item-title'          => $title,
					'menu-item-object'         => 'category',
					'menu-item-object-id'      => $cat->term_id,
					'menu-item-type'           => 'taxonomy',
					'menu-item-parent-id'      => $kyc_menu_item_id,
					'menu-item-status'         => 'publish',
				) );
			}
		}

		// Assign to theme locations.
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = $primary_menu_id;
		$locations['mobile']  = $primary_menu_id; // Mobile shares the primary menu.
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// ── Footer Menu ────────────────────────────────────────────────
	$footer_menu_name = 'Footer Menu';
	$footer_menu      = wp_get_nav_menu_object( $footer_menu_name );

	if ( ! $footer_menu ) {
		$footer_menu_id = wp_create_nav_menu( $footer_menu_name );

		$footer_pages = array(
			'refunds-and-cancellation-policy',
			'about-us',
			'contact-us',
			'disclaimer',
			'terms-and-conditions',
			'privacy-policy',
		);

		foreach ( $footer_pages as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				wp_update_nav_menu_item( $footer_menu_id, 0, array(
					'menu-item-title'     => $page->post_title,
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page->ID,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}
		}

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations['footer'] = $footer_menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}

/**
 * Set default Customizer options that match the original layout.
 *
 * Section 1: Latest News (4 posts)
 * Section 2: Supreme Court (8 posts)
 * Section 3: High Court (8 posts)
 * Section 4: Other Courts (6 posts)
 * Section 5: Legal Updates (4 posts)
 */
function lawandbeyond_set_default_customizer_options() {
	$sections_map = array(
		1 => array( 'slug' => 'latest-news',   'count' => 4 ),
		2 => array( 'slug' => 'supreme-court',  'count' => 8 ),
		3 => array( 'slug' => 'high-court',     'count' => 8 ),
		4 => array( 'slug' => 'other-courts',   'count' => 6 ),
		5 => array( 'slug' => 'legal-updates',  'count' => 4 ),
	);

	foreach ( $sections_map as $i => $sec ) {
		$cat = get_category_by_slug( $sec['slug'] );
		if ( $cat ) {
			set_theme_mod( "lawandbeyond_section_{$i}_category", $sec['slug'] );
			set_theme_mod( "lawandbeyond_section_{$i}_count", $sec['count'] );
		}
	}

	// Default copyright.
	if ( ! get_theme_mod( 'lawandbeyond_copyright' ) ) {
		set_theme_mod( 'lawandbeyond_copyright', '&copy; ' . date( 'Y' ) . ' Law and Beyond. All rights reserved.' );
	}

	// Default sidebar sections.
	if ( ! get_theme_mod( 'lawandbeyond_sidebar_opinion_category' ) ) {
		set_theme_mod( 'lawandbeyond_sidebar_opinion_category', 'opinion' );
		set_theme_mod( 'lawandbeyond_sidebar_opinion_title', 'Opinion' );
		set_theme_mod( 'lawandbeyond_sidebar_opinion_count', 3 );
	}
	if ( ! get_theme_mod( 'lawandbeyond_sidebar_recap_category' ) ) {
		set_theme_mod( 'lawandbeyond_sidebar_recap_category', 'monthly-recap' );
		set_theme_mod( 'lawandbeyond_sidebar_recap_title', 'Monthly Recap' );
		set_theme_mod( 'lawandbeyond_sidebar_recap_count', 3 );
	}
}
