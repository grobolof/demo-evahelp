<?php
/**
 * Content collections shown on the landing and edited in admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return string[]
 */
function kov_content_types(): array {
	return array( 'kov_service', 'kov_price', 'kov_perk', 'kov_step', 'kov_faq' );
}

function kov_register_post_types(): void {
	$common = array(
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => 'kovalski',
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => false,
		'show_in_rest'        => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'supports'            => array( 'title', 'editor', 'page-attributes' ),
	);

	register_post_type(
		'kov_lead',
		array_merge(
			$common,
			array(
				'labels'   => kov_labels(
					array(
						'name'          => 'Заявки',
						'singular_name' => 'Заявка',
						'add_new_item'  => 'Добавить заявку',
						'edit_item'     => 'Заявка',
						'all_items'     => 'Заявки',
						'menu_name'     => 'Заявки',
						'not_found'     => 'Заявок пока нет',
					)
				),
				'supports' => array( 'title' ),
			)
		)
	);

	register_post_type(
		'kov_service',
		array_merge(
			$common,
			array(
				'labels' => kov_labels(
					array(
						'name'          => 'Услуги',
						'singular_name' => 'Услуга',
						'add_new_item'  => 'Добавить услугу',
						'edit_item'     => 'Редактировать услугу',
						'all_items'     => 'Услуги',
						'menu_name'     => 'Услуги',
					)
				),
			)
		)
	);

	register_post_type(
		'kov_price',
		array_merge(
			$common,
			array(
				'labels' => kov_labels(
					array(
						'name'          => 'Цены',
						'singular_name' => 'Цена',
						'add_new_item'  => 'Добавить цену',
						'edit_item'     => 'Редактировать цену',
						'all_items'     => 'Цены',
						'menu_name'     => 'Цены',
					)
				),
			)
		)
	);

	register_post_type(
		'kov_perk',
		array_merge(
			$common,
			array(
				'labels' => kov_labels(
					array(
						'name'          => 'Преимущества',
						'singular_name' => 'Преимущество',
						'add_new_item'  => 'Добавить преимущество',
						'edit_item'     => 'Редактировать преимущество',
						'all_items'     => 'Преимущества',
						'menu_name'     => 'Преимущества',
					)
				),
			)
		)
	);

	register_post_type(
		'kov_step',
		array_merge(
			$common,
			array(
				'labels' => kov_labels(
					array(
						'name'          => 'Шаги',
						'singular_name' => 'Шаг',
						'add_new_item'  => 'Добавить шаг',
						'edit_item'     => 'Редактировать шаг',
						'all_items'     => 'Шаги',
						'menu_name'     => 'Шаги',
					)
				),
			)
		)
	);

	register_post_type(
		'kov_faq',
		array_merge(
			$common,
			array(
				'labels' => kov_labels(
					array(
						'name'          => 'Вопросы',
						'singular_name' => 'Вопрос',
						'add_new_item'  => 'Добавить вопрос',
						'edit_item'     => 'Редактировать вопрос',
						'all_items'     => 'Вопросы',
						'menu_name'     => 'Вопросы',
					)
				),
			)
		)
	);
}
add_action( 'init', 'kov_register_post_types', 5 );

/**
 * @param array<string, string> $labels Custom labels.
 * @return array<string, string>
 */
function kov_labels( array $labels ): array {
	return array_merge(
		array(
			'add_new'           => 'Добавить',
			'new_item'          => 'Новая запись',
			'view_item'         => 'Смотреть',
			'search_items'      => 'Искать',
			'not_found'         => 'Ничего не найдено',
			'not_found_in_trash'=> 'В корзине пусто',
			'all_items'         => 'Все записи',
			'menu_name'         => 'Записи',
		),
		$labels
	);
}

