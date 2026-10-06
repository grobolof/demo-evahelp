<?php
/**
 * Front-end helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kov_html( string $key ): string {
	return nl2br( esc_html( kov( $key ) ), false );
}

/**
 * @return string[]
 */
function kov_lines( string $key ): array {
	$lines = preg_split( '/\r\n|\r|\n/', kov( $key ) );
	if ( ! is_array( $lines ) ) {
		return array();
	}

	$lines = array_map( 'trim', $lines );
	return array_values( array_filter( $lines, static function ( string $line ): bool {
		return $line !== '';
	} ) );
}

function kov_tel_href(): string {
	$phone = preg_replace( '/[^\d+]/', '', kov( 'phone' ) );
	if ( ! is_string( $phone ) || $phone === '' ) {
		$phone = preg_replace( '/[^\d+]/', '', kov( 'phone_display' ) );
	}
	return 'tel:' . ( is_string( $phone ) ? $phone : '' );
}

function kov_anchor( string $id ): string {
	$id = preg_replace( '/[^A-Za-z0-9_-]/', '', $id );
	$id = is_string( $id ) ? $id : '';
	if ( is_front_page() ) {
		return '#' . $id;
	}
	return home_url( '/#' . $id );
}

function kov_page_url( string $slug ): string {
	$page = get_page_by_path( $slug );
	if ( $page instanceof WP_Post ) {
		$link = get_permalink( $page );
		if ( is_string( $link ) ) {
			return $link;
		}
	}
	return home_url( '/' );
}

/**
 * @return WP_Post[]
 */
function kov_items( string $type ): array {
	$posts = get_posts(
		array(
			'post_type'   => $type,
			'post_status' => 'publish',
			'numberposts' => 100,
			'orderby'     => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
		)
	);

	return is_array( $posts ) ? $posts : array();
}

function kov_section_head( string $eyebrow_key, string $title_key, string $text_key = '' ): void {
	echo '<div class="section-head reveal">';
	echo '<p class="eyebrow">' . esc_html( kov( $eyebrow_key ) ) . '</p>';
	echo '<h2>' . esc_html( kov( $title_key ) ) . '</h2>';
	if ( $text_key !== '' && kov( $text_key ) !== '' ) {
		echo '<p>' . esc_html( kov( $text_key ) ) . '</p>';
	}
	echo '</div>';
}

function kov_hero_media(): void {
	$alt = kov( 'hero_alt' );
	$id  = (int) kov( 'hero_image' );
	if ( $id > 0 ) {
		echo wp_get_attachment_image(
			$id,
			'large',
			false,
			array(
				'alt'           => $alt,
				'fetchpriority' => 'high',
				'sizes'         => '(max-width: 980px) 100vw, 520px',
			)
		);
		return;
	}

	printf(
		'<img src="%1$s" srcset="%2$s 768w, %1$s 1024w" sizes="(max-width: 980px) 100vw, 520px" width="1024" height="768" alt="%3$s" fetchpriority="high">',
		esc_url( get_theme_file_uri( 'assets/img/05-1024x768.png' ) ),
		esc_url( get_theme_file_uri( 'assets/img/05-768x576.png' ) ),
		esc_attr( $alt )
	);
}

function kov_about_image( string $setting, string $fallback, string $alt_key, int $width, int $height ): void {
	$alt = kov( $alt_key );
	$id  = (int) kov( $setting );
	if ( $id > 0 ) {
		echo wp_get_attachment_image(
			$id,
			'large',
			false,
			array(
				'alt'     => $alt,
				'loading' => 'lazy',
			)
		);
		return;
	}

	printf(
		'<img src="%s" width="%d" height="%d" alt="%s" loading="lazy">',
		esc_url( get_theme_file_uri( $fallback ) ),
		$width,
		$height,
		esc_attr( $alt )
	);
}
