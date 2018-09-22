<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to naturo_lite_comment() which is
 * located in the inc/template-tags.php file.
 *
 * @package SKT Naturolite
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

	<div id="comments" class="comments-area">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( 
					esc_html( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'naturo_lite' ) ),
					esc_attr(number_format_i18n( get_comments_number()) ),
					'<span>' . get_the_title() . '</span>'
				);
			?>            
		</h2>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above" class="comment-navigation" role="navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'naturo_lite' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( esc_attr__( '&larr; Older Comments', 'naturo_lite' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( esc_attr__( 'Newer Comments &rarr;', 'naturo_lite' ) ); ?></div>
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

		<ol class="comment-list">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use naturo_lite_comment() to format the comments.
				 * If you want to override this in a child theme, then you can
				 * define naturo_lite_comment() and that will be used instead.
				 * See naturo_lite_comment() in inc/template-tags.php for more.
				 */
				wp_list_comments( array( 'callback' => 'naturo_lite_comment' ) );
			?>
		</ol><!-- .comment-list -->
	<?php endif; // have_comments() ?>
	<?php
		if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="nocomments"><?php esc_html_e( 'Comments are closed.', 'naturo_lite' ); ?></p>
		<?php endif; ?>
	<?php comment_form(); ?>
</div><!-- #comments -->