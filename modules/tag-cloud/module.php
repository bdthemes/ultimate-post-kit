<?php
namespace UltimatePostKit\Modules\TagCloud;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'tag-cloud';
	}

	public function get_widgets() {

		$widgets = [
			'Tag_Cloud',
		];
		
		return $widgets;
	}
}
