<?php

use UltimatePostKit\Ultimate_Post_Kit_Loader;
use Elementor\Plugin;

/**
 * You can easily add white label branding for for extended license or multi site license.
 * Don't try for regular license otherwise your license will be invalid.
 * return white label
 */

/**
 * You can easily add white label branding for for extended license or multi site license.
 * Don't try for regular license otherwise your license will be invalid.
 * return white label
 */
define( 'BDTUPK_PNAME', basename( dirname( BDTUPK__FILE__ ) ) );
define( 'BDTUPK_PBNAME', plugin_basename( BDTUPK__FILE__ ) );
define( 'BDTUPK_PATH', plugin_dir_path( BDTUPK__FILE__ ) );
define( 'BDTUPK_URL', plugins_url( '/', BDTUPK__FILE__ ) );
define( 'BDTUPK_ADMIN_PATH', BDTUPK_PATH . 'admin/' );
define( 'BDTUPK_ADMIN_URL', BDTUPK_URL . 'admin/' );
define( 'BDTUPK_ADMIN_ASSETS_PATH', BDTUPK_PATH . 'admin/assets/' );
define( 'BDTUPK_ADMIN_ASSETS_URL', BDTUPK_URL . 'admin/assets/' );
define( 'BDTUPK_MODULES_PATH', BDTUPK_PATH . 'modules/' );
define( 'BDTUPK_INC_PATH', BDTUPK_PATH . 'includes/' );
define( 'BDTUPK_ASSETS_URL', BDTUPK_URL . 'assets/' );
define( 'BDTUPK_ASSETS_PATH', BDTUPK_PATH . 'assets/' );
define( 'BDTUPK_MODULES_URL', BDTUPK_URL . 'modules/' );


if ( ! defined( 'BDTUPK' ) ) {
	define( 'BDTUPK', '' );
} //Add prefix for all widgets <span class="upk-widget-badge"></span>
if ( ! defined( 'BDTUPK_CP' ) ) {
	define( 'BDTUPK_CP', '<span class="upk-widget-badge"></span>' );
} //Add prefix for all widgets <span class="upk-widget-badge"></span>
if ( ! defined( 'BDTUPK_NC' ) ) {
	define( 'BDTUPK_NC', '<span class="upk-new-control"></span>' );
} //Add prefix for all widgets <span class="upk-widget-badge"></span>
if ( ! defined( 'BDTUPK_UC' ) ) {
	define( 'BDTUPK_UC', '<span class="upk-updated-control"></span>' );
} // if you have any custom style

if ( _is_upk_pro_activated() ) {
	if ( ! defined( 'BDTUPK_PC' ) ) {
		define( 'BDTUPK_PC', '' );
	} // pro control badge
	if ( ! defined( 'BDTUPK_SLUG' ) ) {
		define( 'BDTUPK_SLUG', 'ultimate-post-kit' );
	} // set your own alias
	if ( ! defined( 'BDTUPK_TITLE' ) ) {
		define( 'BDTUPK_TITLE', 'Ultimate Post Kit' );
	} // Set your own name for plugin
	define( 'BDTUPK_IS_PC', '' );
} else {
	if ( ! defined( 'BDTUPK_PC' ) ) {
		define( 'BDTUPK_PC', '<span class="upk-pro-control"></span>' );
	} // pro control badge
	if ( ! defined( 'BDTUPK_SLUG' ) ) {
		define( 'BDTUPK_SLUG', 'ultimate-post-kit' );
	} // set your own alias
	if ( ! defined( 'BDTUPK_TITLE' ) ) {
		define( 'BDTUPK_TITLE', 'Ultimate Post Kit' );
	} // Set your own name for plugin
	define( 'BDTUPK_IS_PC', 'upk-disabled-control' );
}

function ultimate_post_kit_is_edit() {
	return Plugin::$instance->editor->is_edit_mode();
}

function ultimate_post_kit_is_preview() {
	return Plugin::$instance->preview->is_preview_mode();
}