function kov_content_meta_boxes(): void {
	add_meta_box( 'kov_service_icon', 'Иконка', 'kov_render_icon_box', 'kov_service', 'side', 'high' );
	add_meta_box( 'kov_perk_icon', 'Иконка', 'kov_render_icon_box', 'kov_perk', 'side', 'high' );
	add_meta_box( 'kov_price_details', 'Стоимость', 'kov_render_price_box', 'kov_price', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'kov_content_meta_boxes' );

function kov_render_icon_box( WP_Post $post ): void {
	wp_nonce_field( 'kov_content_meta', 'kov_content_nonce' );
	$group  = 'kov_perk' === $post->post_type ? 'perk' : 'service';
	$choices = kov_icon_choices( $group );
	$current = (string) get_post_meta( $post->ID, '_kov_icon', true );
	if ( ! isset( $choices[ $current ] ) ) {
		$current = (string) array_key_first( $choices );
	}

	echo '<p><label for="kov_icon">Иконка карточки</label></p>';
	echo '<select id="kov_icon" name="kov_icon" style="width:100%">';
	foreach ( $choices as $value => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $value ),
			selected( $current, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select>';
	echo '<p class="description">Заголовок — название карточки, основной текст — описание. Порядок на сайте задаётся полем «Порядок»: чем меньше число, тем выше карточка.</p>';
}

function kov_render_price_box( WP_Post $post ): void {
	wp_nonce_field( 'kov_content_meta', 'kov_content_nonce' );
	$price    = (string) get_post_meta( $post->ID, '_kov_price', true );
	$badge    = (string) get_post_meta( $post->ID, '_kov_badge', true );
	$featured = (string) get_post_meta( $post->ID, '_kov_featured', true ) === '1';

	echo '<p><label for="kov_price">Сумма</label></p>';
	printf(
		'<input type="text" class="widefat" id="kov_price" name="kov_price" value="%s" placeholder="от 1 500 ₽">',
		esc_attr( $price )
	);
	echo '<p><label for="kov_badge">Бейдж</label></p>';
	printf(
		'<input type="text" class="widefat" id="kov_badge" name="kov_badge" value="%s" placeholder="Частый заказ">',
		esc_attr( $badge )
	);
	echo '<p><label><input type="checkbox" name="kov_featured" value="1" ' . checked( $featured, true, false ) . '> Выделить карточку</label></p>';
	echo '<p class="description">Заголовок — название тарифа, основной текст — пояснение. Бейдж виден, если карточка выделена и подпись не пустая.</p>';
}

function kov_save_content_meta( int $post_id ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['kov_content_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kov_content_nonce'] ) ), 'kov_content_meta' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$type = get_post_type( $post_id );
	if ( in_array( $type, array( 'kov_service', 'kov_perk' ), true ) ) {
		$group   = 'kov_perk' === $type ? 'perk' : 'service';
		$choices = kov_icon_choices( $group );
		$icon    = isset( $_POST['kov_icon'] ) ? sanitize_key( wp_unslash( $_POST['kov_icon'] ) ) : '';
		if ( ! isset( $choices[ $icon ] ) ) {
			$icon = (string) array_key_first( $choices );
		}
		update_post_meta( $post_id, '_kov_icon', $icon );
	}

	if ( 'kov_price' === $type ) {
		$price = isset( $_POST['kov_price'] ) ? sanitize_text_field( wp_unslash( $_POST['kov_price'] ) ) : '';
		$badge = isset( $_POST['kov_badge'] ) ? sanitize_text_field( wp_unslash( $_POST['kov_badge'] ) ) : '';
		update_post_meta( $post_id, '_kov_price', mb_substr( $price, 0, 80 ) );
		update_post_meta( $post_id, '_kov_badge', mb_substr( $badge, 0, 40 ) );
		update_post_meta( $post_id, '_kov_featured', empty( $_POST['kov_featured'] ) ? '' : '1' );
	}
}
add_action( 'save_post', 'kov_save_content_meta' );

