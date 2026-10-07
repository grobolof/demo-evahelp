<?php
/**
 * Legal and other pages in the landing frame.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="doc-page">
	<div class="wrap">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article class="doc-card">
				<p class="eyebrow">Документы</p>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</div>
</section>
<?php
get_footer();
