<?php
/**
 * Landing footer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<footer class="site-foot">
	<div class="wrap foot-inner">
		<div>
			<strong style="color: var(--text)"><?php echo esc_html( kov( 'brand' ) ); ?></strong>
			<div><?php echo esc_html( kov( 'tagline' ) ); ?> · <?php echo esc_html( kov( 'phone_display' ) ); ?></div>
		</div>
		<div class="foot-links">
			<a href="<?php echo esc_url( kov_page_url( 'politika' ) ); ?>">Политика обработки персональных данных</a>
			<a href="<?php echo esc_url( kov_page_url( 'soglasie' ) ); ?>">Согласие на обработку персональных данных</a>
			<?php if ( kov( 'telegram' ) !== '' ) : ?>
				<a href="<?php echo esc_url( kov( 'telegram' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( kov( 'telegram_label' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</footer>

<div class="dock">
	<a class="btn btn-ghost" href="<?php echo esc_url( kov_tel_href() ); ?>">Позвонить</a>
	<a class="btn btn-accent" href="<?php echo esc_url( kov_anchor( 'request' ) ); ?>">Заявка</a>
</div>

<div class="access-fab-bar">
	<div class="wrap">
		<a class="access-fab" href="<?php echo esc_url( kov_page_url( 'app-description' ) ); ?>">О проекте</a>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
