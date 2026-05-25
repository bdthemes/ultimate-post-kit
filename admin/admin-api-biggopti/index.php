<?php

defined( 'ABSPATH' ) || exit;

if ( ! is_admin() ) {
	return;
}

require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/admin-api-biggopti-registry.php';
require_once __DIR__ . '/class-bdt-admin-api-biggopti.php';

\Bdt_Admin_Api_Biggopti::boot( __DIR__ );