// add socials list meta in user profile
function ultimate_post_kit_user_contact_methods( $methods, $core = false ) {

	if ( $core ) {
		$methods['email'] = __( 'Email', 'ultimate-post-kit' );
		$methods['url']   = __( 'Website', 'ultimate-post-kit' );
	}

	$methods['facebook']  = __( 'Facebook', 'ultimate-post-kit' );
	$methods['twitter']   = __( 'Twitter', 'ultimate-post-kit' );
	$methods['linkedin']  = __( 'LinkedIn', 'ultimate-post-kit' );
	$methods['github']    = __( 'GitHub', 'ultimate-post-kit' );
	$methods['wordpress'] = __( 'WordPress', 'ultimate-post-kit' );
	$methods['dribbble']  = __( 'Dribbble', 'ultimate-post-kit' );

	return $methods;
}

add_filter( 'user_contactmethods', 'ultimate_post_kit_user_contact_methods' );

/**
 * Show any alert by this function
 *
 * @param mixed $message [description]
 * @param class prefix  $type    [description]
 * @param boolean $close [description]
 *
 * @return helper [description]
 */
function ultimate_post_kit_alert( $message, $type = 'warning', $close = true ) {
	?>
	<div class="upk-alert-<?php echo esc_attr( $type ); ?>" data-upk-alert>
		<?php if ( $close ) : ?>
			<a class="upk-alert-close" data-upk-close></a>
		<?php endif; ?>
		<?php echo wp_kses_post( $message ); ?>
	</div>
	<?php
}

function ultimate_post_kit_get_alert( $message, $type = 'warning', $close = true ) {

	$output = '<div class="upk-alert-' . $type . '" upk-alert>';
	if ( $close ) :
		$output .= '<a class="upk-alert-close" data-upk-close></a>';
	endif;
	$output .= wp_kses_post( $message );
	$output .= '</div>';

	return $output;
}

/**
 * all array css classes will output as proper space
 *
 * @param array $classes shortcode css class as array
 *
 * @return array string
 */

function ultimate_post_kit_get_post_types( $args = [] ) {

	$post_type_args = [ 
		'show_in_nav_menus' => true,
	];

	if ( ! empty( $args['post_type'] ) ) {
		$post_type_args['name'] = $args['post_type'];
	}

	$_post_types = get_post_types( $post_type_args, 'objects' );

	$post_types = [ '0' => esc_html__( 'Select Type', 'ultimate-post-kit' ) ];

	foreach ( $_post_types as $post_type => $object ) {
		$post_types[ $post_type ] = $object->label;
	}

	return $post_types;
}

function ultimate_post_kit_get_taxonomies() {
	$args       = array(
		'public' => true,
		//'_builtin' => false

	);
	$output     = [ '0' => esc_html__( 'Select Type', 'ultimate-post-kit' ) ];
	$tax_output = 'objects'; // or objects
	$taxonomies = get_taxonomies( $args, $tax_output );
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxonomy ) {
			$post_type_obj           = get_post_type_object( $taxonomy->object_type[0] );
			$output[ $taxonomy->name ] = ( $taxonomy->label ? $taxonomy->label : '' ) . ' (' . isset( $post_type_obj->label ) . ')';
		}
	}

	return $output;
}

function upk_get_category( $post_type ) {
	switch ( $post_type ) {
		case 'campaign':
			$taxonomy = 'campaign_category';
			break;
		case 'lightbox_library':
			$taxonomy = 'ngg_tag';
			break;
		case 'give_forms':
			$taxonomy = 'give_forms_category';
			break;
		case 'tribe_events':
			$taxonomy = 'tribe_events_cat';
			break;
		case 'product':
			$taxonomy = 'product_cat';
			break;
		default:
			$taxonomy = 'category';
			break;
	}

	$categories  = get_the_terms( get_the_ID(), $taxonomy );
	$_categories = [];
	if ( $categories ) {
		foreach ( $categories as $category ) {
			$link                         = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . $category->name . '</a>';
			$_categories[ $category->slug ] = $link;
		}
	}
	return implode( ' ', $_categories );
}


