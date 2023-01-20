<?php

namespace UltimatePostKit\Modules\StaticSocialCount;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'static-social-count';
	}

	public function get_widgets() {

		$widgets = [
			'Static_Social_Count',
		];

		return $widgets;
	}
}
