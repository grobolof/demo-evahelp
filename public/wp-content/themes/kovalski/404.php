<?php
/**
 * Missing page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="doc-page">
	<div class="wrap">
		<article class="doc-card">
			<p class="eyebrow">404</p>
			<h1>Страница не найдена</h1>
			<p>Такой страницы нет. Вернитесь на главную и оставьте заявку или позвоните.</p>
			<p><a class="btn btn-accent" href="<?php echo esc_url( home_url( '/' ) ); ?>">На главную</a></p>
		</article>
	</div>
</section>
<?php
get_footer();
