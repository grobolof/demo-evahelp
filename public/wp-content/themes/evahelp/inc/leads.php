<?php
/**
 * Evacuator requests from the landing form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string>
 */
function kov_lead_statuses(): array {
	return array(
		'new'  => 'Новая',
		'work' => 'В работе',
		'done' => 'Выполнена',
	);
}

function kov_lead_status( int $post_id ): string {
	$status = (string) get_post_meta( $post_id, '_kov_status', true );
	$known  = kov_lead_statuses();
	return isset( $known[ $status ] ) ? $status : 'new';
}

function kov_count_leads( string $status = '' ): int {
	$args = array(
		'post_type'      => 'kov_lead',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	);
	if ( $status !== '' ) {
		$args['meta_key']   = '_kov_status';
		$args['meta_value'] = $status;
	}
	$query = new WP_Query( $args );
	return (int) $query->found_posts;
}

/**
 * @return array{ok: bool, message: string}
 */
function kov_process_lead(): array {
	$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'kov_lead' ) ) {
		return array(
			'ok'      => false,
			'message' => 'Обновите страницу и отправьте заявку ещё раз.',
		);
	}

	$honeypot = isset( $_POST['company'] ) ? trim( (string) wp_unslash( $_POST['company'] ) ) : '';
	if ( $honeypot !== '' ) {
		return array(
			'ok'      => true,
			'message' => 'Заявка принята. Перезвоним, уточним детали и назовём стоимость до выезда.',
		);
	}

	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$ip  = mb_substr( $ip, 0, 64 );
	$key = 'kov_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= 8 ) {
		return array(
			'ok'      => false,
			'message' => 'Слишком много заявок с этого адреса. Позвоните, пожалуйста.',
		);
	}
	set_transient( $key, $hits + 1, HOUR_IN_SECONDS );

	$name    = isset( $_POST['lead_name'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_name'] ) ) : '';
	$phone   = isset( $_POST['lead_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_phone'] ) ) : '';
	$from    = isset( $_POST['lead_from'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_from'] ) ) : '';
	$to      = isset( $_POST['lead_to'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_to'] ) ) : '';
	$note    = isset( $_POST['lead_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['lead_note'] ) ) : '';
	$consent = isset( $_POST['lead_consent'] ) && (string) wp_unslash( $_POST['lead_consent'] ) === '1';
	$digits  = preg_replace( '/\D+/', '', $phone );
	$digits  = is_string( $digits ) ? $digits : '';

	$name  = mb_substr( $name, 0, 80 );
	$phone = mb_substr( $phone, 0, 30 );
	$from  = mb_substr( $from, 0, 160 );
	$to    = mb_substr( $to, 0, 160 );
	$note  = mb_substr( $note, 0, 800 );

	if ( mb_strlen( $name ) < 2 || ! preg_match( '/^7\d{10}$/', $digits ) || mb_strlen( $from ) < 3 || mb_strlen( $to ) < 3 || mb_strlen( $note ) < 3 || ! $consent ) {
		return array(
			'ok'      => false,
			'message' => 'Проверьте имя, телефон, адреса и согласие на обработку данных.',
		);
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'kov_lead',
			'post_status'  => 'publish',
			'post_title'   => $name . ' — ' . $phone,
			'post_content' => $note,
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return array(
			'ok'      => false,
			'message' => 'Не получилось сохранить заявку. Позвоните нам.',
		);
	}

	update_post_meta( $post_id, '_kov_name', $name );
	update_post_meta( $post_id, '_kov_phone', $phone );
	update_post_meta( $post_id, '_kov_from', $from );
	update_post_meta( $post_id, '_kov_to', $to );
	update_post_meta( $post_id, '_kov_note', $note );
	update_post_meta( $post_id, '_kov_status', 'new' );
	update_post_meta( $post_id, '_kov_consent', '1' );
	update_post_meta( $post_id, '_kov_ip', $ip );

	$mail_to = kov( 'notify_email' );
	$sent    = false;
	if ( $mail_to !== '' && is_email( $mail_to ) && kov_mail_transport_ready() ) {
		$subject = 'Заявка: эвакуатор — ' . str_replace( array( "\r", "\n" ), ' ', $name );
		$body    = implode(
			"\n",
			array(
				'Имя: ' . $name,
				'Телефон: ' . $phone,
				'Откуда: ' . $from,
				'Куда: ' . $to,
				'Ситуация: ' . $note,
				'',
				'Открыть в админке: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
			)
		);
		$sent = (bool) wp_mail( $mail_to, $subject, $body );
	}
	update_post_meta( $post_id, '_kov_mail_sent', $sent ? '1' : '0' );

	return array(
		'ok'      => true,
		'message' => 'Заявка принята. Перезвоним, уточним детали и назовём стоимость до выезда.',
	);
}

/**
 * System sendmail in this image can block for a minute when nothing accepts mail.
 * Mailpit and a local SMTP server both answer quickly, so only those get wp_mail().
 */
function kov_mail_transport_ready(): bool {
	$cached = get_transient( 'kov_mail_ready' );
	if ( '1' === $cached || '0' === $cached ) {
		return '1' === $cached;
	}

	$path  = (string) ini_get( 'sendmail_path' );
	$ready = str_contains( $path, 'mailpit' );
	if ( ! $ready ) {
		$errno  = 0;
		$errstr = '';
		$socket = @fsockopen( '127.0.0.1', 25, $errno, $errstr, 1 );
		$ready  = is_resource( $socket );
		if ( $ready ) {
			fclose( $socket );
		}
	}

	set_transient( 'kov_mail_ready', $ready ? '1' : '0', 10 * MINUTE_IN_SECONDS );
	return $ready;
}

function kov_lead_via_ajax(): void {
	$result = kov_process_lead();
	if ( $result['ok'] ) {
		wp_send_json_success( array( 'message' => $result['message'] ) );
	}
	wp_send_json_error( array( 'message' => $result['message'] ), 400 );
}
add_action( 'wp_ajax_kov_submit_lead', 'kov_lead_via_ajax' );
add_action( 'wp_ajax_nopriv_kov_submit_lead', 'kov_lead_via_ajax' );

function kov_lead_via_post(): void {
	$result = kov_process_lead();
	$flag   = $result['ok'] ? 'ok' : 'err';
	wp_safe_redirect( add_query_arg( 'lead', $flag, home_url( '/' ) ) . '#request' );
	exit;
}
add_action( 'admin_post_kov_submit_lead', 'kov_lead_via_post' );
add_action( 'admin_post_nopriv_kov_submit_lead', 'kov_lead_via_post' );

function kov_lead_meta_box(): void {
	add_meta_box( 'kov_lead_details', 'Данные заявки', 'kov_render_lead_box', 'kov_lead', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'kov_lead_meta_box' );

function kov_render_lead_box( WP_Post $post ): void {
	wp_nonce_field( 'kov_lead_admin', 'kov_lead_admin_nonce' );
	$name    = (string) get_post_meta( $post->ID, '_kov_name', true );
	$phone   = (string) get_post_meta( $post->ID, '_kov_phone', true );
	$from    = (string) get_post_meta( $post->ID, '_kov_from', true );
	$to      = (string) get_post_meta( $post->ID, '_kov_to', true );
	$note    = (string) get_post_meta( $post->ID, '_kov_note', true );
	if ( $note === '' ) {
		$note = $post->post_content;
	}
	$status  = kov_lead_status( $post->ID );
	$consent = (string) get_post_meta( $post->ID, '_kov_consent', true ) === '1';
	$ip      = (string) get_post_meta( $post->ID, '_kov_ip', true );
	$mailed  = (string) get_post_meta( $post->ID, '_kov_mail_sent', true );

	echo '<table class="form-table" role="presentation"><tbody>';
	kov_lead_field( 'Имя', 'kov_name', $name );
	kov_lead_field( 'Телефон', 'kov_phone', $phone );
	kov_lead_field( 'Откуда забрать', 'kov_from', $from );
	kov_lead_field( 'Куда доставить', 'kov_to', $to );
	echo '<tr><th scope="row"><label for="kov_note">Ситуация</label></th><td>';
	printf( '<textarea class="large-text" rows="5" id="kov_note" name="kov_note">%s</textarea>', esc_textarea( $note ) );
	echo '</td></tr>';
	echo '<tr><th scope="row"><label for="kov_status">Статус</label></th><td><select id="kov_status" name="kov_status">';
	foreach ( kov_lead_statuses() as $value => $label ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $value ),
			selected( $status, $value, false ),
			esc_html( $label )
		);
	}
	echo '</select></td></tr>';
	echo '<tr><th scope="row">Согласие</th><td><label><input type="checkbox" name="kov_consent" value="1" ' . checked( $consent, true, false ) . '> Согласие на обработку персональных данных получено</label></td></tr>';
	if ( $ip !== '' ) {
		echo '<tr><th scope="row">IP</th><td>' . esc_html( $ip ) . '</td></tr>';
	}
	if ( $mailed !== '' ) {
		echo '<tr><th scope="row">Письмо</th><td>' . ( $mailed === '1' ? 'Отправлено' : 'Не отправлено, заявка сохранена в админке' ) . '</td></tr>';
	}
	echo '</tbody></table>';
	echo '<p class="description">Заголовок в списке собирается из имени и телефона.</p>';
}

function kov_lead_field( string $label, string $name, string $value ): void {
	echo '<tr><th scope="row"><label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label></th><td>';
	printf(
		'<input type="text" class="regular-text" id="%1$s" name="%1$s" value="%2$s">',
		esc_attr( $name ),
		esc_attr( $value )
	);
	echo '</td></tr>';
}

function kov_save_lead_admin( int $post_id ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['kov_lead_admin_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kov_lead_admin_nonce'] ) ), 'kov_lead_admin' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$name  = isset( $_POST['kov_name'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['kov_name'] ) ), 0, 80 ) : '';
	$phone = isset( $_POST['kov_phone'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['kov_phone'] ) ), 0, 30 ) : '';
	$from  = isset( $_POST['kov_from'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['kov_from'] ) ), 0, 160 ) : '';
	$to    = isset( $_POST['kov_to'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['kov_to'] ) ), 0, 160 ) : '';
	$note  = isset( $_POST['kov_note'] ) ? mb_substr( sanitize_textarea_field( wp_unslash( $_POST['kov_note'] ) ), 0, 800 ) : '';
	$status = isset( $_POST['kov_status'] ) ? sanitize_key( wp_unslash( $_POST['kov_status'] ) ) : 'new';
	if ( ! isset( kov_lead_statuses()[ $status ] ) ) {
		$status = 'new';
	}

	update_post_meta( $post_id, '_kov_name', $name );
	update_post_meta( $post_id, '_kov_phone', $phone );
	update_post_meta( $post_id, '_kov_from', $from );
	update_post_meta( $post_id, '_kov_to', $to );
	update_post_meta( $post_id, '_kov_note', $note );
	update_post_meta( $post_id, '_kov_status', $status );
	update_post_meta( $post_id, '_kov_consent', empty( $_POST['kov_consent'] ) ? '' : '1' );

	$title = trim( $name . ( $phone !== '' ? ' — ' . $phone : '' ) );
	if ( $title === '' ) {
		$title = 'Заявка';
	}

	remove_action( 'save_post_kov_lead', 'kov_save_lead_admin' );
	wp_update_post(
		array(
			'ID'           => $post_id,
			'post_title'   => $title,
			'post_content' => $note,
		)
	);
	add_action( 'save_post_kov_lead', 'kov_save_lead_admin' );
}
add_action( 'save_post_kov_lead', 'kov_save_lead_admin' );