function kov_content_admin_columns( array $columns ): array {
	$screen = get_current_screen();
	$type   = $screen ? $screen->post_type : '';
	$next   = array(
		'cb'    => $columns['cb'] ?? '',
		'title' => 'kov_faq' === $type ? 'Вопрос' : 'Название',
	);

	if ( 'kov_price' === $type ) {
		$next['kov_price'] = 'Сумма';
	}
	if ( in_array( $type, array( 'kov_service', 'kov_perk' ), true ) ) {
		$next['kov_icon'] = 'Иконка';
	}

	$next['kov_excerpt'] = 'kov_faq' === $type ? 'Ответ' : 'Текст';
	$next['kov_order']   = 'Порядок';
	$next['date']        = 'Дата';
	return $next;
}

function kov_content_admin_column( string $column, int $post_id ): void {
	if ( 'kov_price' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, '_kov_price', true ) );
		if ( (string) get_post_meta( $post_id, '_kov_featured', true ) === '1' ) {
			echo ' <span class="kov-status kov-status-work">выделена</span>';
		}
		return;
	}

	if ( 'kov_icon' === $column ) {
		$type    = get_post_type( $post_id );
		$group   = 'kov_perk' === $type ? 'perk' : 'service';
		$choices = kov_icon_choices( $group );
		$icon    = (string) get_post_meta( $post_id, '_kov_icon', true );
		echo esc_html( $choices[ $icon ] ?? $icon );
		return;
	}

	if ( 'kov_excerpt' === $column ) {
		echo esc_html( wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 18, '…' ) );
		return;
	}

	if ( 'kov_order' === $column ) {
		$post = get_post( $post_id );
		echo $post instanceof WP_Post ? (int) $post->menu_order : 0;
	}
}

function kov_register_content_columns(): void {
	foreach ( kov_content_types() as $type ) {
		add_filter( "manage_{$type}_posts_columns", 'kov_content_admin_columns' );
		add_action( "manage_{$type}_posts_custom_column", 'kov_content_admin_column', 10, 2 );
	}
}
add_action( 'admin_init', 'kov_register_content_columns' );

function kov_sort_content_admin( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$type = $query->get( 'post_type' );
	if ( ! in_array( $type, kov_content_types(), true ) ) {
		return;
	}
	if ( $query->get( 'orderby' ) ) {
		return;
	}
	$query->set(
		'orderby',
		array(
			'menu_order' => 'ASC',
			'date'       => 'ASC',
		)
	);
}
add_action( 'pre_get_posts', 'kov_sort_content_admin' );

/**
 * @param string               $title Placeholder.
 * @param WP_Post|null         $post  Current post.
 */
function kov_title_placeholder( string $title, $post ): string {
	if ( ! $post instanceof WP_Post ) {
		return $title;
	}
	$map = array(
		'kov_service' => 'Название услуги',
		'kov_price'   => 'Название тарифа',
		'kov_perk'    => 'Заголовок преимущества',
		'kov_step'    => 'Название шага',
		'kov_faq'     => 'Вопрос',
		'kov_lead'    => 'Имя и телефон',
	);
	return $map[ $post->post_type ] ?? $title;
}
add_filter( 'enter_title_here', 'kov_title_placeholder', 10, 2 );

/**
 * @param array<string, string> $actions Row actions.
 */
function kov_content_row_actions( array $actions, WP_Post $post ): array {
	if ( in_array( $post->post_type, kov_content_types(), true ) || 'kov_lead' === $post->post_type ) {
		unset( $actions['view'] );
	}
	if ( 'kov_lead' === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'] );
	}
	return $actions;
}
add_filter( 'post_row_actions', 'kov_content_row_actions', 10, 2 );

function kov_disable_block_editor( bool $use, string $post_type ): bool {
	if ( str_starts_with( $post_type, 'kov_' ) ) {
		return false;
	}
	return $use;
}
add_filter( 'use_block_editor_for_post_type', 'kov_disable_block_editor', 10, 2 );