function ultimate_post_kit_allow_tags( $tag = null ) {
	$tag_allowed = wp_kses_allowed_html( 'post' );

	$tag_allowed['input']  = [ 
		'class'   => [],
		'id'      => [],
		'name'    => [],
		'value'   => [],
		'checked' => [],
		'type'    => [],
	];
	$tag_allowed['select'] = [ 
		'class'    => [],
		'id'       => [],
		'name'     => [],
		'value'    => [],
		'multiple' => [],
		'type'     => [],
	];
	$tag_allowed['option'] = [ 
		'value'    => [],
		'selected' => [],
	];

	$tag_allowed['title'] = [ 
		'a'      => [ 
			'href'  => [],
			'title' => [],
			'class' => [],
		],
		'br'     => [],
		'em'     => [],
		'strong' => [],
		'hr'     => [],
	];

	$tag_allowed['text'] = [ 
		'a'      => [ 
			'href'  => [],
			'title' => [],
			'class' => [],
		],
		'br'     => [],
		'em'     => [],
		'strong' => [],
		'hr'     => [],
		'i'      => [ 
			'class' => [],
		],
		'span'   => [ 
			'class' => [],
		],
	];

	$tag_allowed['svg'] = [ 
		'svg'     => [ 
			'version'     => [],
			'xmlns'       => [],
			'viewbox'     => [],
			'xml:space'   => [],
			'xmlns:xlink' => [],
			'x'           => [],
			'y'           => [],
			'style'       => [],
		],
		'g'       => [],
		'path'    => [ 
			'class' => [],
			'd'     => [],
		],
		'ellipse' => [ 
			'class' => [],
			'cx'    => [],
			'cy'    => [],
			'rx'    => [],
			'ry'    => [],
		],
		'circle'  => [ 
			'class' => [],
			'cx'    => [],
			'cy'    => [],
			'r'     => [],
		],
		'rect'    => [ 
			'x'         => [],
			'y'         => [],
			'transform' => [],
			'height'    => [],
			'width'     => [],
			'class'     => [],
		],
		'line'    => [ 
			'class' => [],
			'x1'    => [],
			'x2'    => [],
			'y1'    => [],
			'y2'    => [],
		],
		'style'   => [],


	];

	if ( $tag == null ) {
		return $tag_allowed;
	} elseif ( is_array( $tag ) ) {
		$new_tag_allow = [];

		foreach ( $tag as $_tag ) {
			$new_tag_allow[ $_tag ] = $tag_allowed[ $_tag ];
		}

		return $new_tag_allow;
	} else {
		return isset( $tag_allowed[ $tag ] ) ? $tag_allowed[ $tag ] : [];
	}
}

/**
 * HexColor
 */
function strToHex( $string, $steps = -10 ) {

	if ( empty( $string ) ) {
		return false;
	}

	$hex_output = sprintf( '%s', substr( md5( $string ), 0, 6 ) );

	// Steps should be between -255 and 255. Negative = darker, positive = lighter
	$steps = max( -255, min( 255, $steps ) );

	// Split into three parts: R, G and B
	$color_parts = str_split( $hex_output, 2 );
	$output      = '#';

	foreach ( $color_parts as $color ) {
		$color  = hexdec( $color );                                   // Convert to decimal
		$color  = max( 0, min( 255, $color + $steps ) );              // Adjust color
		$output .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code
	}

	return strToUpper( $output );
}

