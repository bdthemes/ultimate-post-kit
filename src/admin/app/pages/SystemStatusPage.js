/**
 * System Status, laid out like Element Pack's: an attention notice, widget
 * counts, the server requirement checks and the environment.
 */
import { __, sprintf } from '@wordpress/i18n';
import PageHeader from '../components/shell/PageHeader';
import StatusCheckRow from '../components/StatusCheckRow';
import Card, { SectionCard } from '../components/ui/Card';
import Alert from '../components/ui/Alert';
import { GaugeIcon, CodeIcon } from '../components/ui/Icons';
import config, { sectionFields } from '../utils/config';

function counts( id ) {
	const fields = sectionFields( id ).filter( ( f ) => ! f.is_locked && f.plugin_active !== false );
	return { active: fields.filter( ( f ) => f.enabled ).length, total: fields.length };
}

function WidgetStatCard( { title, active, total } ) {
	return (
		<Card className="p-5">
			<div className="text-[12.5px] font-bold text-[#6c6c75] dark:text-ink-muted">{ title }</div>
			<div className="flex items-baseline gap-2 mt-2">
				<span className="text-[34px] font-black tracking-[-.03em] text-[#0f1115] dark:text-ink-text">{ active }</span>
				<span className="text-[12.5px] font-semibold text-[#6d6d74] dark:text-ink-muted">
					{ sprintf(
						/* translators: %d: total count */
						__( 'of %d active', 'ultimate-post-kit' ),
						total
					) }
				</span>
			</div>
		</Card>
	);
}

function EnvRow( { label, value } ) {
	return (
		<div className="flex items-center justify-between gap-6 py-[13px] px-7 max-[640px]:px-5 border-t border-[#f3f3f6] dark:border-ink-border">
			<span className="text-[13.5px] font-bold text-[#1a1a1f] dark:text-ink-text">{ label }</span>
			<span className="font-mono text-[12.5px] text-[#6c6c75] dark:text-ink-muted text-right break-all">{ value }</span>
		</div>
	);
}

export default function SystemStatusPage() {
	const checks = ( Array.isArray( config.system ) ? config.system : [] ).map( ( check ) => ( {
		...check,
		value: check.value ? `${ __( 'Currently:', 'ultimate-post-kit' ) } ${ check.value }` : '',
		recommendation: check.hint,
	} ) );
	const failed = checks.filter( ( c ) => ! c.pass ).length;
	const env = config.environment || {};
	const core = counts( 'ultimate_post_kit_active_modules' );
	const ext = counts( 'ultimate_post_kit_elementor_extend' );
	const features = counts( 'ultimate_post_kit_other_settings' );

	return (
		<div>
			<PageHeader
				title={ __( 'System Status', 'ultimate-post-kit' ) }
				subtitle={ __( 'Server requirements and environment overview.', 'ultimate-post-kit' ) }
			/>

			<div className="flex flex-col gap-4">
				{ failed > 0 ? (
					<Alert tone="warning">
						{ sprintf(
							/* translators: %d: number of failed checks */
							__( '%d requirement(s) need attention.', 'ultimate-post-kit' ),
							failed
						) }
					</Alert>
				) : null }

				<div className="grid grid-cols-3 max-[900px]:grid-cols-1 gap-3">
					<WidgetStatCard title={ __( 'Core widgets', 'ultimate-post-kit' ) } active={ core.active } total={ core.total } />
					<WidgetStatCard title={ __( 'Extensions', 'ultimate-post-kit' ) } active={ ext.active } total={ ext.total } />
					<WidgetStatCard title={ __( 'Features', 'ultimate-post-kit' ) } active={ features.active } total={ features.total } />
				</div>

				<SectionCard icon={ GaugeIcon } title={ __( 'System requirements', 'ultimate-post-kit' ) } count={ checks.length }>
					{ checks.map( ( row ) => (
						<StatusCheckRow key={ row.id } check={ row } />
					) ) }
					<p className="text-[12.5px] text-[#6d6d74] dark:text-ink-muted py-3 px-7 max-[640px]:px-5 border-t border-[#f3f3f6] dark:border-ink-border m-0">
						{ __( 'If you run several Elementor add-ons alongside Ultimate Post Kit, you may need to allocate more memory.', 'ultimate-post-kit' ) }
					</p>
				</SectionCard>

				<SectionCard
					icon={ CodeIcon }
					iconClassName="bg-[#f3f0ff] text-[#8b5cf6] dark:bg-[#241d3a]"
					title={ __( 'Environment', 'ultimate-post-kit' ) }
				>
					{ env.wp_version ? <EnvRow label={ __( 'WordPress', 'ultimate-post-kit' ) } value={ env.wp_version } /> : null }
					<EnvRow label={ config.title || 'Ultimate Post Kit' } value={ config.version } />
					{ env.php_version ? <EnvRow label={ __( 'PHP', 'ultimate-post-kit' ) } value={ env.php_version } /> : null }
					{ env.server ? <EnvRow label={ __( 'Server', 'ultimate-post-kit' ) } value={ env.server } /> : null }
					{ env.language ? <EnvRow label={ __( 'Language', 'ultimate-post-kit' ) } value={ env.language } /> : null }
				</SectionCard>
			</div>
		</div>
	);
}
