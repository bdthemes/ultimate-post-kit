<?php
namespace UltimatePostKit\Modules\NewsTicker;

use UltimatePostKit\Base\Ultimate_Post_Kit_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Ultimate_Post_Kit_Module_Base {

	public function get_name() {
		return 'news-ticker';
	}

	public function get_widgets() {

		$widgets = [
			'News_Ticker',
		];
		
		return $widgets;
	}
}
