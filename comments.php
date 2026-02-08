<?php
/**
 * The template for displaying comments.
 *
 * @package LawAndBeyond
 */

/*
 * If the current post is protected by a password and the visitor
 * has not yet entered the password, return early.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			if ( '1' === $comment_count ) {
				printf(
					/* translators: %s: post title */
					esc_html__( 'One comment on &ldquo;%s&rdquo;', 'lawandbeyond' ),
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			} else {
				printf(
					/* translators: 1: comment count, 2: post title */
					esc_html(
						_nx(
							'%1$s comment on &ldquo;%2$s&rdquo;',
							'%1$s comments on &ldquo;%2$s&rdquo;',
							$comment_count,
							'comments title',
							'lawandbeyond'
						)
					),
					number_format_i18n( $comment_count ),
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 50,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation();
		?>

	<?php endif; ?>

	<?php
	// If comments are closed and there are comments, leave a note.
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'lawandbeyond' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h3>',
		)
	);
	?>

</div><!-- #comments -->
