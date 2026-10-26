/**
 * Widget categories from the `content_type` tokens of each field (the same
 * tokens the classic dashboard filtered by). A widget's category is its first
 * matching token; `new` is a badge, not a category.
 */
import { __ } from '@wordpress/i18n';

export const CATEGORIES = {
	grid: { label: __( 'Grid', 'ultimate-post-kit' ), accent: 'blue' },
	carousel: { label: __( 'Carousel', 'ultimate-post-kit' ), accent: 'teal' },
	list: { label: __( 'List', 'ultimate-post-kit' ), accent: 'violet' },
	slider: { label: __( 'Slider', 'ultimate-post-kit' ), accent: 'sky' },
	tabs: { label: __( 'Tabs', 'ultimate-post-kit' ), accent: 'amber' },
	timeline: { label: __( 'Timeline', 'ultimate-post-kit' ), accent: 'indigo' },
	loop: { label: __( 'Loop', 'ultimate-post-kit' ), accent: 'woo' },
	'template-builder': { label: __( 'Template Builder', 'ultimate-post-kit' ), accent: 'green' },
	others: { label: __( 'Others', 'ultimate-post-kit' ), accent: 'rose' },
};

export function tokens( mod ) {
	return String( mod.content_type || '' ).toLowerCase().split( /\s+/ ).filter( Boolean );
}

export function isNew( mod ) {
	return tokens( mod ).includes( 'new' );
}

export function categoryKeyForModule( mod ) {
	return tokens( mod ).find( ( token ) => CATEGORIES[ token ] ) || 'others';
}

export function categoryFor( mod ) {
	return CATEGORIES[ categoryKeyForModule( mod ) ];
}
