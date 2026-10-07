<?php
/**
 * First-run content matching the original landing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kov_seed(): void {
	if ( get_option( 'kov_seeded' ) === '1' ) {
		return;
	}

	kov_seed_collection( 'kov_service', kov_seed_services() );
	kov_seed_collection( 'kov_price', kov_seed_prices() );
	kov_seed_collection( 'kov_perk', kov_seed_perks() );
	kov_seed_collection( 'kov_step', kov_seed_steps() );
	kov_seed_collection( 'kov_faq', kov_seed_faq() );
	kov_seed_page(
		'politika',
		'Политика обработки персональных данных',
		'Политика обработки персональных данных сайта эвакуатора EvaHelp.',
		kov_seed_policy_html()
	);
	kov_seed_page(
		'soglasie',
		'Согласие на обработку персональных данных',
		'Согласие на обработку персональных данных для заявок на эвакуатор EvaHelp.',
		kov_seed_consent_html()
	);
	kov_seed_access_page();

	if ( get_option( 'blogname' ) === 'Evacuator' ) {
		update_option( 'blogname', 'EvaHelp' );
		update_option( 'blogdescription', 'Эвакуатор 24/7' );
	}

	update_option( 'kov_seeded', '1' );
}
add_action( 'init', 'kov_seed', 20 );
add_action( 'after_switch_theme', 'kov_seed' );

/**
 * The access page is created after the first seed, so it is ensured separately.
 */
function kov_ensure_access_page(): void {
	if ( get_option( 'kov_app_description_ready' ) === '1' ) {
		return;
	}

	$page = get_page_by_path( 'app-description' );
	if ( ! $page instanceof WP_Post ) {
		$legacy = get_page_by_path( 'access' );
		if ( $legacy instanceof WP_Post ) {
			wp_update_post(
				array(
					'ID'        => $legacy->ID,
					'post_name' => 'app-description',
				)
			);
			clean_post_cache( $legacy->ID );
			$page = get_page_by_path( 'app-description' );
		} else {
			kov_seed_access_page();
			$page = get_page_by_path( 'app-description' );
		}
	}

	if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
		update_option( 'kov_app_description_ready', '1' );
	}
}
add_action( 'init', 'kov_ensure_access_page', 21 );

function kov_seed_access_page(): void {
	kov_seed_page(
		'app-description',
		'Доступы',
		'Вход в админку EvaHelp и права пользователя guest.',
		''
	);
}

/**
 * @param array<int, array<string, mixed>> $items Items.
 */
function kov_seed_collection( string $type, array $items ): void {
	$existing = get_posts(
		array(
			'post_type'   => $type,
			'post_status' => array( 'publish', 'draft', 'private', 'trash' ),
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( $existing ) {
		return;
	}

	foreach ( $items as $index => $item ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => $type,
				'post_status'  => 'publish',
				'post_title'   => $item['title'],
				'post_content' => $item['content'],
				'menu_order'   => $index + 1,
			),
			true
		);
		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}
		if ( ! empty( $item['icon'] ) ) {
			update_post_meta( $post_id, '_kov_icon', $item['icon'] );
		}
		if ( isset( $item['price'] ) ) {
			update_post_meta( $post_id, '_kov_price', $item['price'] );
			update_post_meta( $post_id, '_kov_badge', $item['badge'] ?? '' );
			update_post_meta( $post_id, '_kov_featured', ! empty( $item['featured'] ) ? '1' : '' );
		}
	}
}

function kov_seed_page( string $slug, string $title, string $excerpt, string $content ): void {
	$existing = get_page_by_path( $slug );
	if ( $existing instanceof WP_Post ) {
		return;
	}
	wp_insert_post(
		array(
			'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_name'     => $slug,
			'post_title'    => $title,
			'post_excerpt'  => $excerpt,
			'post_content'  => $content,
			'comment_status'=> 'closed',
			'ping_status'   => 'closed',
		)
	);
}

/**
 * @return array<int, array<string, string>>
 */
