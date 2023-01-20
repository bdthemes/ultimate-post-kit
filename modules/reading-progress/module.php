<?php
namespace UltimatePostKit\Modules\ReadingProgress;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'reading-progress';
	}

	public function get_widgets() {

		$widgets = [
			'Reading_Progress',
		];
		
		return $widgets;
	}
}
