/**
 * Route table. The tab strip, the WordPress submenu sync and the legacy-hash
 * redirects are all derived from it.
 *
 * `legacy` is the hash the classic dashboard used for the same screen
 * (admin.php?page=ultimate_post_kit_options#ultimate_post_kit_active_modules); old
 * links and the WordPress submenu entries still use those.
 */
import { __ } from '@wordpress/i18n';
import config, { sections, sectionFields } from '../utils/config';
import {
	DashboardIcon,
	GridIcon,
	PuzzleIcon,
	SlidersIcon,
	ServerIcon,
	PackageIcon,
	ClockIcon,
	TagIcon,
	CodeIcon,
	KeyIcon,
	RocketIcon,
	LinkIcon,
	GaugeIcon,
} from '../components/ui/Icons';
import Dashboard from '../pages/Dashboard';
import SectionPage from '../pages/SectionPage';
import FeaturesPage from '../pages/FeaturesPage';
import ApiSettingsPage from '../pages/ApiSettingsPage';
import PerformancePage, { ASSET_FIELD } from '../pages/PerformancePage';
import SystemStatusPage from '../pages/SystemStatusPage';
import PanelPage from '../pages/PanelPage';
import GetProPage from '../pages/GetProPage';
import OtherPluginsPage from '../pages/OtherPluginsPage';
import LicensePage from '../pages/pro/LicensePage';
import WhiteLabelPage from '../pages/pro/WhiteLabelPage';
import CustomCodePage from '../pages/pro/CustomCodePage';
import RollbackPage from '../pages/pro/RollbackPage';
import ProFeaturePage from '../pages/ProFeaturePage';

/**
 * Native pages for add-on panels. When the add-on hands over the page data
 * (panel.data, from the `ultimate_post_kit/admin/react_pages` filter), the app
 * renders that page; otherwise the panel's PHP markup is shown.
 */
const NATIVE_PAGES = {
	ultimate_post_kit_license_settings: LicensePage,
	ultimate_post_kit_extra_options: WhiteLabelPage,
	ultimate_post_kit_custom_code: CustomCodePage,
	ultimate_post_kit_rollback_version: RollbackPage,
};

/**
 * Pro features that have their own tab once Ultimate Post Kit Pro is active. In
 * the free plugin the same tabs show, in the same places, with a page that
 * describes the feature and links to the upgrade (the Get Pro page's items).
 */
