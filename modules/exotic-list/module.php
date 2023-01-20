<?php
namespace UltimatePostKit\Modules\ExoticList;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'exotic-list';
	}

	public function get_widgets() {

		$widgets = [
			'Exotic_List',
		];
		
		return $widgets;
	}
}