function ultimate_post_kit_post_pagination( $wp_query, $widget_id = '' ) {

	/** Stop execution if there's only 1 page */
	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}

	if ( is_front_page() ) {
		$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
	} else {
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	}
	$max = intval( $wp_query->max_num_pages );

	/** Add current page to the array */
	if ( $paged >= 1 ) {
		$links[] = $paged;
	}

	/** Add the pages around the current page to the array */
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	printf( '<ul class="upk-pagination" data-widget-id="%s" >' . "\n", $widget_id );

	/** Previous Post Link */
	if ( get_previous_posts_link() ) {
		printf( '<li class="upk-pagination-previous">%s</li>' . "\n", get_previous_posts_link( '<span data-upk-pagination-previous><i class="upk-icon-arrow-left-5" aria-hidden="true"></i></span>' ) );
	}

	/** Link to first page, plus ellipses if necessary */
	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="current"' : '';

		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) ) {
			echo '<li class="upk-pagination-dot-dot"><span>...</span></li>';
		}
	}

	/** Link to current page, plus 2 pages in either direction if necessary */
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="upk-active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	/** Link to last page, plus ellipses if necessary */
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) ) {
			echo '<li class="upk-pagination-dot-dot"><span>...</span></li>' . "\n";
		}

		$class = $paged == $max ? ' class="upk-active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

	/** Next Post Link */
	if ( get_next_posts_link( null, $paged ) ) {
		printf( '<li class="upk-pagination-next>%s</li>' . "\n", get_next_posts_link( '<span data-upk-pagination-next><i class="upk-icon-arrow-right-5" aria-hidden="true"></i></span>' ) );
	}

	echo '</ul>' . "\n";
}

function ultimate_post_kit_template_edit_link( $template_id ) {
	if ( Ultimate_Post_Kit_Loader::elementor()->editor->is_edit_mode() ) {

		$final_url = add_query_arg( [ 'elementor' => '' ], get_permalink( $template_id ) );

		$output = sprintf( '<a class="upk-elementor-template-edit-link" href="%s" title="%s" target="_blank"><i class="eicon-edit"></i></a>', esc_url( $final_url ), esc_html__( 'Edit Template', 'ultimate-post-kit' ) );

		return $output;
	}
}

function ultimate_post_kit_time_diff( $from, $to = '' ) {
	$diff    = human_time_diff( $from, $to );
	$replace = array(
		' hour'    => 'h',
		' hours'   => 'h',
		' day'     => 'd',
		' days'    => 'd',
		' minute'  => 'm',
		' minutes' => 'm',
		' second'  => 's',
		' seconds' => 's',
	);

	return strtr( $diff, $replace );
}

function ultimate_post_kit_post_time_diff( $format = '' ) {
	$displayAgo = esc_html__( 'ago', 'ultimate-post-kit' );

	if ( $format == 'short' ) {
		$output = ultimate_post_kit_time_diff( strtotime( get_the_date() ), current_time( 'timestamp' ) );
	} else {
		$output = human_time_diff( strtotime( get_the_date() ), current_time( 'timestamp' ) );
	}

	$output = $output . ' ' . $displayAgo;

	return $output;
}

function ultimate_post_kit_iso_time( $time ) {
	$current_offset  = (float) get_option( 'gmt_offset' );
	$timezone_string = get_option( 'timezone_string' );

	// Create a UTC+- zone if no timezone string exists.
	//if ( empty( $timezone_string ) ) {
	if ( 0 === $current_offset ) {
		$timezone_string = '+00:00';
	} elseif ( $current_offset < 0 ) {
		$timezone_string = $current_offset . ':00';
	} else {
		$timezone_string = '+0' . $current_offset . ':00';
	}
	//}

	$sub_time   = [];
	$sub_time   = explode( " ", $time );
	$final_time = $sub_time[0] . 'T' . $sub_time[1] . ':00' . $timezone_string;

	return $final_time;
}

/**
 * @return array
 */
function ultimate_post_kit_get_menu() {

	$menus = wp_get_nav_menus();
	$items = [ 0 => esc_html__( 'Select Menu', 'ultimate-post-kit' ) ];
	foreach ( $menus as $menu ) {
		$items[ $menu->slug ] = $menu->name;
	}

	return $items;
}

/**
 * default get_option() default value check
 *
 * @param string $option settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function ultimate_post_kit_option( $option, $section, $default = '' ) {

	$options = get_option( $section );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default;
}

/**
 * @return array of elementor template
 */
