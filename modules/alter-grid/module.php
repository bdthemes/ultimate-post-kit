<?php
namespace UltimatePostKit\Modules\AlterGrid;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'alter-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Alter_Grid',
		];
		
		return $widgets;
	}
}
