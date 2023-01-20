<?php
namespace UltimatePostKit\Modules\ScottList;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'scott-list';
	}

	public function get_widgets() {

		$widgets = [
			'Scott_List',
		];
		
		return $widgets;
	}
}