/**
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function kov_lead_columns( array $columns ): array {
	$columns = array(
		'cb'         => $columns['cb'] ?? '',
		'title'      => 'Заявка',
		'kov_phone'  => 'Телефон',
		'kov_from'   => 'Откуда',
		'kov_to'     => 'Куда',
		'kov_note'   => 'Ситуация',
		'kov_status' => 'Статус',
		'date'       => 'Дата',
	);
	return $columns;
}
add_filter( 'manage_kov_lead_posts_columns', 'kov_lead_columns' );

function kov_lead_column( string $column, int $post_id ): void {
	if ( 'kov_phone' === $column ) {
		$phone = (string) get_post_meta( $post_id, '_kov_phone', true );
		$href  = preg_replace( '/[^\d+]/', '', $phone );
		if ( is_string( $href ) && $href !== '' ) {
			echo '<a href="' . esc_url( 'tel:' . $href ) . '">' . esc_html( $phone ) . '</a>';
		}
		return;
	}
	if ( 'kov_from' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, '_kov_from', true ) );
		return;
	}
	if ( 'kov_to' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, '_kov_to', true ) );
		return;
	}
	if ( 'kov_note' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, '_kov_note', true ) );
		return;
	}
	if ( 'kov_status' === $column ) {
		$status = kov_lead_status( $post_id );
		$labels = kov_lead_statuses();
		printf(
			'<span class="kov-status kov-status-%s">%s</span>',
			esc_attr( $status ),
			esc_html( $labels[ $status ] )
		);
	}
}
add_action( 'manage_kov_lead_posts_custom_column', 'kov_lead_column', 10, 2 );

/**
 * @param array<string, string> $views Views.
 * @return array<string, string>
 */
