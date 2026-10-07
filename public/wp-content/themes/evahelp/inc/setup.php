<?php
/**
 * Theme supports, assets and document titles.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kov_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'kov_setup' );

function kov_assets(): void {
	wp_enqueue_style(
		'evahelp-fonts',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'evahelp', get_stylesheet_uri(), array( 'evahelp-fonts' ), KOV_VERSION );
	wp_enqueue_script( 'evahelp', get_theme_file_uri( 'assets/js/main.js' ), array(), KOV_VERSION, true );
	wp_localize_script(
		'evahelp',
		'kovLead',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);

	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'kov_assets', 100 );

function kov_head(): void {
	echo "<script>document.documentElement.classList.add('kov-anim');</script>\n";
	echo "<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">\n";
	echo "<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>\n";
	echo '<link rel="icon" href="' . esc_url( get_theme_file_uri( 'assets/favicon.svg' ) ) . '" type="image/svg+xml">' . "\n";
	echo '<link rel="icon" href="' . esc_url( get_theme_file_uri( 'assets/favicon-32.png' ) ) . '" type="image/png" sizes="32x32">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( get_theme_file_uri( 'assets/apple-touch-icon.png' ) ) . '">' . "\n";

	if ( is_front_page() ) {
		echo '<meta name="description" content="' . esc_attr( kov( 'meta_description' ) ) . "\">\n";
		return;
	}

	if ( is_singular( 'page' ) ) {
		$excerpt = get_post_field( 'post_excerpt', get_queried_object_id() );
		if ( is_string( $excerpt ) && $excerpt !== '' ) {
			echo '<meta name="description" content="' . esc_attr( $excerpt ) . "\">\n";
		}
	}
}
add_action( 'wp_head', 'kov_head', 0 );

/**
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function kov_document_title( array $parts ): array {
	if ( is_front_page() ) {
		$parts['title']   = kov( 'meta_title' );
		$parts['tagline'] = '';
		$parts['site']    = '';
		return $parts;
	}
	if ( is_page() ) {
		$parts['site'] = kov( 'tagline' );
	}
	return $parts;
}
add_filter( 'document_title_parts', 'kov_document_title' );

function kov_shortcode_phone(): string {
	$label = kov( 'phone_display' );
	if ( $label === '' ) {
		return '';
	}
	return '<a href="' . esc_url( kov_tel_href() ) . '">' . esc_html( $label ) . '</a>';
}
add_shortcode( 'kov_phone', 'kov_shortcode_phone' );

function kov_shortcode_email(): string {
	$email = kov( 'email' );
	if ( $email === '' || ! is_email( $email ) ) {
		return '';
	}
	return '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>';
}
add_shortcode( 'kov_email', 'kov_shortcode_email' );

function kov_shortcode_operator(): string {
	return esc_html( kov( 'operator' ) );
}
add_shortcode( 'kov_operator', 'kov_shortcode_operator' );

function kov_shortcode_city(): string {
	return esc_html( kov( 'city' ) );
}
add_shortcode( 'kov_city', 'kov_shortcode_city' );

function kov_shortcode_operator_dative(): string {
	return esc_html( kov( 'operator_dative' ) );
}
add_shortcode( 'kov_operator_to', 'kov_shortcode_operator_dative' );

function kov_hide_managed_singles(): void {
	if ( is_singular( array_merge( array( 'kov_lead' ), kov_content_types() ) ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}
add_action( 'template_redirect', 'kov_hide_managed_singles' );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
