<?php
namespace UltimatePostKit\Modules\EliteGrid;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'elite-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Elite_Grid',
		];
		
		return $widgets;
	}
}