function proFeatureRoutes() {
	return [
		{
			id: 'ultimate_post_kit_extra_options',
			label: __( 'White Label', 'ultimate-post-kit' ),
			page: {
				icon: TagIcon,
				accent: 'woo',
				title: __( 'White Label', 'ultimate-post-kit' ),
				subtitle: __( 'Rebrand Ultimate Post Kit across the admin for your clients.', 'ultimate-post-kit' ),
				headline: __( 'Make Ultimate Post Kit your own brand', 'ultimate-post-kit' ),
				description: __( 'Rebrand the plugin for client sites: your name, your icon and your logo, with the license screen and the plugin itself hidden from clients.', 'ultimate-post-kit' ),
				points: [
					{ title: __( 'Your name and menu icon', 'ultimate-post-kit' ), sub: __( 'Replace "Ultimate Post Kit" and its icon throughout the admin.', 'ultimate-post-kit' ) },
					{ title: __( 'Your logo', 'ultimate-post-kit' ), sub: __( 'Show your own logo in the dashboard header.', 'ultimate-post-kit' ) },
					{ title: __( 'Hide the license menu', 'ultimate-post-kit' ), sub: __( 'Keep license details away from clients.', 'ultimate-post-kit' ) },
					{ title: __( 'Hide Ultimate Post Kit', 'ultimate-post-kit' ), sub: __( 'Remove the plugin from the admin, with an access link sent to your email.', 'ultimate-post-kit' ) },
				],
			},
		},
		{
			id: 'ultimate_post_kit_custom_code',
			label: __( 'Custom Code', 'ultimate-post-kit' ),
			page: {
				icon: CodeIcon,
				accent: 'violet',
				title: __( 'Custom CSS & JS', 'ultimate-post-kit' ),
				subtitle: __( 'Inject custom CSS/JS into your site and exclude pages from loading it.', 'ultimate-post-kit' ),
				headline: __( 'Add site-wide CSS and JavaScript', 'ultimate-post-kit' ),
				description: __( 'Write CSS and JavaScript once and load it across the site, in the header or the footer, and keep it off the pages where you don\'t want it.', 'ultimate-post-kit' ),
				points: [
					{ title: __( 'Header code', 'ultimate-post-kit' ), sub: __( 'CSS and JavaScript printed in the <head> of every page.', 'ultimate-post-kit' ) },
					{ title: __( 'Footer code', 'ultimate-post-kit' ), sub: __( 'CSS and JavaScript printed before the closing </body> tag.', 'ultimate-post-kit' ) },
					{ title: __( 'Code editor', 'ultimate-post-kit' ), sub: __( 'Syntax highlighting, line numbers and bracket matching.', 'ultimate-post-kit' ) },
					{ title: __( 'Page exclusions', 'ultimate-post-kit' ), sub: __( 'Choose pages and posts that should not load the custom code.', 'ultimate-post-kit' ) },
				],
			},
		},
		{
			id: 'ultimate_post_kit_rollback_version',
			label: __( 'Rollback Version', 'ultimate-post-kit' ),
			page: {
				icon: ClockIcon,
				accent: 'green',
				title: __( 'Rollback Version', 'ultimate-post-kit' ),
				subtitle: __( 'Restore a previous version and get priority help.', 'ultimate-post-kit' ),
				headline: __( 'Roll back an update in one click', 'ultimate-post-kit' ),
				description: __( 'If an update causes a problem on your site, go back to the version you had before while it is being fixed, and get priority help from the BdThemes team.', 'ultimate-post-kit' ),
				points: [
					{ title: __( 'One-click rollback', 'ultimate-post-kit' ), sub: __( 'Restore the previous version straight from the dashboard.', 'ultimate-post-kit' ) },
					{ title: __( 'Automatic backup', 'ultimate-post-kit' ), sub: __( 'The installed version is backed up before each update.', 'ultimate-post-kit' ) },
					{ title: __( 'Premium support', 'ultimate-post-kit' ), sub: __( 'Priority help from the BdThemes support team.', 'ultimate-post-kit' ) },
					{ title: __( 'Pro updates', 'ultimate-post-kit' ), sub: __( 'New Pro widgets and features as they are released.', 'ultimate-post-kit' ) },
				],
			},
		},
	];
}

/** Known PHP panels: route path, icon and where they sit in the strip. */
const PANEL_ROUTES = {
	ultimate_post_kit_rollback_version: { path: '/rollback', icon: ClockIcon, slot: 'more' },
	ultimate_post_kit_extra_options: { path: '/white-label', icon: TagIcon, slot: 'primary' },
	ultimate_post_kit_custom_code: { path: '/custom-code', icon: CodeIcon, slot: 'primary', label: __( 'Custom Code', 'ultimate-post-kit' ) },
	ultimate_post_kit_license_settings: { path: '/license', icon: KeyIcon, slot: 'primary' },
};

function sectionRoute( path, legacy, section, label, icon, title, subtitle ) {
	return {
		path,
		legacy,
		label,
		icon,
		slot: 'primary',
		end: true,
		render: () => (
			<SectionPage
				section={ section }
				title={ title || label }
				subtitle={ subtitle }
			/>
		),
	};
}

