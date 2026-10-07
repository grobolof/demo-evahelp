<?php
/**
 * Admin menu: site data and an overview of the landing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kov_register_admin_menu(): void {
	add_menu_page(
		'EvaHelp',
		'EvaHelp',
		'kov_view_leads',
		'evahelp',
		'kov_render_overview',
		'dashicons-car',
		26
	);

	$GLOBALS['kov_settings_hook'] = add_submenu_page(
		'evahelp',
		'Данные сайта',
		'Данные сайта',
		'manage_options',
		'kov-settings',
		'kov_render_settings'
	);
}
add_action( 'admin_menu', 'kov_register_admin_menu', 5 );

function kov_register_settings(): void {
	register_setting(
		'kov_settings',
		'kov_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kov_sanitize_settings',
			'default'           => kov_default_settings(),
		)
	);
}
add_action( 'admin_init', 'kov_register_settings' );

function kov_reorder_admin_menu(): void {
	global $submenu, $menu;
	if ( ! isset( $submenu['evahelp'] ) || ! is_array( $submenu['evahelp'] ) ) {
		return;
	}

	$by_slug = array();
	foreach ( $submenu['evahelp'] as $item ) {
		$by_slug[ $item[2] ] = $item;
	}

	$order = array(
		'evahelp',
		'edit.php?post_type=kov_lead',
		'kov-settings',
		'edit.php?post_type=kov_service',
		'edit.php?post_type=kov_price',
		'edit.php?post_type=kov_perk',
		'edit.php?post_type=kov_step',
		'edit.php?post_type=kov_faq',
	);

	$new_count = current_user_can( 'kov_view_leads' ) ? kov_count_leads( 'new' ) : 0;
	$sorted    = array();
	foreach ( $order as $slug ) {
		if ( ! isset( $by_slug[ $slug ] ) ) {
			continue;
		}
		if ( 'evahelp' === $slug ) {
			$by_slug[ $slug ][0] = 'Обзор';
		}
		if ( 'edit.php?post_type=kov_lead' === $slug && $new_count > 0 ) {
			$by_slug[ $slug ][0] .= kov_admin_count_badge( $new_count );
		}
		$sorted[] = $by_slug[ $slug ];
	}
	foreach ( $by_slug as $slug => $item ) {
		if ( ! in_array( $slug, $order, true ) ) {
			$sorted[] = $item;
		}
	}
	if ( function_exists( 'kov_is_lead_viewer' ) && kov_is_lead_viewer() ) {
		$sorted = array_values(
			array_filter(
				$sorted,
				static function ( array $item ): bool {
					return isset( $item[2] ) && 'edit.php?post_type=kov_lead' === $item[2];
				}
			)
		);
	}
	$submenu['evahelp'] = $sorted;

	if ( $new_count > 0 && is_array( $menu ) ) {
		foreach ( $menu as $index => $item ) {
			if ( isset( $item[2] ) && 'evahelp' === $item[2] ) {
				$menu[ $index ][0] .= kov_admin_count_badge( $new_count );
			}
		}
	}
}
add_action( 'admin_menu', 'kov_reorder_admin_menu', 99 );

function kov_admin_count_badge( int $count ): string {
	return ' <span class="awaiting-mod count-' . $count . '"><span class="pending-count">' . $count . '</span></span>';
}

function kov_admin_assets( string $hook ): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$types  = array_merge( array( 'kov_lead' ), kov_content_types() );
	$is_kov = ( $screen && in_array( $screen->post_type, $types, true ) )
		|| $hook === 'toplevel_page_evahelp'
		|| $hook === ( $GLOBALS['kov_settings_hook'] ?? '' );

	if ( ! $is_kov ) {
		return;
	}

	wp_enqueue_style(
		'evahelp-admin',
		get_theme_file_uri( 'assets/css/admin.css' ),
		array(),
		KOV_VERSION
	);

	if ( $hook === ( $GLOBALS['kov_settings_hook'] ?? '' ) ) {
		wp_enqueue_media();
		wp_enqueue_script(
			'evahelp-admin',
			get_theme_file_uri( 'assets/js/admin.js' ),
			array( 'jquery' ),
			KOV_VERSION,
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'kov_admin_assets' );

function kov_render_overview(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html( 'Недостаточно прав.' ) );
	}

	$cards = array(
		array( 'Новые заявки', (string) kov_count_leads( 'new' ), admin_url( 'edit.php?post_type=kov_lead&kov_status=new' ) ),
		array( 'Все заявки', (string) kov_count_leads(), admin_url( 'edit.php?post_type=kov_lead' ) ),
		array( 'Услуги', (string) kov_count_published( 'kov_service' ), admin_url( 'edit.php?post_type=kov_service' ) ),
		array( 'Цены', (string) kov_count_published( 'kov_price' ), admin_url( 'edit.php?post_type=kov_price' ) ),
		array( 'Вопросы', (string) kov_count_published( 'kov_faq' ), admin_url( 'edit.php?post_type=kov_faq' ) ),
	);
	?>
	<div class="wrap kov-admin">
		<h1>EvaHelp</h1>
		<p>Лендинг эвакуатора открывается на <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">главной странице сайта</a>. Телефон, тексты и фото меняются в «Данных сайта». Карточки услуг, цен, шагов и вопросов — отдельные списки в этом меню. Заявки с формы попадают в «Заявки».</p>
		<div class="kov-overview-grid">
			<?php foreach ( $cards as $card ) : ?>
				<a class="kov-stat" href="<?php echo esc_url( $card[2] ); ?>">
					<strong><?php echo esc_html( $card[1] ); ?></strong>
					<span><?php echo esc_html( $card[0] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
		<h2>Что сейчас на сайте</h2>
		<table class="widefat striped kov-snapshot">
			<tbody>
				<tr><th>Название</th><td><?php echo esc_html( kov( 'brand' ) ); ?></td></tr>
				<tr><th>Телефон</th><td><a href="<?php echo esc_url( kov_tel_href() ); ?>"><?php echo esc_html( kov( 'phone_display' ) ); ?></a></td></tr>
				<tr><th>Почта заявок</th><td><?php echo esc_html( kov( 'notify_email' ) ); ?></td></tr>
				<tr><th>Telegram</th><td><?php echo esc_html( kov( 'telegram' ) ); ?></td></tr>
				<tr><th>WhatsApp</th><td><?php echo esc_html( kov( 'whatsapp' ) ); ?></td></tr>
				<tr><th>Режим</th><td><?php echo esc_html( kov( 'hours' ) ); ?></td></tr>
			</tbody>
		</table>
		<p>
			<?php if ( current_user_can( 'manage_options' ) ) : ?>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=kov-settings' ) ); ?>">Изменить данные сайта</a>
			<?php endif; ?>
			<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=kov_lead' ) ); ?>">Заявки</a>
		</p>
		<?php kov_render_leads_widget(); ?>
	</div>
	<?php
}

function kov_count_published( string $type ): int {
	$counts = wp_count_posts( $type );
	return ( $counts && isset( $counts->publish ) ) ? (int) $counts->publish : 0;
}

function kov_render_settings(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html( 'Недостаточно прав.' ) );
	}
	?>
	<div class="wrap kov-admin">
		<h1>Данные сайта</h1>
		<p>Эти поля выводятся на лендинге. Карточки услуг, цен, преимуществ, шагов и вопросов редактируются в соседних разделах меню EvaHelp.</p>
		<?php settings_errors(); ?>
		<form action="options.php" method="post">
			<?php settings_fields( 'kov_settings' ); ?>
			<?php foreach ( kov_settings_sections() as $section ) : ?>
				<h2><?php echo esc_html( $section['title'] ); ?></h2>
				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( $section['fields'] as $field ) : ?>
						<tr>
							<th scope="row"><label for="kov_<?php echo esc_attr( $field['key'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
							<td>
								<?php kov_render_setting_field( $field ); ?>
								<?php if ( ! empty( $field['hint'] ) ) : ?>
									<p class="description"><?php echo esc_html( $field['hint'] ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endforeach; ?>
			<?php submit_button( 'Сохранить данные' ); ?>
		</form>
	</div>
	<?php
}

/**
 * @param array<string, mixed> $field Field schema.
 */
