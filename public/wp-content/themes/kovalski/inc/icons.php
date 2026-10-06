<?php
/**
 * Inline icons used by service and advantage cards.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string>
 */
function kov_icon_markup_map(): array {
	return array(
		'breakdown' => '<path d="M14.7 6.3a4.1 4.1 0 0 0-5.7 5.6L3.5 17.4 6.6 20.5l5.5-5.5a4.1 4.1 0 0 0 5.6-5.7L15 12l-3-3z"/>',
		'accident'  => '<path d="M10.3 3.8 1.8 18.2A2 2 0 0 0 3.5 21h17a2 2 0 0 0 1.7-2.8L13.7 3.8a2 2 0 0 0-3.4 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
		'transport' => '<path d="M1 7h13v8H1z"/><path d="M14 10h4l3 3v2h-7z"/><circle cx="5.5" cy="17.5" r="1.5"/><circle cx="17.5" cy="17.5" r="1.5"/>',
		'suv'       => '<path d="M3 16V8l2.2-3h8.2L16 8h3.2L21 11v5"/><path d="M3 16H2"/><circle cx="7" cy="16" r="1.6"/><circle cx="17" cy="16" r="1.6"/>',
		'intercity' => '<circle cx="6" cy="6" r="2.2"/><circle cx="18" cy="18" r="2.2"/><path d="M8 6h5a4 4 0 0 1 0 8H11"/><path d="M16 18h-3a4 4 0 0 1-3.5-2"/>',
		'complex'   => '<rect x="4" y="8" width="16" height="10" rx="1.5"/><path d="M12 8V4"/><circle cx="12" cy="3.2" r="1.2"/><path d="M8 13h8"/>',
		'speed'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v6l4 2"/>',
		'always'    => '<path d="M21 14.5A8.5 8.5 0 1 1 9.5 3 7 7 0 0 0 21 14.5z"/>',
		'drivers'   => '<path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="8" r="4"/>',
		'fleet'     => '<path d="M1 7h13v8H1z"/><path d="M14 10h4l3 3v2h-7z"/><circle cx="5.5" cy="17.5" r="1.5"/><circle cx="17.5" cy="17.5" r="1.5"/>',
		'price'     => '<path d="M20 6 9 17l-5-5"/>',
		'phone'     => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.81.3 1.6.54 2.36a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.72-1.11a2 2 0 0 1 2.11-.45c.76.24 1.55.41 2.36.54A2 2 0 0 1 22 16.92z"/>',
		'message'   => '<path d="M21 15a8 8 0 0 1-8 8H7l-4 3V15a8 8 0 0 1 8-8h2a8 8 0 0 1 8 8z"/>',
	);
}

/**
 * @return array<string, string>
 */
function kov_icon_choices( string $group ): array {
	if ( 'perk' === $group ) {
		return array(
			'speed'   => 'Быстрая подача',
			'always'  => 'Круглосуточно',
			'drivers' => 'Водители',
			'fleet'   => 'Своя техника',
			'price'   => 'Без доплат',
		);
	}

	return array(
		'breakdown' => 'Поломка',
		'accident'  => 'ДТП',
		'transport' => 'Перевозка',
		'suv'       => 'Внедорожник',
		'intercity' => 'Межгород',
		'complex'   => 'Сложная погрузка',
	);
}

function kov_icon( string $name ): string {
	$map = kov_icon_markup_map();
	if ( ! isset( $map[ $name ] ) ) {
		$name = 'transport';
	}

	return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $map[ $name ] . '</svg>';
}
