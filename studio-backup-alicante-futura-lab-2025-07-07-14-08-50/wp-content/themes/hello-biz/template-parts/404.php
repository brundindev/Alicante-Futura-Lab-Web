<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package HelloBiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<main id="content" class="site-main">

	<?php if ( apply_filters( 'hello-plus-theme/page_title', true ) ) : ?>
		<div class="page-header">
			<p class="sub-heading">
				<?php echo esc_html__( '404', 'hello-biz' ); ?>
			</p>
			<h1 class="entry-title">
				<?php echo esc_html__( 'We can\'t find that page', 'hello-biz' ); ?>
			</h1>
		</div>
	<?php endif; ?>

	<div class="page-content">
		<p>
			<?php
			printf(
			/* translators: %s: link to home page */
				wp_kses(
					__( 'Try searching or <a href="%s">return to homepage</a>.', 'hello-biz' ),
					[
						'a' => [
							'href' => [],
						],
					]
				),
				esc_url( home_url( '/' ) )
			);
			?>
		</p>
		<?php get_search_form(); ?>
	</div>

</main>
