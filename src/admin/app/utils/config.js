/**
 * Bootstrap data printed by PHP (admin/class-react-dashboard.php) as
 * window.UltimatePostKitAdmin. Read once; the app never fetches it again.
 */
const config = window.UltimatePostKitAdmin || {};

export const sections = config.sections || {};

export function sectionFields( id ) {
	return ( sections[ id ] && sections[ id ].fields ) || [];
}

export default config;
