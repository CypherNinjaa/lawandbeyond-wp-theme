<?php
/**
 * Template part: Displayed when no content is found.
 *
 * @package LawAndBeyond
 */
?>

<section class="no-results not-found">
	<header class="archive-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'lawandbeyond' ); ?></h1>
	</header>

	<div class="entry-content">
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'Sorry, no results matched your search terms. Please try again with different keywords.', 'lawandbeyond' ); ?></p>
		<?php else : ?>
			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'lawandbeyond' ); ?></p>
		<?php endif; ?>

		<?php get_search_form(); ?>
	</div>
</section>