function ultimate_post_kit_et_options() {

	$templates = Ultimate_Post_Kit_Loader::elementor()->templates_manager->get_source( 'local' )->get_items();
	$types     = [];

	if ( empty( $templates ) ) {
		$template_options = [ '0' => __( 'Template Not Found!', 'ultimate-post-kit' ) ];
	} else {
		$template_options = [ '0' => __( 'Select Template', 'ultimate-post-kit' ) ];

		foreach ( $templates as $template ) {
			$template_options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
			$types[ $template['template_id'] ]            = $template['type'];
		}
	}

	return $template_options;
}

/**
 * @return array of wp default sidebars
 */
function ultimate_post_kit_sidebar_options() {

	global $wp_registered_sidebars;
	$sidebar_options = [];

	if ( ! $wp_registered_sidebars ) {
		$sidebar_options[0] = esc_html__( 'No sidebars were found', 'ultimate-post-kit' );
	} else {
		$sidebar_options[0] = esc_html__( 'Select Sidebar', 'ultimate-post-kit' );

		foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
			$sidebar_options[ $sidebar_id ] = $sidebar['name'];
		}
	}

	return $sidebar_options;
}

/**
 * @param string category name
 *
 * @return array of category
 */
function ultimate_post_kit_get_category( $taxonomy = 'category' ) {

	$post_options = [];

	$post_categories = get_terms(
		[ 
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		]
	);

	if ( is_wp_error( $post_categories ) ) {
		return $post_options;
	}

	if ( false !== $post_categories and is_array( $post_categories ) ) {
		foreach ( $post_categories as $category ) {
			$post_options[ $category->slug ] = $category->name;
		}
	}

	return $post_options;
}

/**
 * @param array all ajax posted array there
 *
 * @return array return all setting as array
 */
function ultimate_post_kit_ajax_settings( $settings ) {

	$required_settings = [ 
		'show_date'      => true,
		'show_comment'   => true,
		'show_link'      => true,
		'show_meta'      => true,
		'show_title'     => true,
		'show_excerpt'   => true,
		'show_lightbox'  => true,
		'show_thumbnail' => true,
		'show_category'  => false,
		'show_tags'      => false,
	];

	foreach ( $settings as $key => $value ) {
		if ( in_array( $key, $required_settings ) ) {
			$required_settings[ $key ] = $value;
		}
	}

	return $required_settings;
}

// BDT Blend Type
function ultimate_post_kit_blend_options() {
	$blend_options = [ 
		'multiply'    => esc_html__( 'Multiply', 'ultimate-post-kit' ),
		'screen'      => esc_html__( 'Screen', 'ultimate-post-kit' ),
		'overlay'     => esc_html__( 'Overlay', 'ultimate-post-kit' ),
		'darken'      => esc_html__( 'Darken', 'ultimate-post-kit' ),
		'lighten'     => esc_html__( 'Lighten', 'ultimate-post-kit' ),
		'color-dodge' => esc_html__( 'Color-Dodge', 'ultimate-post-kit' ),
		'color-burn'  => esc_html__( 'Color-Burn', 'ultimate-post-kit' ),
		'hard-light'  => esc_html__( 'Hard-Light', 'ultimate-post-kit' ),
		'soft-light'  => esc_html__( 'Soft-Light', 'ultimate-post-kit' ),
		'difference'  => esc_html__( 'Difference', 'ultimate-post-kit' ),
		'exclusion'   => esc_html__( 'Exclusion', 'ultimate-post-kit' ),
		'hue'         => esc_html__( 'Hue', 'ultimate-post-kit' ),
		'saturation'  => esc_html__( 'Saturation', 'ultimate-post-kit' ),
		'color'       => esc_html__( 'Color', 'ultimate-post-kit' ),
		'luminosity'  => esc_html__( 'Luminosity', 'ultimate-post-kit' ),
	];

	return $blend_options;
}

