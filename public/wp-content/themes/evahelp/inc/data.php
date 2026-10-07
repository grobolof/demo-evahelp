<?php
/**
 * Site copy edited from the admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array{title: string, fields: array<int, array<string, mixed>>}>
 */
function kov_settings_sections(): array {
	return array(
		array(
			'title'  => 'Контакты и компания',
			'fields' => array(
				array( 'key' => 'brand', 'label' => 'Название', 'type' => 'text', 'default' => 'EvaHelp' ),
				array( 'key' => 'logo_letter', 'label' => 'Буква в логотипе', 'type' => 'text', 'default' => 'EH' ),
				array( 'key' => 'tagline', 'label' => 'Подпись', 'type' => 'text', 'default' => 'Эвакуатор 24/7' ),
				array( 'key' => 'phone_display', 'label' => 'Телефон на сайте', 'type' => 'text', 'default' => '+7 (965) 068-62-59' ),
				array( 'key' => 'phone', 'label' => 'Телефон для звонка', 'type' => 'text', 'default' => '+79650686259', 'hint' => 'Формат для ссылки tel:, например +79650686259.' ),
				array( 'key' => 'telegram', 'label' => 'Telegram', 'type' => 'url', 'default' => 'https://t.me/EvaHelp' ),
				array( 'key' => 'telegram_label', 'label' => 'Подпись Telegram', 'type' => 'text', 'default' => '@EvaHelp' ),
				array( 'key' => 'whatsapp', 'label' => 'WhatsApp', 'type' => 'url', 'default' => 'https://wa.me/79650686259' ),
				array( 'key' => 'email', 'label' => 'Почта в документах', 'type' => 'email', 'default' => 'Vladkoval314@gmail.com' ),
				array( 'key' => 'notify_email', 'label' => 'Куда отправлять заявки', 'type' => 'email', 'default' => 'Vladkoval314@gmail.com', 'hint' => 'Письмо о новой заявке. Заявка сохраняется в админке и если письмо не ушло.' ),
				array( 'key' => 'operator', 'label' => 'Оператор персональных данных', 'type' => 'text', 'default' => 'Владислав Коваленко', 'hint' => 'Именительный падеж, как в политике: Владислав Коваленко.' ),
				array( 'key' => 'operator_dative', 'label' => 'Оператор в тексте согласия', 'type' => 'text', 'default' => 'Владиславу Коваленко', 'hint' => 'Дательный падеж: даю согласие Владиславу Коваленко.' ),
				array( 'key' => 'city', 'label' => 'Город', 'type' => 'text', 'default' => 'г. Санкт-Петербург' ),
				array( 'key' => 'hours', 'label' => 'Режим работы', 'type' => 'textarea', 'default' => 'Круглосуточно, 24/7. Санкт-Петербург и Ленинградская область.' ),
				array( 'key' => 'messengers_text', 'label' => 'Текст про мессенджеры', 'type' => 'textarea', 'default' => 'Напишите, если неудобно говорить. Ответим и назовём цену.' ),
			),
		),
		array(
			'title'  => 'Первый экран',
			'fields' => array(
				array( 'key' => 'meta_title', 'label' => 'Заголовок вкладки', 'type' => 'text', 'default' => 'Эвакуатор 24/7 в Санкт-Петербурге' ),
				array( 'key' => 'meta_description', 'label' => 'Описание для поисковиков', 'type' => 'textarea', 'default' => 'Эвакуатор 24/7 в Санкт-Петербурге и Ленобласти. Подача от 20 минут, легковой от 1500 ₽, кроссовер от 2000 ₽. Цена фиксируется до выезда.' ),
				array( 'key' => 'hero_eyebrow', 'label' => 'Надзаголовок', 'type' => 'text', 'default' => 'Санкт-Петербург и Ленобласть' ),
				array( 'key' => 'hero_title', 'label' => 'Заголовок', 'type' => 'text', 'default' => 'Эвакуатор 24/7' ),
				array( 'key' => 'hero_lede', 'label' => 'Текст', 'type' => 'textarea', 'default' => 'Подача эвакуатора от 20 минут. Заберём легковой, кроссовер или внедорожник после поломки и ДТП. Ориентир цены — от 1 500 ₽, точную сумму фиксируем до выезда.' ),
				array( 'key' => 'hero_caption', 'label' => 'Подпись к фото', 'type' => 'text', 'default' => 'Собственная техника · аккуратная погрузка' ),
				array( 'key' => 'hero_alt', 'label' => 'Описание главного фото', 'type' => 'text', 'default' => 'Эвакуатор с автомобилем на платформе вечером в городе' ),
				array( 'key' => 'fact_1_value', 'label' => 'Факт 1, значение', 'type' => 'text', 'default' => 'от 20 мин' ),
				array( 'key' => 'fact_1_label', 'label' => 'Факт 1, подпись', 'type' => 'text', 'default' => 'подача по городу' ),
				array( 'key' => 'fact_2_value', 'label' => 'Факт 2, значение', 'type' => 'text', 'default' => 'от 1 500 ₽' ),
				array( 'key' => 'fact_2_label', 'label' => 'Факт 2, подпись', 'type' => 'text', 'default' => 'легковой автомобиль' ),
				array( 'key' => 'fact_3_value', 'label' => 'Факт 3, значение', 'type' => 'text', 'default' => '24/7' ),
				array( 'key' => 'fact_3_label', 'label' => 'Факт 3, подпись', 'type' => 'text', 'default' => 'без выходных' ),
			),
		),
		array(
			'title'  => 'Тексты разделов',
			'fields' => array(
				array( 'key' => 'services_eyebrow', 'label' => 'Услуги, надзаголовок', 'type' => 'text', 'default' => 'Услуги' ),
				array( 'key' => 'services_title', 'label' => 'Услуги, заголовок', 'type' => 'text', 'default' => 'Когда вызывают эвакуатор' ),
				array( 'key' => 'services_text', 'label' => 'Услуги, текст', 'type' => 'textarea', 'default' => 'Поломка, авария, перевозка по городу или в другой регион. Если машина не на ходу или её нужно перевезти бережно — это к нам.' ),
				array( 'key' => 'prices_eyebrow', 'label' => 'Цены, надзаголовок', 'type' => 'text', 'default' => 'Цены' ),
				array( 'key' => 'prices_title', 'label' => 'Цены, заголовок', 'type' => 'text', 'default' => 'Сколько примерно стоит' ),
				array( 'key' => 'prices_text', 'label' => 'Цены, текст', 'type' => 'textarea', 'default' => 'Ниже ориентиры. Точную стоимость называем по телефону до выезда — она зависит от расстояния, класса авто и сложности погрузки.' ),
				array( 'key' => 'price_note', 'label' => 'Примечание под ценами', 'type' => 'textarea', 'default' => 'Внедорожник, ночной выезд и сложная погрузка считаются отдельно и входят в сумму, которую фиксируем до выезда.' ),
				array( 'key' => 'about_eyebrow', 'label' => 'О компании, надзаголовок', 'type' => 'text', 'default' => 'О компании' ),
				array( 'key' => 'about_title', 'label' => 'О компании, заголовок', 'type' => 'text', 'default' => 'Эвакуация без сюрпризов в цене' ),
				array( 'key' => 'about_text', 'label' => 'О компании, абзац 1', 'type' => 'textarea', 'default' => 'Работаем в Санкт-Петербурге и Ленинградской области круглосуточно, на собственной технике. Водители с опытом грузят автомобиль мягкими стропами и фиксируют его на платформе.' ),
				array( 'key' => 'about_text_2', 'label' => 'О компании, абзац 2', 'type' => 'textarea', 'default' => 'Стоимость называем до выезда. Если ситуация на месте такая, как вы описали, доплат не будет.' ),
				array(
					'key'     => 'about_points',
					'label'   => 'Пункты о компании',
					'type'    => 'textarea',
					'default' => "Круглосуточный выезд\nАккуратная погрузка\nЦена до выезда\nГород и область",
					'hint'    => 'Один пункт на строку.',
				),
				array( 'key' => 'steps_eyebrow', 'label' => 'Как работаем, надзаголовок', 'type' => 'text', 'default' => 'Как проходит работа' ),
				array( 'key' => 'steps_title', 'label' => 'Как работаем, заголовок', 'type' => 'text', 'default' => 'От заявки до доставки' ),
				array( 'key' => 'perks_eyebrow', 'label' => 'Преимущества, надзаголовок', 'type' => 'text', 'default' => 'Преимущества' ),
				array( 'key' => 'perks_title', 'label' => 'Преимущества, заголовок', 'type' => 'text', 'default' => 'Почему вызывают нас' ),
				array( 'key' => 'faq_eyebrow', 'label' => 'FAQ, надзаголовок', 'type' => 'text', 'default' => 'FAQ' ),
				array( 'key' => 'faq_title', 'label' => 'FAQ, заголовок', 'type' => 'text', 'default' => 'Частые вопросы' ),
				array( 'key' => 'contacts_eyebrow', 'label' => 'Контакты, надзаголовок', 'type' => 'text', 'default' => 'Контакты' ),
				array( 'key' => 'contacts_title', 'label' => 'Контакты, заголовок', 'type' => 'text', 'default' => 'Как связаться' ),
				array( 'key' => 'request_eyebrow', 'label' => 'Заявка, надзаголовок', 'type' => 'text', 'default' => 'Форма заявки' ),
				array( 'key' => 'request_title', 'label' => 'Заявка, заголовок', 'type' => 'text', 'default' => 'Оставьте заявку — перезвоним и назовём цену' ),
				array( 'key' => 'request_text', 'label' => 'Заявка, текст', 'type' => 'textarea', 'default' => 'Нужны имя, телефон, откуда забрать автомобиль, куда его доставить и пара слов о ситуации. Этого достаточно, чтобы посчитать подачу.' ),
			),
		),
		array(
			'title'  => 'Фотографии',
			'fields' => array(
				array( 'key' => 'hero_image', 'label' => 'Главное фото', 'type' => 'image', 'default' => 0, 'fallback' => 'assets/img/05-1024x768.png', 'hint' => 'Если не выбрано, используется фото из темы.' ),
				array( 'key' => 'about_image_1', 'label' => 'Фото о компании 1', 'type' => 'image', 'default' => 0, 'fallback' => 'assets/img/01-1024x768.png' ),
				array( 'key' => 'about_alt_1', 'label' => 'Описание фото 1', 'type' => 'text', 'default' => 'Автомобиль на платформе эвакуатора' ),
				array( 'key' => 'about_image_2', 'label' => 'Фото о компании 2', 'type' => 'image', 'default' => 0, 'fallback' => 'assets/img/04-768x576.png' ),
				array( 'key' => 'about_alt_2', 'label' => 'Описание фото 2', 'type' => 'text', 'default' => 'Легковой автомобиль закреплён на эвакуаторе' ),
				array( 'key' => 'about_image_3', 'label' => 'Фото о компании 3', 'type' => 'image', 'default' => 0, 'fallback' => 'assets/img/06-768x576.png' ),
				array( 'key' => 'about_alt_3', 'label' => 'Описание фото 3', 'type' => 'text', 'default' => 'Перевозка техники на платформе эвакуатора' ),
			),
		),
	);
}

