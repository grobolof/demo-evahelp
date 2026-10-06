<?php
/**
 * View-only access to the leads list.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string>
 */
function kov_lead_capabilities(): array {
	return array(
		'edit_post'              => 'edit_kov_lead',
		'read_post'              => 'read_kov_lead',
		'delete_post'            => 'delete_kov_lead',
		'edit_posts'             => 'kov_view_leads',
		'edit_others_posts'      => 'edit_kov_leads',
		'delete_posts'           => 'delete_kov_leads',
		'publish_posts'          => 'publish_kov_leads',
		'read_private_posts'     => 'read_private_kov_leads',
		'create_posts'           => 'create_kov_leads',
		'delete_others_posts'    => 'delete_others_kov_leads',
		'delete_private_posts'   => 'delete_private_kov_leads',
		'delete_published_posts' => 'delete_published_kov_leads',
		'edit_private_posts'     => 'edit_private_kov_leads',
		'edit_published_posts'   => 'edit_published_kov_leads',
	);
}

function kov_register_guest_role(): void {
	$caps = array( 'read' => true, 'kov_view_leads' => true );
	$role = get_role( 'kov_guest' );
	if ( ! $role ) {
		add_role( 'kov_guest', 'Просмотр заявок', $caps );
	} else {
		foreach ( $caps as $cap => $grant ) {
			$role->add_cap( $cap, $grant );
		}
	}

	$admin = get_role( 'administrator' );
	if ( ! $admin ) {
		return;
	}
	foreach ( kov_lead_capabilities() as $cap ) {
		$admin->add_cap( $cap );
	}
}
add_action( 'init', 'kov_register_guest_role', 4 );

function kov_is_lead_viewer(): bool {
	$user = wp_get_current_user();
	if ( ! $user instanceof WP_User || ! $user->exists() ) {
		return false;
	}
	return in_array( 'kov_guest', (array) $user->roles, true );
}

/**
 * @param string           $redirect Requested redirect.
 * @param string           $requested Requested URL.
 * @param WP_User|WP_Error $user Logged-in user.
 */
function kov_guest_login_redirect( string $redirect, string $requested, $user ): string {
	if ( $user instanceof WP_User && in_array( 'kov_guest', (array) $user->roles, true ) ) {
		return admin_url( 'edit.php?post_type=kov_lead' );
	}
	return $redirect;
}
add_filter( 'login_redirect', 'kov_guest_login_redirect', 10, 3 );

function kov_guest_lock_admin(): void {
	if ( ! kov_is_lead_viewer() || wp_doing_ajax() ) {
		return;
	}

	global $pagenow;
	$script = is_string( $pagenow ) ? $pagenow : '';
	if ( in_array( $script, array( 'admin-ajax.php', 'load-styles.php', 'load-scripts.php', 'async-upload.php' ), true ) ) {
		return;
	}

	$type   = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
	$status = isset( $_GET['post_status'] ) ? sanitize_key( wp_unslash( $_GET['post_status'] ) ) : '';
	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
	$on_list = 'edit.php' === $script && 'kov_lead' === $type && 'trash' !== $status && ! in_array( $action, array( 'edit', 'trash', 'delete', 'untrash' ), true );

	if ( $on_list ) {
		return;
	}

	wp_safe_redirect( admin_url( 'edit.php?post_type=kov_lead' ) );
	exit;
}
add_action( 'admin_init', 'kov_guest_lock_admin' );

function kov_guest_deny_redirect(): void {
	if ( ! kov_is_lead_viewer() ) {
		return;
	}
	wp_safe_redirect( admin_url( 'edit.php?post_type=kov_lead' ) );
	exit;
}
add_action( 'admin_page_access_denied', 'kov_guest_deny_redirect' );

/**
 * Viewers never edit or delete a lead, including ones they authored.
 *
 * @param string[] $caps    Primitive caps required.
 * @param string   $cap     Capability being checked.
 * @param int      $user_id User ID.
 * @param mixed[]  $args    Extra arguments, usually the post ID.
 * @return string[]
 */
function kov_guest_block_lead_changes( array $caps, string $cap, int $user_id, array $args ): array {
	if ( ! in_array( $cap, array( 'edit_post', 'delete_post', 'publish_post' ), true ) ) {
		return $caps;
	}
	$post_id = isset( $args[0] ) ? (int) $args[0] : 0;
	if ( $post_id < 1 || 'kov_lead' !== get_post_type( $post_id ) ) {
		return $caps;
	}
	$user = get_userdata( $user_id );
	if ( $user instanceof WP_User && in_array( 'kov_guest', (array) $user->roles, true ) ) {
		return array( 'do_not_allow' );
	}
	return $caps;
}
add_filter( 'map_meta_cap', 'kov_guest_block_lead_changes', 10, 4 );

function kov_guest_hide_menus(): void {
	if ( ! kov_is_lead_viewer() ) {
		return;
	}

	global $menu;
	if ( ! is_array( $menu ) ) {
		return;
	}

	foreach ( $menu as $item ) {
		$slug = $item[2] ?? '';
		if ( $slug !== '' && 'kovalski' !== $slug ) {
			remove_menu_page( $slug );
		}
	}
}
add_action( 'admin_menu', 'kov_guest_hide_menus', 999 );

/**
 * @param array<string, string> $actions Bulk actions.
 * @return array<string, string>
 */
function kov_guest_bulk_actions( array $actions ): array {
	if ( kov_is_lead_viewer() ) {
		return array();
	}
	return $actions;
}
add_filter( 'bulk_actions-edit-kov_lead', 'kov_guest_bulk_actions' );

function kov_guest_admin_bar( WP_Admin_Bar $bar ): void {
	if ( ! kov_is_lead_viewer() ) {
		return;
	}
	foreach ( array( 'wp-logo', 'site-name', 'updates', 'comments', 'new-content', 'edit', 'user-info', 'command-palette' ) as $node ) {
		$bar->remove_node( $node );
	}
	$account = $bar->get_node( 'my-account' );
	if ( $account ) {
		$account->href = admin_url( 'edit.php?post_type=kov_lead' );
		$bar->add_node( (array) $account );
	}
}
add_action( 'admin_bar_menu', 'kov_guest_admin_bar', 999 );