export function buildRoutes() {
	const list = [
		{
			path: '/',
			legacy: 'ultimate_post_kit_welcome',
			label: __( 'Dashboard', 'ultimate-post-kit' ),
			icon: DashboardIcon,
			slot: 'primary',
			end: true,
			render: () => <Dashboard />,
		},
		{
			path: '/widgets',
			legacy: 'ultimate_post_kit_active_modules',
			label: __( 'Widgets', 'ultimate-post-kit' ),
			icon: GridIcon,
			slot: 'primary',
			end: true,
			render: () => (
				<SectionPage
					section="ultimate_post_kit_active_modules"
					title={ __( 'Widgets', 'ultimate-post-kit' ) }
				/>
			),
		},
	];

	if ( sectionFields( 'ultimate_post_kit_elementor_extend' ).length ) {
		list.push(
			sectionRoute(
				'/extensions',
				'ultimate_post_kit_elementor_extend',
				'ultimate_post_kit_elementor_extend',
				__( 'Extensions', 'ultimate-post-kit' ),
				PuzzleIcon
			)
		);
	}

	list.push(
		{
			path: '/features',
			legacy: 'ultimate_post_kit_other_settings',
			label: __( 'Features', 'ultimate-post-kit' ),
			icon: SlidersIcon,
			slot: 'primary',
			end: true,
			render: () => <FeaturesPage />,
		},
	);

	if ( sectionFields( 'ultimate_post_kit_other_settings' ).some( ( f ) => f.name === ASSET_FIELD ) ) {
		list.push( {
			path: '/performance',
			legacy: 'ultimate_post_kit_performance',
			label: __( 'Performance', 'ultimate-post-kit' ),
			icon: GaugeIcon,
			// Right after Features in the tab strip, as in Element Pack.
			slot: 'primary',
			end: true,
			render: () => <PerformancePage />,
		} );
	}

	if ( ( sections.ultimate_post_kit_api_settings?.groups || [] ).length ) {
		list.push( {
			path: '/api-settings',
			legacy: 'ultimate_post_kit_api_settings',
			label: __( 'API Settings', 'ultimate-post-kit' ),
			icon: LinkIcon,
			slot: 'primary',
			end: true,
			render: () => <ApiSettingsPage />,
		} );
	}

	list.push(
		{
			path: '/system-status',
			legacy: 'ultimate_post_kit_analytics_system_req',
			label: __( 'System Status', 'ultimate-post-kit' ),
			icon: ServerIcon,
			slot: 'more',
			end: true,
			render: () => <SystemStatusPage />,
		}
	);

	list.push( {
		path: '/other-plugins',
		legacy: 'ultimate_post_kit_other_plugins',
		label: __( 'Other Plugins', 'ultimate-post-kit' ),
		icon: PackageIcon,
		slot: 'more',
		end: true,
		render: () => <OtherPluginsPage />,
	} );

	( config.panels || [] ).forEach( ( panel ) => {
		const known = PANEL_ROUTES[ panel.id ] || {
			path: `/${ panel.id.replace( /^ultimate_post_kit_/, '' ).replace( /_/g, '-' ) }`,
			icon: PuzzleIcon,
			slot: 'more',
		};
		list.push( {
			path: known.path,
			legacy: panel.id,
			// A tab label of our own where the add-on's panel title is longer.
			label: known.label || panel.title,
			icon: known.icon,
			slot: known.slot,
			end: true,
			render: () => {
				const Native = panel.data ? NATIVE_PAGES[ panel.id ] : null;
				return Native ? <Native panel={ panel } data={ panel.data } /> : <PanelPage panel={ panel } />;
			},
		} );
	} );

	if ( ! config.isPro ) {
		// The Pro features' tabs, where Pro puts them (an add-on panel that is
		// registered anyway keeps its own page).
		proFeatureRoutes().forEach( ( feature ) => {
			const known = PANEL_ROUTES[ feature.id ];
			if ( list.some( ( route ) => route.path === known.path ) ) {
				return;
			}
			list.push( {
				path: known.path,
				legacy: feature.id,
				label: feature.label,
				icon: known.icon,
				slot: known.slot,
				end: true,
				render: () => <ProFeaturePage { ...feature.page } />,
			} );
		} );

		list.push( {
			path: '/get-pro',
			legacy: 'ultimate_post_kit_get_pro',
			label: __( 'You May Also Need', 'ultimate-post-kit' ),
			icon: RocketIcon,
			// A tab of its own in the strip, after the Pro features' tabs.
			slot: 'primary',
			end: true,
			render: () => <GetProPage />,
		} );
	}

	return list;
}

export const routes = buildRoutes();

/** Route for a classic-dashboard hash such as `ultimate_post_kit_active_modules`. */
export function routeForLegacyHash( hash ) {
	const id = String( hash || '' ).replace( /^#/, '' );
	const match = routes.find( ( route ) => route.legacy === id );
	if ( match ) {
		return match.path;
	}
	// Without Pro, the classic dashboard's last tab (#ultimate_post_kit_license_settings) was "Get Pro".
	if ( id === 'ultimate_post_kit_license_settings' && ! config.isPro ) {
		return '/get-pro';
	}
	return null;
}
