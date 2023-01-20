<?php
namespace UltimatePostKit\Modules\HazelGrid;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'hazel-grid';
	}

	public function get_widgets() {

		$widgets = [
			'Hazel_Grid',
		];
		
		return $widgets;
	}
}
