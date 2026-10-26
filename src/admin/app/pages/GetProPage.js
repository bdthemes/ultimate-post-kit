/**
 * Get Pro (free plugin only): what Ultimate Post Kit Pro adds, and where to get it.
 */
import { __ } from '@wordpress/i18n';
import PageHeader from '../components/shell/PageHeader';
import IconTile from '../components/ui/IconTile';
import { CheckIcon, ZapIcon, GridIcon, LayoutIcon, TagIcon, CodeIcon, ClockIcon, ExternalIcon } from '../components/ui/Icons';
import config, { sectionFields } from '../utils/config';

export default function GetProPage() {
	const proWidgets = [
		...sectionFields( 'ultimate_post_kit_active_modules' ),
		...sectionFields( 'ultimate_post_kit_elementor_extend' ),
	].filter( ( f ) => f.widget_type === 'pro' );

	const perks = [
		{ icon: GridIcon, accent: 'blue', title: __( 'Every Pro widget', 'ultimate-post-kit' ), sub: __( 'Unlock all the grids, lists, carousels and sliders marked PRO in your widget list.', 'ultimate-post-kit' ) },
		{ icon: LayoutIcon, accent: 'rose', title: __( 'Template Builder', 'ultimate-post-kit' ), sub: __( 'Design single post, archive and author templates with Elementor.', 'ultimate-post-kit' ) },
		{ icon: ZapIcon, accent: 'amber', title: __( 'Dynamic Content & MultiColor', 'ultimate-post-kit' ), sub: __( 'Fill widgets with live WordPress data and color every item its own way.', 'ultimate-post-kit' ) },
		{ icon: CodeIcon, accent: 'violet', title: __( 'Custom CSS & JS', 'ultimate-post-kit' ), sub: __( 'Inject site-wide code with page exclusions.', 'ultimate-post-kit' ) },
		{ icon: TagIcon, accent: 'woo', title: __( 'White label', 'ultimate-post-kit' ), sub: __( 'Rebrand the plugin for client sites.', 'ultimate-post-kit' ) },
		{ icon: ClockIcon, accent: 'green', title: __( 'Rollback & premium support', 'ultimate-post-kit' ), sub: __( 'Restore a previous version and get priority help.', 'ultimate-post-kit' ) },
	];

	return (
		<div>
			<PageHeader
				title={ __( 'Ultimate Post Kit Pro', 'ultimate-post-kit' ) }
				subtitle={ __( 'Everything in the free version, plus the features below.', 'ultimate-post-kit' ) }
			/>

			<div className="rounded-card p-8 max-[640px]:p-5 relative overflow-hidden text-white [background:linear-gradient(135deg,#0a1912,#0f3a26)]">
				<div className="absolute inset-0 pointer-events-none [background:radial-gradient(circle_at_85%_0%,rgba(8,201,114,.35),transparent_55%)]" />
				<div className="relative max-w-[640px]">
					<div className="text-[12px] font-bold tracking-[.12em] uppercase text-[#86efac]">
						{ __( 'Upgrade', 'ultimate-post-kit' ) }
					</div>
					<div className="text-[26px] font-extrabold mt-2 tracking-[-.02em]">
						{ __( 'Create your sleek website with Ultimate Post Kit Pro', 'ultimate-post-kit' ) }
					</div>
					<p className="text-[14px] text-white/75 mt-3 leading-[1.6]">
						{ proWidgets.length
							? `${ proWidgets.length } ${ __( 'Pro widgets and extensions, plus the tools that news, magazine and blog sites need.', 'ultimate-post-kit' ) }`
							: __( 'Pro widgets and extensions, plus the tools that news, magazine and blog sites need.', 'ultimate-post-kit' ) }
					</p>
					<div className="flex flex-wrap gap-3 mt-6">
						<a href={ config.getProUrl } target="_blank" rel="noreferrer" className="inline-flex items-center gap-2 bg-gradient-brand text-white py-3 px-6 rounded-full text-[14px] font-extrabold shadow-glow hover:-translate-y-px transition-all">
							<ZapIcon size={ 15 } />
							{ __( 'Get Premium at 30% OFF', 'ultimate-post-kit' ) }
						</a>
						<a href={ config.pricingUrl } target="_blank" rel="noreferrer" className="inline-flex items-center gap-2 bg-white/[.12] border border-white/[.2] text-white py-3 px-6 rounded-full text-[14px] font-bold hover:bg-white/20 transition-colors">
							{ __( 'Compare Free vs Pro', 'ultimate-post-kit' ) }
							<ExternalIcon size={ 13 } />
						</a>
					</div>
				</div>
			</div>

			<div className="grid grid-cols-3 max-[1024px]:grid-cols-2 max-[640px]:grid-cols-1 gap-4 mt-4">
				{ perks.map( ( perk ) => (
					<div key={ perk.title } className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card p-5">
						<IconTile icon={ perk.icon } accent={ perk.accent } size="xl" />
						<div className="text-[15px] font-extrabold text-[#0f1115] dark:text-ink-text mt-3 flex items-center gap-2">
							<CheckIcon size={ 14 } className="text-[#1a7d3d] dark:text-[#4ade80]" />
							{ perk.title }
						</div>
						<p className="text-[13px] text-[#6c6c75] dark:text-ink-muted mt-1 leading-[1.55]">{ perk.sub }</p>
					</div>
				) ) }
			</div>
		</div>
	);
}
