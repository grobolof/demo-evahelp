<?php
/**
 * Kovalski theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KOV_VERSION', '1.1.0' );

require get_template_directory() . '/inc/data.php';
require get_template_directory() . '/inc/icons.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/guest.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/leads.php';
require get_template_directory() . '/inc/admin.php';
require get_template_directory() . '/inc/seed.php';
require get_template_directory() . '/inc/setup.php';
