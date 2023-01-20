<?php
namespace UltimatePostKit\Modules\HaroldList;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'harold-list';
	}

	public function get_widgets() {

		$widgets = [
			'Harold_List',
		];
		
		return $widgets;
	}
}
