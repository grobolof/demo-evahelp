<?php
/**
 * Landing header.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$kov_nav = array(
	'services' => 'Услуги',
	'prices'   => 'Цены',
	'about'    => 'О компании',
	'steps'    => 'Как работаем',
	'faq'      => 'FAQ',
	'contacts' => 'Контакты',
);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'kov-landing' ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content">К содержанию</a>

<header class="site-head">
	<div class="head-inner">
		<a class="logo" href="<?php echo esc_url( is_front_page() ? '#top' : home_url( '/' ) ); ?>">
			<span class="logo-mark" aria-hidden="true"><?php echo esc_html( kov( 'logo_letter' ) ); ?></span>
			<span>
				<strong><?php echo esc_html( kov( 'brand' ) ); ?></strong>
				<small><?php echo esc_html( kov( 'tagline' ) ); ?></small>
			</span>
		</a>
		<nav class="nav" id="site-nav" aria-label="Разделы страницы">
			<?php foreach ( $kov_nav as $anchor => $label ) : ?>
				<a href="<?php echo esc_url( kov_anchor( $anchor ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="head-tools">
			<a class="head-phone" href="<?php echo esc_url( kov_tel_href() ); ?>"><?php echo esc_html( kov( 'phone_display' ) ); ?></a>
			<a class="btn btn-accent" href="<?php echo esc_url( kov_anchor( 'request' ) ); ?>">Вызвать</a>
			<button class="burger" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Меню">
				<span></span>
			</button>
		</div>
	</div>
</header>

<main id="content">
