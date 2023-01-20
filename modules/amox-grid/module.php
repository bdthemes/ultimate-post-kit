<?php
namespace UltimatePostKit\Modules\AmoxGrid;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'amox-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Amox_Grid',
		];
		
		return $widgets;
	}
}
