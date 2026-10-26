/**
 * Dashboard overview in the Element Pack layout: welcome header, stat row,
 * health strip, quick actions, most-used widgets and release timeline on the
 * left; promo, system status and resources on the right. Every value comes
 * from the bootstrap data PHP prints with the page.
 */
import { __, sprintf } from '@wordpress/i18n';
import DashboardStatCard from '../components/dashboard/DashboardStatCard';
import HealthStrip from '../components/dashboard/HealthStrip';
import QuickActions from '../components/dashboard/QuickActions';
import SystemStatusCard from '../components/dashboard/SystemStatusCard';
import ResourcesCard from '../components/dashboard/ResourcesCard';
import ActivityCard from '../components/dashboard/ActivityCard';
import PromoCard from '../components/dashboard/PromoCard';
import MostUsedCard from '../components/dashboard/MostUsedCard';
import SuggestionsCard from '../components/dashboard/SuggestionsCard';
import { GridIcon, PuzzleIcon, ZapIcon, SlidersIcon, GaugeIcon, ServerIcon, PackageIcon, RocketIcon, PlusIcon, TagIcon, CodeIcon, KeyIcon, LinkIcon } from '../components/ui/Icons';
import config, { sectionFields } from '../utils/config';
import { ASSET_FIELD } from './PerformancePage';

function percent( part, total ) {
	return total ? Math.round( ( part / total ) * 100 ) : 0;
}

function hasPanel( id ) {
	return ( config.panels || [] ).some( ( panel ) => panel.id === id );
}

