<?php
/**
 * The template for displaying search results.
 *
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $wp_query;
$found_posts = $wp_query->found_posts;
$search_term = get_search_query();
?>
<main id="content" class="site-main">
	<?php get_search_form(); ?>
	<div class="page-content">
		<?php
		if ( apply_filters( 'hello-plus-theme/page_title', true ) ) :
			?>
			<div class="page-header">
				<h1 class="entry-title">
					<?php

					$count_text = sprintf(
					/* translators: %d: Number of search results */
						_n( '%d result', '%d results', $found_posts, 'hello-biz' ),
						number_format_i18n( $found_posts )
					);

					$post_count_text = sprintf(
					/* translators: 1: Number of results, 2: Search term */
						__( '%1$s found for %2$s', 'hello-biz' ),
						$count_text,
						sprintf(
							'<span>%s</span>',
							$search_term
						)
					);

					echo wp_kses_post( $post_count_text );
					?>
				</h1>
			</div>

		<?php endif; ?>
		<div class="posts-container">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					$entry_id  = get_the_ID();
					$post_link = get_the_permalink( $entry_id );
					?>
					<article class="post">
						<h2 class="entry-title">
							<a href="<?php echo esc_url( $post_link ); ?>">
								<?php echo wp_kses_post( get_the_title() ); ?>
							</a>
						</h2>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php echo esc_url( $post_link ); ?>">
								<?php echo get_the_post_thumbnail( $entry_id, 'large' ); ?>
							</a>
						<?php endif; ?>
						<?php the_excerpt(); ?>
					</article>
					<?php
				endwhile;
			else :
				?>
				<p>
					<?php echo esc_html__( 'It seems we can\'t find what you\'re looking for.', 'hello-biz' ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
		if ( $wp_query->max_num_pages > 1 ) :
			$arr_r      = '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none"><path d="M8.89626 5.5L7.60376 6.7925L11.8021 11L7.60376 15.2075L8.89626 16.5L14.3963 11L8.89626 5.5Z" fill="#0C0D0E"/></svg>';
			$arr_l      = '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none"><path d="M13.1037 5.5L14.3962 6.7925L10.1979 11L14.3962 15.2075L13.1037 16.5L7.6037 11L13.1037 5.5Z" fill="#0C0D0E"/></svg>';
			$prev_arrow = is_rtl() ? $arr_r : $arr_l;
			$next_arrow = is_rtl() ? $arr_l : $arr_r;
			$prev_text  = sprintf(
				'%1$s %2$s',
				sprintf(
					'<span class="meta-nav">%s</span>',
					$prev_arrow
				),
				sprintf(
					'<span class="screen-reader-text">%s</span>',
					__( 'Previous', 'hello-biz' )
				)
			);
			$next_text  = sprintf(
				'%1$s %2$s',
				sprintf(
					'<span class="meta-nav">%s</span>',
					$next_arrow
				),
				sprintf(
					'<span class="screen-reader-text">%s</span>',
					__( 'Next', 'hello-biz' )
				)
			);

			the_posts_pagination(
				[
					'mid_size'  => 2,
					'prev_text' => $prev_text,
					'next_text' => $next_text,
					'class'     => 'ehp-pagination',
				]
			);
		endif;
		?>
	</div>
</main>