function kov_render_setting_field( array $field ): void {
	$key  = (string) $field['key'];
	$type = (string) $field['type'];
	$id   = 'kov_' . $key;

	if ( 'image' === $type ) {
		$attachment = (int) kov( $key );
		$fallback   = get_theme_file_uri( (string) ( $field['fallback'] ?? '' ) );
		$src        = $fallback;
		if ( $attachment > 0 ) {
			$url = wp_get_attachment_image_url( $attachment, 'medium' );
			if ( is_string( $url ) && $url !== '' ) {
				$src = $url;
			}
		}
		echo '<div class="kov-image-field">';
		printf(
			'<img src="%s" alt="" data-fallback="%s">',
			esc_url( $src ),
			esc_url( $fallback )
		);
		printf(
			'<input type="hidden" id="%1$s" name="kov_settings[%2$s]" value="%3$d">',
			esc_attr( $id ),
			esc_attr( $key ),
			$attachment
		);
		echo '<p><button type="button" class="button kov-pick-image" data-target="' . esc_attr( $id ) . '">Выбрать изображение</button> ';
		echo '<button type="button" class="button kov-clear-image">Фото по умолчанию</button></p>';
		echo '</div>';
		return;
	}

	$value = kov( $key );
	if ( 'textarea' === $type ) {
		printf(
			'<textarea class="large-text" rows="4" id="%1$s" name="kov_settings[%2$s]">%3$s</textarea>',
			esc_attr( $id ),
			esc_attr( $key ),
			esc_textarea( $value )
		);
		return;
	}

	$input_type = 'email' === $type ? 'email' : ( 'url' === $type ? 'url' : 'text' );
	printf(
		'<input class="regular-text" type="%1$s" id="%2$s" name="kov_settings[%3$s]" value="%4$s">',
		esc_attr( $input_type ),
		esc_attr( $id ),
		esc_attr( $key ),
		esc_attr( $value )
	);
}
