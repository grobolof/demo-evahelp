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
	<div class="wrap foot-credit">
		<p>
			© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( kov( 'brand' ) ); ?> —
			<a href="https://github.com/grobolof" target="_blank" rel="noopener noreferrer">@grobolof</a>
			(проект является демонстрацией с открытым
			<a class="foot-source" href="https://github.com/grobolof/demo-evahelp" target="_blank" rel="noopener noreferrer">исходным кодом
				<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false">
					<path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82A7.65 7.65 0 0 1 8 4.77c.68.003 1.36.092 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"></path>
				</svg>
			</a>)
		</p>
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