// BDT Position
function ultimate_post_kit_position() {
	$position_options = [ 
		''              => esc_html__( 'Default', 'ultimate-post-kit' ),
		'top-left'      => esc_html__( 'Top Left', 'ultimate-post-kit' ),
		'top-center'    => esc_html__( 'Top Center', 'ultimate-post-kit' ),
		'top-right'     => esc_html__( 'Top Right', 'ultimate-post-kit' ),
		'center'        => esc_html__( 'Center', 'ultimate-post-kit' ),
		'center-left'   => esc_html__( 'Center Left', 'ultimate-post-kit' ),
		'center-right'  => esc_html__( 'Center Right', 'ultimate-post-kit' ),
		'bottom-left'   => esc_html__( 'Bottom Left', 'ultimate-post-kit' ),
		'bottom-center' => esc_html__( 'Bottom Center', 'ultimate-post-kit' ),
		'bottom-right'  => esc_html__( 'Bottom Right', 'ultimate-post-kit' ),
	];

	return $position_options;
}

// BDT Thumbnavs Position
function ultimate_post_kit_thumbnavs_position() {
	$position_options = [ 
		'top-left'      => esc_html__( 'Top Left', 'ultimate-post-kit' ),
		'top-center'    => esc_html__( 'Top Center', 'ultimate-post-kit' ),
		'top-right'     => esc_html__( 'Top Right', 'ultimate-post-kit' ),
		'center-left'   => esc_html__( 'Center Left', 'ultimate-post-kit' ),
		'center-right'  => esc_html__( 'Center Right', 'ultimate-post-kit' ),
		'bottom-left'   => esc_html__( 'Bottom Left', 'ultimate-post-kit' ),
		'bottom-center' => esc_html__( 'Bottom Center', 'ultimate-post-kit' ),
		'bottom-right'  => esc_html__( 'Bottom Right', 'ultimate-post-kit' ),
	];

	return $position_options;
}

function ultimate_post_kit_navigation_position() {
	$position_options = [ 
		'top-left'      => esc_html__( 'Top Left', 'ultimate-post-kit' ),
		'top-center'    => esc_html__( 'Top Center', 'ultimate-post-kit' ),
		'top-right'     => esc_html__( 'Top Right', 'ultimate-post-kit' ),
		'center'        => esc_html__( 'Center', 'ultimate-post-kit' ),
		'center-left'   => esc_html__( 'Center Left', 'ultimate-post-kit' ),
		'center-right'  => esc_html__( 'Center Right', 'ultimate-post-kit' ),
		'bottom-left'   => esc_html__( 'Bottom Left', 'ultimate-post-kit' ),
		'bottom-center' => esc_html__( 'Bottom Center', 'ultimate-post-kit' ),
		'bottom-right'  => esc_html__( 'Bottom Right', 'ultimate-post-kit' ),
	];

	return $position_options;
}


function ultimate_post_kit_pagination_position() {
	$position_options = [ 
		'top-left'      => esc_html__( 'Top Left', 'ultimate-post-kit' ),
		'top-center'    => esc_html__( 'Top Center', 'ultimate-post-kit' ),
		'top-right'     => esc_html__( 'Top Right', 'ultimate-post-kit' ),
		'center-left'   => esc_html__( 'Center Left', 'ultimate-post-kit' ),
		'center-right'  => esc_html__( 'Center Right', 'ultimate-post-kit' ),
		'bottom-left'   => esc_html__( 'Bottom Left', 'ultimate-post-kit' ),
		'bottom-center' => esc_html__( 'Bottom Center', 'ultimate-post-kit' ),
		'bottom-right'  => esc_html__( 'Bottom Right', 'ultimate-post-kit' ),
	];

	return $position_options;
}