function kov_seed_services(): array {
	return array(
		array(
			'title'   => 'После поломки',
			'content' => 'Не заводится, сел аккумулятор, перегрев или другое, из-за чего машина не едет дальше.',
			'icon'    => 'breakdown',
		),
		array(
			'title'   => 'После ДТП',
			'content' => 'Заберём автомобиль с места аварии, с трассы или из двора, если к нему можно подъехать.',
			'icon'    => 'accident',
		),
		array(
			'title'   => 'Перевозка автомобилей',
			'content' => 'Доставим на СТО, стоянку, домой или по другому адресу в городе и области.',
			'icon'    => 'transport',
		),
		array(
			'title'   => 'Внедорожники',
			'content' => 'Полный привод, большой клиренс и нестандартная база — грузим на подходящую технику.',
			'icon'    => 'suv',
		),
		array(
			'title'   => 'Межгород',
			'content' => 'Перевозка по Ленобласти и в другой город. Стоимость считаем от километража.',
			'icon'    => 'intercity',
		),
		array(
			'title'   => 'Сложная погрузка',
			'content' => 'Заблокированные колёса, кювет, тесный двор или паркинг — подберём способ погрузки.',
			'icon'    => 'complex',
		),
	);
}

/**
 * @return array<int, array<string, mixed>>
 */
function kov_seed_prices(): array {
	return array(
		array(
			'title'    => 'Легковой автомобиль',
			'content'  => 'Городская подача и стандартная погрузка легкового автомобиля.',
			'price'    => 'от 1 500 ₽',
			'featured' => false,
			'badge'    => '',
		),
		array(
			'title'    => 'Кроссовер',
			'content'  => 'Кроссоверы и автомобили выше легкового класса.',
			'price'    => 'от 2 000 ₽',
			'featured' => true,
			'badge'    => 'Частый заказ',
		),
		array(
			'title'    => 'Межгород',
			'content'  => 'Ставка за километр. Минимальный заказ зависит от маршрута.',
			'price'    => 'от 50 ₽/км',
			'featured' => false,
			'badge'    => '',
		),
	);
}

/**
 * @return array<int, array<string, string>>
 */
function kov_seed_perks(): array {
	return array(
		array(
			'title'   => 'Быстрая подача',
			'content' => 'По Санкт-Петербургу ориентир — от 20 минут. Точное время говорим при звонке.',
			'icon'    => 'speed',
		),
		array(
			'title'   => 'Работа 24/7',
			'content' => 'Выезжаем днём, ночью и в выходные, без паузы на «нерабочие часы».',
			'icon'    => 'always',
		),
		array(
			'title'   => 'Опытные водители',
			'content' => 'Водители регулярно возят легковые, кроссоверы и внедорожники.',
			'icon'    => 'drivers',
		),
		array(
			'title'   => 'Собственная техника',
			'content' => 'Платформы и стропы наши: не ищем машину на стороне в момент заявки.',
			'icon'    => 'fleet',
		),
		array(
			'title'   => 'Без скрытых доплат',
			'content' => 'Цена, названная до выезда, и есть цена поездки.',
			'icon'    => 'price',
		),
	);
}

/**
 * @return array<int, array<string, string>>
 */
function kov_seed_steps(): array {
	return array(
		array(
			'title'   => 'Заявка',
			'content' => 'Звоните или оставьте форму: имя, телефон, откуда забрать и куда везти.',
		),
		array(
			'title'   => 'Расчёт стоимости',
			'content' => 'Называем окончательную цену до выезда. На месте она не меняется.',
		),
		array(
			'title'   => 'Выезд эвакуатора',
			'content' => 'Машина выезжает сразу после согласования. По городу обычно от 20 минут.',
		),
		array(
			'title'   => 'Доставка автомобиля',
			'content' => 'Аккуратно грузим и привозим по адресу, который зафиксировали в заявке.',
		),
	);
}

/**
 * @return array<int, array<string, string>>
 */