export default function Dashboard() {
	const widgets = sectionFields( 'ultimate_post_kit_active_modules' );
	const extensions = sectionFields( 'ultimate_post_kit_elementor_extend' );
	// Asset Optimization has its own Performance page and is not one of the
	// features the Features page lists, so it is not counted here either.
	const features = sectionFields( 'ultimate_post_kit_other_settings' ).filter( ( f ) => f.name !== ASSET_FIELD );

	// Same rule as the Widgets page: locked Pro widgets cannot be switched,
	// so they are not counted.
	const available = widgets.filter( ( w ) => ! w.is_locked && w.plugin_active !== false );
	const extensionsOn = extensions.filter( ( w ) => w.enabled && ! w.is_locked ).length;
	const enabled = available.filter( ( w ) => w.enabled ).length;
	const used = widgets.filter( ( w ) => w.usage_count > 0 );
	const featuresOn = features.filter( ( f ) => f.enabled ).length;
	const tracking = widgets.some( ( w ) => w.has_usage );
	const checks = Array.isArray( config.system ) ? config.system : [];
	const checksOk = checks.filter( ( c ) => c.pass ).length;

	// The comment sits right before __() so it survives minifying and reaches the POT.
	const ofTotal = /* translators: 1: count, 2: total */ __( '%1$d of %2$d', 'ultimate-post-kit' );

	const welcome = config.userName
		? sprintf(
				/* translators: %s: user display name */
				__( 'Welcome back, %s', 'ultimate-post-kit' ),
				config.userName
		  )
		: __( 'Welcome back', 'ultimate-post-kit' );

	const metrics = [
		{
			label: __( 'Widgets enabled', 'ultimate-post-kit' ),
			value: `${ percent( enabled, available.length ) }%`,
			percent: percent( enabled, available.length ),
			sub: sprintf( ofTotal, enabled, available.length ),
			icon: ZapIcon,
			accent: 'rose',
			barColor: '#60df54',
		},
		tracking && {
			label: __( 'Widgets in use', 'ultimate-post-kit' ),
			value: `${ percent( used.length, widgets.length ) }%`,
			percent: percent( used.length, widgets.length ),
			sub: sprintf( ofTotal, used.length, widgets.length ),
			icon: GaugeIcon,
			accent: 'green',
			barColor: '#22a550',
		},
		{
			label: __( 'Features active', 'ultimate-post-kit' ),
			value: `${ percent( featuresOn, features.length ) }%`,
			percent: percent( featuresOn, features.length ),
			sub: sprintf( ofTotal, featuresOn, features.length ),
			icon: SlidersIcon,
			accent: 'amber',
			barColor: '#f59e0b',
		},
		{
			label: __( 'System checks passed', 'ultimate-post-kit' ),
			value: `${ percent( checksOk, checks.length ) }%`,
			percent: percent( checksOk, checks.length ),
			sub: sprintf( ofTotal, checksOk, checks.length ),
			icon: ServerIcon,
			accent: 'blue',
			barColor: '#408efa',
		},
	].filter( Boolean );

	const actions = [
		{ to: '/widgets', icon: GridIcon, accent: 'rose', label: __( 'Core widgets', 'ultimate-post-kit' ), sub: sprintf( /* translators: %d: widget count */ __( 'Toggle %d widgets', 'ultimate-post-kit' ), widgets.length ) },
		extensions.length > 0 && { to: '/extensions', icon: PuzzleIcon, accent: 'woo', label: __( 'Extensions', 'ultimate-post-kit' ), sub: __( 'Animations & more', 'ultimate-post-kit' ) },
		{ to: '/features', icon: SlidersIcon, accent: 'amber', label: __( 'Features', 'ultimate-post-kit' ), sub: __( 'Live copy, duplicator…', 'ultimate-post-kit' ) },
		{ to: '/api-settings', icon: LinkIcon, accent: 'indigo', label: __( 'API settings', 'ultimate-post-kit' ), sub: __( 'Mailchimp, Instagram', 'ultimate-post-kit' ) },
		hasPanel( 'ultimate_post_kit_custom_code' ) && { to: '/custom-code', icon: CodeIcon, accent: 'violet', label: __( 'Custom code', 'ultimate-post-kit' ), sub: __( 'Inject CSS & JS', 'ultimate-post-kit' ) },
		hasPanel( 'ultimate_post_kit_extra_options' ) && { to: '/white-label', icon: TagIcon, accent: 'teal', label: __( 'White label', 'ultimate-post-kit' ), sub: __( 'Rebrand the plugin', 'ultimate-post-kit' ) },
		hasPanel( 'ultimate_post_kit_license_settings' ) && { to: '/license', icon: KeyIcon, accent: 'green', label: __( 'License', 'ultimate-post-kit' ), sub: __( 'Manage activation', 'ultimate-post-kit' ) },
		{ to: '/system-status', icon: ServerIcon, accent: 'blue', label: __( 'System status', 'ultimate-post-kit' ), sub: __( 'Server checks', 'ultimate-post-kit' ) },
		{ to: '/other-plugins', icon: PackageIcon, accent: 'sky', label: __( 'Other plugins', 'ultimate-post-kit' ), sub: __( 'From BdThemes', 'ultimate-post-kit' ) },
		! config.isPro && { to: '/get-pro', icon: RocketIcon, accent: 'green', label: __( 'Get Pro', 'ultimate-post-kit' ), sub: __( 'Unlock every widget', 'ultimate-post-kit' ) },
	]
		.filter( Boolean )
		.slice( 0, 6 );

	const mostUsed = [ ...used ].sort( ( a, b ) => b.usage_count - a.usage_count ).slice( 0, 5 );

	return (
		<div>
			<div className="flex items-end justify-between gap-4 flex-wrap mb-[22px]">
				<div>
					<h1 className="text-[31px] leading-[1.15] font-extrabold tracking-[-.025em] text-[#0f1115] dark:text-ink-text">
						{ welcome }
					</h1>
					<p className="text-[14.5px] text-[#6c6c75] dark:text-ink-muted mt-[6px]">
						{ __( 'Here’s what’s happening with your post widgets.', 'ultimate-post-kit' ) }
					</p>
				</div>
				<div className="flex items-center gap-[10px]">
					<a
						href={ config.setupWizardUrl }
						className="inline-flex items-center gap-[7px] rounded-full py-[10px] px-[15px] text-[13px] font-bold bg-white dark:bg-ink-card border border-[#e2e2e6] dark:border-[#2a313c] text-[#3f3f46] dark:text-[#cdd0d6] transition-all hover:border-brand hover:text-brand"
					>
						<RocketIcon size={ 15 } />
						{ __( 'Setup wizard', 'ultimate-post-kit' ) }
					</a>
					<a
						href={ config.newPageUrl }
						className="inline-flex items-center gap-[7px] rounded-full py-[10px] px-[15px] text-[13px] font-extrabold bg-gradient-brand text-white shadow-glow transition-all hover:-translate-y-px hover:shadow-glow-lg"
					>
						<PlusIcon size={ 15 } />
						{ __( 'Create new page', 'ultimate-post-kit' ) }
					</a>
				</div>
			</div>

			<div className="grid grid-cols-4 max-[1024px]:grid-cols-2 max-[640px]:grid-cols-1 gap-4">
				<DashboardStatCard
					icon={ GridIcon }
					accent="rose"
					value={ widgets.length }
					label={ __( 'Total widgets', 'ultimate-post-kit' ) }
					pill={ tracking ? sprintf( /* translators: %d: used widget count */ __( '%d used', 'ultimate-post-kit' ), used.length ) : null }
					pillTone="green"
				/>
				<DashboardStatCard
					icon={ ZapIcon }
					accent="green"
					value={ enabled }
					label={ __( 'Widgets enabled', 'ultimate-post-kit' ) }
					pill={ __( 'live', 'ultimate-post-kit' ) }
					pillTone="blue"
				/>
				<DashboardStatCard
					icon={ PuzzleIcon }
					accent="woo"
					value={ extensionsOn }
					label={ __( 'Extensions active', 'ultimate-post-kit' ) }
					pill={ sprintf( /* translators: %d: total */ __( 'of %d', 'ultimate-post-kit' ), extensions.length ) }
				/>
				<DashboardStatCard
					icon={ SlidersIcon }
					accent="amber"
					value={ featuresOn }
					label={ __( 'Features active', 'ultimate-post-kit' ) }
					pill={ sprintf( /* translators: %d: total */ __( 'of %d', 'ultimate-post-kit' ), features.length ) }
				/>
			</div>

			<HealthStrip metrics={ metrics } />

			<div className="grid grid-cols-[1.6fr_1fr] max-[1024px]:grid-cols-1 gap-4 mt-4 items-start">
				<div className="flex flex-col gap-4">
					<QuickActions actions={ actions } />
					<MostUsedCard widgets={ mostUsed } />
					<ActivityCard />
				</div>
				<div className="flex flex-col gap-4">
					<SuggestionsCard />
					<PromoCard />
					<SystemStatusCard />
					<ResourcesCard />
				</div>
			</div>
		</div>
	);
}