function kov_lead_views( array $views ): array {
	$current     = isset( $_GET['kov_status'] ) ? sanitize_key( wp_unslash( $_GET['kov_status'] ) ) : '';
	$post_status = isset( $_GET['post_status'] ) ? sanitize_key( wp_unslash( $_GET['post_status'] ) ) : '';
	$base        = admin_url( 'edit.php?post_type=kov_lead' );
	$custom      = array();

	$all_class = ( $current === '' && $post_status !== 'trash' ) ? ' class="current"' : '';
	$custom['all'] = '<a href="' . esc_url( $base ) . '"' . $all_class . '>Все <span class="count">(' . kov_count_leads() . ')</span></a>';

	foreach ( kov_lead_statuses() as $key => $label ) {
		$class = ( $current === $key && $post_status !== 'trash' ) ? ' class="current"' : '';
		$url   = add_query_arg( 'kov_status', $key, $base );
		$custom[ $key ] = '<a href="' . esc_url( $url ) . '"' . $class . '>' . esc_html( $label ) . ' <span class="count">(' . kov_count_leads( $key ) . ')</span></a>';
	}

	if ( isset( $views['trash'] ) && ! kov_is_lead_viewer() ) {
		$custom['trash'] = $views['trash'];
	}
	return $custom;
}
add_filter( 'views_edit-kov_lead', 'kov_lead_views' );

