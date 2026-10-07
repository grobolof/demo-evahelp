<?php
/**
 * Demo credentials for the guest admin account.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$kov_admin_url = admin_url();

get_header();
?>
<section class="doc-page">
	<div class="wrap access-stack">
		<article class="doc-card access-card">
			<h1>Доступы</h1>
			<p><a class="access-admin" href="<?php echo esc_url( $kov_admin_url ); ?>"><span aria-hidden="true">👉</span> Вход в админку</a></p>
			<p class="access-login">Логин: <code>guest@mil.ru</code></p>
			<p class="access-login">Пароль: <code>guest</code></p>
		</article>
		<article class="doc-card access-card">
			<h2>Описание</h2>
			<p>1. Пользователю <strong>guest</strong> доступен раздел «Заявки» в админке для просмотра заявок эвакуатора и возможность их удалить.</p>
		</article>
	</div>
</section>
<?php
get_footer();
