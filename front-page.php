<?php
/**
 * The front page template for Law and Beyond.
 *
 * Displays the Top Stories hero section followed by category-based
 * news sections configured via the Customizer.
 *
 * @package LawAndBeyond
 */

get_header();
?>

<!-- Top Stories Section -->
<?php get_template_part( 'template-parts/section-top-stories' ); ?>

<div id="primary" class="content-area">
	<div class="container">
		<div class="page-grid">
			<main class="site-main" role="main">

				<?php
				/**
				 * Render up to 5 category sections from the Customizer.
				 * Each section has a title, category slug, and post count.
				 */
				for ( $i = 1; $i <= 5; $i++ ) :
					$section_title = get_theme_mod( "lawandbeyond_section_{$i}_title", '' );
					$section_cat   = get_theme_mod( "lawandbeyond_section_{$i}_category", '' );
					$section_count = get_theme_mod( "lawandbeyond_section_{$i}_count", 4 );

					if ( empty( $section_cat ) ) {
						continue;
					}

					$cat_obj = get_category_by_slug( $section_cat );
					if ( ! $cat_obj ) {
						continue;
					}

					// Use the customizer title or fall back to the category name.
					$display_title = ! empty( $section_title ) ? $section_title : $cat_obj->name;

					$section_query = new WP_Query(
						array(
							'category_name'  => $section_cat,
							'posts_per_page' => intval( $section_count ),
							'post_status'    => 'publish',
							'no_found_rows'  => true,
						)
					);

					if ( $section_query->have_posts() ) :
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
								while ( $section_query->have_posts() ) :
									$section_query->the_post();
									get_template_part( 'template-parts/content' );
								endwhile;
								?>
							</div>
						</section>
						<?php
						wp_reset_postdata();
					endif;
				endfor;
				?>

			</main>

			<?php get_sidebar(); ?>
		</div>
	</div>
</div>

<?php
get_footer();