function kov_filter_leads_by_status( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() || $query->get( 'post_type' ) !== 'kov_lead' ) {
		return;
	}
	$status = isset( $_GET['kov_status'] ) ? sanitize_key( wp_unslash( $_GET['kov_status'] ) ) : '';
	$trash  = isset( $_GET['post_status'] ) && sanitize_key( wp_unslash( $_GET['post_status'] ) ) === 'trash';
	if ( $trash || ! isset( kov_lead_statuses()[ $status ] ) ) {
		return;
	}
	$query->set(
		'meta_query',
		array(
			array(
				'key'   => '_kov_status',
				'value' => $status,
			),
		)
	);
}
add_action( 'pre_get_posts', 'kov_filter_leads_by_status' );

/**
 * @param array<string, string> $actions Row actions.
 */
function kov_lead_status_actions( array $actions, WP_Post $post ): array {
	if ( $post->post_type !== 'kov_lead' ) {
		return $actions;
	}
	if ( kov_is_lead_viewer() ) {
		if ( ! current_user_can( 'delete_post', $post->ID ) ) {
			return array();
		}
		$url = wp_nonce_url(
			admin_url( 'admin-post.php?action=kov_lead_delete&post=' . $post->ID ),
			'kov_lead_delete_' . $post->ID
		);
		return array(
			'kov_delete' => '<a href="' . esc_url( $url ) . '" class="submitdelete" onclick="return confirm(\'Удалить заявку из базы без возможности восстановления?\');">Удалить</a>',
		);
	}
	$current = kov_lead_status( $post->ID );
	foreach ( kov_lead_statuses() as $key => $label ) {
		if ( $key === $current ) {
			continue;
		}
		$url = wp_nonce_url(
			admin_url( 'admin-post.php?action=kov_lead_status&post=' . $post->ID . '&status=' . $key ),
			'kov_lead_status_' . $post->ID
		);
		$actions[ 'kov_' . $key ] = '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
	return $actions;
}
add_filter( 'post_row_actions', 'kov_lead_status_actions', 20, 2 );

function kov_lead_status_action(): void {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	$status  = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';
	check_admin_referer( 'kov_lead_status_' . $post_id );

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die( esc_html( 'Недостаточно прав.' ) );
	}
	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post || $post->post_type !== 'kov_lead' ) {
		wp_die( esc_html( 'Заявка не найдена.' ) );
	}
	if ( ! isset( kov_lead_statuses()[ $status ] ) ) {
		wp_die( esc_html( 'Неизвестный статус.' ) );
	}

	update_post_meta( $post_id, '_kov_status', $status );
	$back = wp_get_referer();
	wp_safe_redirect( $back ? $back : admin_url( 'edit.php?post_type=kov_lead' ) );
	exit;
}
add_action( 'admin_post_kov_lead_status', 'kov_lead_status_action' );