function kov_seed_faq(): array {
	return array(
		array(
			'title'   => 'Сколько ждать эвакуатор?',
			'content' => 'По Санкт-Петербургу обычно 20–40 минут. Если вы на трассе или в области, время считаем по маршруту и называем его до выезда.',
		),
		array(
			'title'   => 'Меняется ли цена после приезда?',
			'content' => 'Нет. Окончательную стоимость фиксируем до выезда. На месте она не растёт, если ситуация совпадает с тем, что вы описали.',
		),
		array(
			'title'   => 'Что делать, если колёса заблокированы?',
			'content' => 'Напишите об этом в заявке или скажите по телефону. Подберём погрузку: лебёдку, подкатные тележки или частичную погрузку — и сразу включим это в цену.',
		),
		array(
			'title'   => 'Эвакуируете после ДТП и ночью?',
			'content' => 'Да. Забираем машины после аварии и выезжаем круглосуточно. Ночной выезд тоже считаем заранее, без доплаты на месте.',
		),
		array(
			'title'   => 'Можно увезти внедорожник или уехать в другой город?',
			'content' => 'Да. Внедорожники и межгород считаем отдельно: для города — от стоимости по классу авто, для трассы — от 50 ₽/км.',
		),
		array(
			'title'   => 'Как можно оплатить?',
			'content' => 'Наличными или переводом на карту после доставки автомобиля.',
		),
	);
}

function kov_seed_policy_html(): string {
	return <<<'HTML'
<p><strong>ПОЛИТИКА ОБРАБОТКИ ПЕРСОНАЛЬНЫХ ДАННЫХ</strong></p>
<p>1. Общие положения.<br>Настоящая Политика определяет порядок обработки персональных данных пользователей сайта.</p>
<p>2. Оператор персональных данных.<br>Физическое лицо: [kov_operator].<br>Телефон: [kov_phone].<br>E-mail: [kov_email].<br>Местонахождение: [kov_city].</p>
<p>3. Персональные данные.<br>Обрабатываются: имя, номер телефона, сведения, указанные пользователем в формах сайта, а также технические данные, автоматически передаваемые браузером (IP-адрес, cookies, сведения о браузере и устройстве).</p>
<p>4. Цели обработки.<br>Обратная связь с пользователями, прием и обработка заявок, оказание услуг эвакуации, информирование по обращениям, исполнение требований законодательства.</p>
<p>5. Правовые основания.<br>Обработка осуществляется на основании Федерального закона РФ №152-ФЗ «О персональных данных», согласия пользователя и иных применимых норм законодательства.</p>
<p>6. Передача данных.<br>Персональные данные не передаются третьим лицам, кроме случаев, предусмотренных законодательством РФ либо необходимых для работы сайта и обработки обращений.</p>
<p>7. Хранение.<br>Данные хранятся не дольше, чем это необходимо для достижения целей обработки или исполнения требований законодательства.</p>
<p>8. Права пользователя.<br>Пользователь вправе запросить доступ к своим данным, их уточнение, удаление, ограничение обработки и отозвать согласие, направив обращение на [kov_email].</p>
<p>9. Защита данных.<br>Оператор принимает необходимые организационные и технические меры для защиты персональных данных.</p>
<p>10. Заключительные положения.<br>Политика действует бессрочно до принятия новой редакции.</p>
HTML;
}

function kov_seed_consent_html(): string {
	return <<<'HTML'
<p><strong>СОГЛАСИЕ НА ОБРАБОТКУ ПЕРСОНАЛЬНЫХ ДАННЫХ</strong></p>
<p>Оставляя свои данные на сайте, я свободно, своей волей и в своем интересе даю согласие [kov_operator_to] на обработку моих персональных данных в соответствии с Федеральным законом №152-ФЗ «О персональных данных».</p>
<p>Я соглашаюсь на обработку следующих данных: имя, номер телефона и иных сведений, добровольно указанных в формах сайта.</p>
<p>Цели обработки: обратная связь, обработка заявок, предоставление консультаций, оказание услуг эвакуации.</p>
<p>Оператор вправе осуществлять сбор, запись, систематизацию, хранение, уточнение, использование, удаление и уничтожение персональных данных.</p>
<p>Согласие действует до достижения целей обработки либо до его отзыва.</p>
<p>Отозвать согласие можно, направив обращение на адрес электронной почты: [kov_email].</p>
<p>Отправляя форму на сайте, пользователь подтверждает ознакомление с настоящим согласием и принимает его условия.</p>
HTML;
}