/**
 * @return array<int, array<string, mixed>>
 */
function kov_settings_fields(): array {
	$fields = array();
	foreach ( kov_settings_sections() as $section ) {
		foreach ( $section['fields'] as $field ) {
			$fields[] = $field;
		}
	}
	return $fields;
}

/**
 * @return array<string, mixed>
 */
function kov_default_settings(): array {
	$defaults = array();
	foreach ( kov_settings_fields() as $field ) {
		$defaults[ $field['key'] ] = $field['default'];
	}
	return $defaults;
}

/**
 * @return array<string, mixed>
 */
function kov_settings(): array {
	$stored = get_option( 'kov_settings', array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}
	return array_merge( kov_default_settings(), $stored );
}

function kov( string $key ): string {
	$settings = kov_settings();
	if ( ! isset( $settings[ $key ] ) || is_array( $settings[ $key ] ) ) {
		return '';
	}
	return (string) $settings[ $key ];
}

/**
 * @param mixed $input Raw option value.
 * @return array<string, mixed>
 */
function kov_sanitize_settings( $input ): array {
	$clean = array();
	if ( ! is_array( $input ) ) {
		$input = array();
	}

	foreach ( kov_settings_fields() as $field ) {
		$key     = $field['key'];
		$default = $field['default'];
		$raw     = $input[ $key ] ?? $default;
		$clean[ $key ] = kov_sanitize_setting( $field, $raw );
	}

	return $clean;
}

/**
 * @param array<string, mixed> $field Field schema.
 * @param mixed                $raw   Submitted value.
 * @return int|string
 */
function kov_sanitize_setting( array $field, $raw ) {
	$type = $field['type'];

	if ( 'image' === $type ) {
		$id = absint( $raw );
		if ( $id && wp_attachment_is_image( $id ) ) {
			return $id;
		}
		return 0;
	}

	$value = is_scalar( $raw ) ? (string) $raw : '';
	$value = str_replace( "\0", '', $value );

	if ( 'textarea' === $type ) {
		return mb_substr( sanitize_textarea_field( $value ), 0, 4000 );
	}

	if ( 'url' === $type ) {
		return esc_url_raw( trim( $value ) );
	}

	if ( 'email' === $type ) {
		return sanitize_email( $value );
	}

	$value = sanitize_text_field( $value );
	if ( 'logo_letter' === $field['key'] ) {
		return mb_substr( $value, 0, 2 );
	}

	return mb_substr( $value, 0, 300 );
}