function kov_delete_lead( int $post_id ): bool {
	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post || 'kov_lead' !== $post->post_type ) {
		return false;
	}
	if ( ! current_user_can( 'delete_post', $post_id ) ) {
		return false;
	}
	$result = wp_delete_post( $post_id, true );
	return $result instanceof WP_Post;
}

function kov_lead_delete_action(): void {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	check_admin_referer( 'kov_lead_delete_' . $post_id );

	if ( ! kov_delete_lead( $post_id ) ) {
		wp_die( esc_html( 'Не удалось удалить заявку.' ) );
	}

	$back = wp_get_referer();
	$back = $back ? remove_query_arg( 'kov_deleted', $back ) : admin_url( 'edit.php?post_type=kov_lead' );
	wp_safe_redirect( add_query_arg( 'kov_deleted', '1', $back ) );
	exit;
}
add_action( 'admin_post_kov_lead_delete', 'kov_lead_delete_action' );

function kov_lead_deleted_notice(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'edit-kov_lead' !== $screen->id || ! isset( $_GET['kov_deleted'] ) ) {
		return;
	}
	$count = absint( wp_unslash( $_GET['kov_deleted'] ) );
	if ( $count < 1 ) {
		return;
	}
	$text = 1 === $count
		? 'Заявка удалена из базы.'
		: sprintf( 'Удалено заявок: %d. Записи убраны из базы.', $count );
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $text ) . '</p></div>';
}
add_action( 'admin_notices', 'kov_lead_deleted_notice' );

function kov_lead_dashboard_widget(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}
	wp_add_dashboard_widget( 'kov_leads_widget', 'Заявки на эвакуатор', 'kov_render_leads_widget' );
}
add_action( 'wp_dashboard_setup', 'kov_lead_dashboard_widget' );

function kov_render_leads_widget(): void {
	$leads = get_posts(
		array(
			'post_type'   => 'kov_lead',
			'post_status' => 'publish',
			'numberposts' => 5,
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);
	$new = kov_count_leads( 'new' );
	echo '<p>Новых заявок: <strong>' . (int) $new . '</strong>. ';
	echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=kov_lead' ) ) . '">Открыть все</a></p>';
	if ( ! $leads ) {
		echo '<p>Заявок пока нет. Они появятся после отправки формы на сайте.</p>';
		return;
	}
	echo '<table class="widefat striped"><tbody>';
	foreach ( $leads as $lead ) {
		$status = kov_lead_status( $lead->ID );
		$labels = kov_lead_statuses();
		echo '<tr><td><a href="' . esc_url( get_edit_post_link( $lead->ID ) ) . '">' . esc_html( get_the_title( $lead ) ) . '</a><br><span class="description">' . esc_html( (string) get_post_meta( $lead->ID, '_kov_from', true ) ) . ' → ' . esc_html( (string) get_post_meta( $lead->ID, '_kov_to', true ) ) . '</span></td>';
		echo '<td>' . esc_html( $labels[ $status ] ) . '</td></tr>';
	}
	echo '</tbody></table>';
}
