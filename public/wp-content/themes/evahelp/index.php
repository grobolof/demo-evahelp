<?php
/**
 * Fallback template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="doc-page">
	<div class="wrap">
		<article class="doc-card">
			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<h1>Страница не найдена</h1>
				<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">На главную</a></p>
			<?php endif; ?>
		</article>
	</div>
</section>
<?php
get_footer();
