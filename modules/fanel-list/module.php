<?php
namespace UltimatePostKit\Modules\FanelList;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'fanel-list';
	}

	public function get_widgets() {

		$widgets = [
			'Fanel_List',
		];
		
		return $widgets;
	}
}