// Title Tags
function ultimate_post_kit_title_tags() {
	$title_tags = [ 
		'h1'   => 'H1',
		'h2'   => 'H2',
		'h3'   => 'H3',
		'h4'   => 'H4',
		'h5'   => 'H5',
		'h6'   => 'H6',
		'div'  => 'div',
		'span' => 'span',
		'p'    => 'p',
	];

	return $title_tags;
}
function ultimate_post_kit_hide_on_class( $selectors ) {
	$element_hide_on = '';
	if ( ! empty( $selectors ) ) {
		foreach ( $selectors as $element ) {

			if ( $element == 'desktop' ) {
				$element_hide_on .= ' upk-desktop';
			}
			if ( $element == 'tablet' ) {
				$element_hide_on .= ' upk-tablet';
			}
			if ( $element == 'mobile' ) {
				$element_hide_on .= ' upk-mobile';
			}
		}
	}
	return $element_hide_on;
}
function ultimate_post_kit_mask_shapes() {
	$path       = BDTUPK_ASSETS_URL . 'images/mask/';
	$shape_name = 'shape';
	$extension  = '.svg';
	$list       = [ 0 => esc_html__( 'Select Mask', 'ultimate-post-kit' ) ];

	for ( $i = 1; $i <= 20; $i++ ) {
		$list[ $path . $shape_name . '-' . $i . $extension ] = ucwords( $shape_name . ' ' . $i );
	}

	return $list;
}

/**
 * String to ID maker for any title to relavent id
 * @param  [type] string any title or string
 * @return [type]         [description]
 */
function ultimate_post_kit_string_id( $string ) {
	//Lower case everything
	$string = strtolower( $string );
	//Make alphanumeric (removes all other characters)
	$string = preg_replace( "/[^a-z0-9_\s-]/", "", $string );
	//Clean up multiple dashes or whitespaces
	$string = preg_replace( "/[\s-]+/", " ", $string );
	//Convert whitespaces and underscore to dash
	$string = preg_replace( "/[\s_]/", "-", $string );

	//finally return here
	return $string;
}


function ultimate_post_kit_dashboard_link( $suffix = '#welcome' ) {
	return add_query_arg( [ 'page' => 'ultimate_post_kit_options' . $suffix ], admin_url( 'admin.php' ) );
}

/**
 * @param int $limit default limit is 25 word
 * @param bool $strip_shortcode if you want to strip shortcode from excert text
 * @param string $trail trail string default is ...
 *
 * @return string return custom limited excerpt text
 */
function ultimate_post_kit_custom_excerpt( $limit = 25, $strip_shortcode = false, $trail = '' ) {

	$output = get_the_content();

	if ( $limit ) {
		$output = wp_trim_words( $output, $limit, $trail );
	}

	if ( $strip_shortcode ) {
		$output = strip_shortcodes( $output );
	}

	return wpautop( $output );
}

function get_user_role( $id ) {

	$user = new WP_User( $id );

	return array_shift( $user->roles );
}


/**
 * @param string $content return posts content
 * @param int $avg_reading_speed average word reading speed per minute
 *
 * @return string return average reading time of  specifiic posts.
 */

if ( _is_upk_pro_activated() ) {
	function ultimate_post_kit_reading_time( $content, $avg_reading_speed ) {
		$total_word      = str_word_count( strip_tags( $content ) );
		$reading_minute  = floor( $total_word / $avg_reading_speed );
		$reading_seconds = floor( $total_word % $avg_reading_speed / ( $avg_reading_speed / 60 ) );
		if ( $total_word >= $avg_reading_speed ) {
			return $reading_minute . ' min ' . $reading_seconds . ' sec read';
		} else {
			return $reading_seconds . ' sec read';
		}
	}
}


/**
 * License Validation
 */
if ( ! function_exists( 'upk_license_validation' ) ) {
	function upk_license_validation() {

		if ( function_exists( '_is_upk_pro_activated' ) && false === _is_upk_pro_activated() ) {
			return false;
		}

		$license_key = trim( get_option( 'ultimate_post_kit_license_key' ) );

		if ( isset( $license_key ) && ! empty( $license_key ) ) {
			return true;
		} else {
			return false;
		}
		return false;
	}
}
