/**
 * API Settings (ultimate_post_kit_api_settings), laid out like Element Pack's
 * Integrations page: summary tiles, one row per service with its brand tile
 * and connection pill, and a Configure dialog holding the key fields.
 *
 * Each service is one start_group … end_group block of the settings schema.
 * Saving goes through the existing `ultimate_post_kit_settings_save` AJAX
 * action, which checks the nonce and manage_options and runs every value
 * through the field's sanitize callback. The whole section is posted, as the
 * classic form did.
 */
import { useCallback, useEffect, useMemo, useRef, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import PageHeader from '../components/shell/PageHeader';
import SaveBar from '../components/ui/SaveBar';
import ProgressBar from '../components/ui/ProgressBar';
import Alert from '../components/ui/Alert';
import { useToast } from '../components/ui/Toast';
import { CheckIcon, GaugeIcon, XIcon, PlayIcon, ShieldIcon } from '../components/ui/Icons';
import config, { sections } from '../utils/config';

const SECTION = 'ultimate_post_kit_api_settings';

/** Brand tile colours, matched on the group's field name. */
const SERVICE_COLORS = [
	{ match: 'mailchimp', color: '#ff9c00', category: __( 'Marketing', 'ultimate-post-kit' ) },
	{ match: 'instagram', color: '#e1306c', category: __( 'Social', 'ultimate-post-kit' ) },
];

function serviceMeta( key ) {
	return (
		SERVICE_COLORS.find( ( meta ) => String( key ).includes( meta.match ) ) || {
			color: '#00a85c',
			category: __( 'Other', 'ultimate-post-kit' ),
		}
	);
}

function isFilled( value ) {
	return String( value ?? '' ).trim() !== '';
}

const STATUS_STYLES = {
	connected: { color: 'text-[#15803d] bg-[#ecfdf3] dark:bg-[#0f2318] dark:text-[#4ade80]', dot: 'bg-[#22c55e]' },
	partial: { color: 'text-[#b45309] bg-[#fff7ed] dark:bg-[#2a1c0e] dark:text-[#fdba74]', dot: 'bg-[#f59e0b]' },
	empty: { color: 'text-[#6d6d74] bg-[#f4f4f5] dark:bg-ink-subtle dark:text-ink-muted', dot: 'bg-[#d4d4d8]' },
};

function statusLabel( status ) {
	if ( status === 'connected' ) {
		return __( 'Connected', 'ultimate-post-kit' );
	}
	if ( status === 'partial' ) {
		return __( 'Incomplete', 'ultimate-post-kit' );
	}
	return __( 'Not set', 'ultimate-post-kit' );
}

function StatusPill( { status } ) {
	const style = STATUS_STYLES[ status ] || STATUS_STYLES.empty;
	return (
		<span className={ `inline-flex items-center gap-[6px] text-[10px] font-extrabold py-[2px] px-[9px] rounded-full ${ style.color }` }>
			<span className={ `w-[6px] h-[6px] rounded-full ${ style.dot }` } />
			{ statusLabel( status ) }
		</span>
	);
}

function BrandTile( { group } ) {
	return (
		<span
			className="w-[46px] h-[46px] rounded-[12px] flex-none grid place-items-center text-white font-extrabold text-[17px] shadow-[inset_0_0_0_1px_rgba(255,255,255,.14)]"
			style={ { background: group.meta.color } }
			aria-hidden="true"
		>
			{ group.label.charAt( 0 ).toUpperCase() }
		</span>
	);
}

/** One key field: label, input (masked until shown), help text. */
function KeyField( { field, value, onChange } ) {
	const [ shown, setShown ] = useState( false );
	const id = `upk-api-${ field.name }`;

	return (
		<div className="py-4 px-6 border-b border-[#f1f1f4] dark:border-ink-border last:border-b-0">
			<label htmlFor={ id } className="block text-[13px] font-bold text-[#27272a] dark:text-ink-text mb-2">
				{ field.label }
			</label>
			<div className="flex gap-2">
				<input
					id={ id }
					type={ shown ? 'text' : 'password' }
					value={ value }
					onChange={ ( event ) => onChange( field.name, event.target.value ) }
					placeholder={ field.placeholder || '' }
					autoComplete="off"
					spellCheck={ false }
					className="flex-1 min-w-0 py-[10px] px-[14px] border border-[#e2e2e6] dark:border-ink-border rounded-btn font-mono text-[13px] bg-white dark:bg-ink-subtle text-[#1a1a1f] dark:text-ink-text outline-none transition-shadow focus:border-brand focus:shadow-[0_0_0_3px_rgba(8,201,114,.15)]"
				/>
				<button
					type="button"
					onClick={ () => setShown( ( current ) => ! current ) }
					aria-controls={ id }
					aria-pressed={ shown }
					className="flex-none py-[10px] px-[14px] rounded-btn border border-[#e2e2e6] dark:border-ink-border text-[12.5px] font-bold text-[#52525b] dark:text-ink-muted hover:border-brand hover:text-brand"
				>
					{ shown ? __( 'Hide', 'ultimate-post-kit' ) : __( 'Show', 'ultimate-post-kit' ) }
				</button>
			</div>
			{ field.desc ? (
				<p
					className="upk-rich-desc text-[12px] text-[#6d6d74] dark:text-ink-muted mt-2 mb-0 leading-[1.5]"
					dangerouslySetInnerHTML={ { __html: field.desc } }
				/>
			) : null }
		</div>
	);
}

function ConfigDialog( { group, values, onChange, onClose, onSave, isDirty, isSaving } ) {
	const dialogRef = useRef( null );
	const groupKey = group ? group.key : null;

	// Focus moves into the dialog when it opens and back when it closes.
	useEffect( () => {
		if ( ! groupKey ) {
			return undefined;
		}
		const previous = document.activeElement;
		const onKey = ( event ) => {
			if ( event.key === 'Escape' ) {
				onClose();
			}
		};
		document.addEventListener( 'keydown', onKey );
		const node = dialogRef.current;
		const first = node && ( node.querySelector( 'input' ) || node.querySelector( 'button' ) );
		if ( first ) {
			first.focus();
		}
		return () => {
			document.removeEventListener( 'keydown', onKey );
			if ( previous && previous.focus ) {
				previous.focus();
			}
		};
	}, [ groupKey, onClose ] );

	if ( ! group ) {
		return null;
	}

	const titleId = `upk-api-dialog-${ group.key }`;

	return (
		<div
			className="fixed inset-0 z-[100000] grid place-items-center p-5 bg-[rgba(9,9,11,.6)] backdrop-blur-sm animate-upk-fade-up"
			onClick={ ( event ) => {
				if ( event.target === event.currentTarget ) {
					onClose();
				}
			} }
			role="presentation"
		>
			<div
				ref={ dialogRef }
				role="dialog"
				aria-modal="true"
				aria-labelledby={ titleId }
				className="w-full max-w-[640px] max-h-[86vh] overflow-y-auto bg-white dark:bg-ink-card rounded-sheet shadow-sheet animate-upk-pop"
			>
				<div className="flex items-center gap-[15px] py-5 px-6 border-b border-[#f1f1f4] dark:border-ink-border sticky top-0 bg-white dark:bg-ink-card z-10">
					<BrandTile group={ group } />
					<div className="flex-1 min-w-0">
						<div className="flex items-center gap-2 flex-wrap">
							<h2 id={ titleId } className="text-[16px] font-extrabold text-[#0f1115] dark:text-ink-text">
								{ group.label }
							</h2>
							<StatusPill status={ group.status } />
						</div>
						{ group.desc ? (
							<p
								className="upk-rich-desc text-[12.5px] text-[#6d6d74] dark:text-ink-muted mt-1 mb-0 leading-[1.5] break-words"
								dangerouslySetInnerHTML={ { __html: group.desc } }
							/>
						) : null }
					</div>
					<button
						type="button"
						onClick={ onClose }
						aria-label={ __( 'Close', 'ultimate-post-kit' ) }
						className="w-9 h-9 rounded-full flex-none grid place-items-center bg-[#f4f5f7] dark:bg-ink-subtle text-[#52525b] dark:text-ink-muted hover:text-[#0f1115] dark:hover:text-ink-text"
					>
						<XIcon size={ 16 } />
					</button>
				</div>

				<div>
					{ group.inputs.map( ( field ) => (
						<KeyField key={ field.name } field={ field } value={ values[ field.name ] ?? '' } onChange={ onChange } />
					) ) }
				</div>

				<div className="flex items-center justify-between gap-3 py-4 px-6 border-t border-[#f1f1f4] dark:border-ink-border sticky bottom-0 bg-white dark:bg-ink-card flex-wrap">
					<span className="text-[11.5px] text-[#6d6d74] dark:text-ink-muted">
						{ sprintf(
							/* translators: 1: filled count, 2: total fields */
							__( '%1$d of %2$d fields filled', 'ultimate-post-kit' ),
							group.filled,
							group.inputs.length
						) }
					</span>
					<div className="flex gap-[10px]">
						<button
							type="button"
							onClick={ onClose }
							className="bg-white dark:bg-ink-card border border-[#e2e2e6] dark:border-ink-border text-[#3f3f46] dark:text-ink-text py-[10px] px-[18px] rounded-full text-[13px] font-bold hover:border-brand hover:text-brand"
						>
							{ __( 'Close', 'ultimate-post-kit' ) }
						</button>
						<button
							type="button"
							disabled={ ! isDirty || isSaving }
							onClick={ onSave }
							className="flex items-center gap-[7px] bg-gradient-brand text-white py-[10px] px-[22px] rounded-full text-[13px] font-extrabold shadow-glow disabled:opacity-50 disabled:cursor-not-allowed"
						>
							<CheckIcon size={ 14 } strokeWidth={ 2.4 } />
							{ isSaving ? __( 'Saving…', 'ultimate-post-kit' ) : __( 'Save changes', 'ultimate-post-kit' ) }
						</button>
					</div>
				</div>
			</div>
		</div>
	);
}

async function saveApiSection( values ) {
	const section = sections[ SECTION ] || {};
	const merged = { ...( section.stored || {} ), ...values };

	const body = new window.FormData();
	body.append( 'action', 'ultimate_post_kit_settings_save' );
	body.append( '_wpnonce', config.saveNonce || '' );
	body.append( 'id', SECTION );
	Object.keys( merged ).forEach( ( name ) => {
		body.append( `${ SECTION }[${ name }]`, merged[ name ] );
	} );

	const response = await window.fetch( config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body } );
	let json = null;
	try {
		json = await response.json();
	} catch ( e ) {
		json = null;
	}
	if ( ! response.ok || ! json || ! json.success ) {
		throw new Error( __( 'Settings could not be saved. Reload the page and try again.', 'ultimate-post-kit' ) );
	}

	// The server trims and strips tags; later saves start from what is stored.
	section.stored = merged;
	return merged;
}

export default function ApiSettingsPage() {
	const section = sections[ SECTION ] || {};
	const groups = useMemo( () => section.groups || [], [ section.groups ] );
	const inputs = useMemo( () => section.inputs || [], [ section.inputs ] );
	const showToast = useToast();

	const initial = useMemo( () => {
		const state = {};
		inputs.forEach( ( field ) => {
			state[ field.name ] = String( ( section.stored || {} )[ field.name ] ?? '' );
		} );
		return state;
	}, [ inputs, section.stored ] );

	const [ saved, setSaved ] = useState( initial );
	const [ draft, setDraft ] = useState( initial );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ error, setError ] = useState( null );
	const [ openKey, setOpenKey ] = useState( null );

	const isDirty = Object.keys( draft ).some( ( name ) => draft[ name ] !== saved[ name ] );

	const withStatus = groups.map( ( group ) => {
		const groupInputs = inputs.filter( ( field ) => field.group === group.key );
		const filled = groupInputs.filter( ( field ) => isFilled( draft[ field.name ] ) ).length;
		let status = 'empty';
		if ( groupInputs.length && filled === groupInputs.length ) {
			status = 'connected';
		} else if ( filled > 0 ) {
			status = 'partial';
		}
		return { ...group, inputs: groupInputs, filled, status, meta: serviceMeta( group.key ) };
	} );

	const connected = withStatus.filter( ( group ) => group.status === 'connected' ).length;
	const health = inputs.length
		? Math.round( ( inputs.filter( ( field ) => isFilled( draft[ field.name ] ) ).length / inputs.length ) * 100 )
		: 0;
	const openGroup = openKey ? withStatus.find( ( group ) => group.key === openKey ) || null : null;

	const onChange = useCallback( ( name, value ) => setDraft( ( current ) => ( { ...current, [ name ]: value } ) ), [] );
	const closeDialog = useCallback( () => setOpenKey( null ), [] );

	const onSave = async () => {
		setIsSaving( true );
		setError( null );
		try {
			const stored = await saveApiSection( draft );
			const next = {};
			Object.keys( draft ).forEach( ( name ) => {
				next[ name ] = String( stored[ name ] ?? '' );
			} );
			setSaved( next );
			showToast( __( 'API settings saved.', 'ultimate-post-kit' ) );
		} catch ( e ) {
			setError( e );
			showToast( e.message, { tone: 'error' } );
		} finally {
			setIsSaving( false );
		}
	};

	return (
		<div>
			<PageHeader
				title={ __( 'API Settings', 'ultimate-post-kit' ) }
				subtitle={ __( 'Connect the services the Mailchimp and Instagram widgets use.', 'ultimate-post-kit' ) }
			/>

			<div className="flex flex-col gap-4">
				{ error ? <Alert tone="error">{ error.message }</Alert> : null }

				<div className="grid grid-cols-3 max-[900px]:grid-cols-1 gap-[14px]">
					<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-panel p-[18px]">
						<div className="flex items-center gap-[9px]">
							<span className="w-[34px] h-[34px] rounded-[10px] grid place-items-center bg-[#ecfdf3] text-[#1a7d3d] dark:bg-[#0f2318] dark:text-[#4ade80]">
								<CheckIcon size={ 18 } />
							</span>
							<span className="text-[13px] font-bold text-[#6d6d74] dark:text-ink-muted">{ __( 'Connected', 'ultimate-post-kit' ) }</span>
						</div>
						<div className="text-[30px] font-black tracking-[-.03em] text-[#0f1115] dark:text-ink-text mt-[10px]">
							{ connected }
							<span className="text-base font-bold text-[#6d6d74] dark:text-ink-muted">{ ` / ${ withStatus.length }` }</span>
						</div>
					</div>
					<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-panel p-[18px]">
						<div className="flex items-center gap-[9px]">
							<span className="w-[34px] h-[34px] rounded-[10px] grid place-items-center bg-[#eafaf1] text-brand dark:bg-[#0f2a1c]">
								<GaugeIcon size={ 18 } />
							</span>
							<span className="text-[13px] font-bold text-[#6d6d74] dark:text-ink-muted">{ __( 'Setup health', 'ultimate-post-kit' ) }</span>
						</div>
						<div className="flex items-center gap-3 mt-[14px]">
							<ProgressBar percent={ health } height={ 9 } className="flex-1" />
							<span className="text-[15px] font-extrabold text-brand tabular-nums">{ health }%</span>
						</div>
					</div>
					<div className="rounded-panel p-[18px] text-white relative overflow-hidden [background:linear-gradient(135deg,#0a1912,#0f3a26)]">
						<div className="absolute -top-10 -right-5 w-[150px] h-[150px] rounded-full [background:radial-gradient(circle,rgba(132,237,146,.25),transparent_65%)]" />
						<div className="relative flex gap-3">
							<ShieldIcon size={ 18 } className="flex-none text-[#86efac] mt-[2px]" />
							<div>
								<div className="text-xs font-bold text-white/60">{ __( 'Your keys stay here', 'ultimate-post-kit' ) }</div>
								<div className="text-sm font-bold mt-[6px] leading-[1.4]">
									{ __( 'Keys are stored in your own database and only sent to the service they belong to.', 'ultimate-post-kit' ) }
								</div>
							</div>
						</div>
					</div>
				</div>

				<div className="flex flex-col gap-3">
					{ withStatus.map( ( group ) => (
						<div
							key={ group.key }
							className={ `bg-white dark:bg-ink-card border rounded-panel transition-colors ${
								group.status === 'connected'
									? 'border-[#c6f0d4] dark:border-[#1d4030]'
									: 'border-[#e9e9ed] dark:border-ink-border'
							}` }
						>
							<div className="flex items-center gap-[15px] py-4 px-[18px] max-[640px]:flex-wrap">
								<BrandTile group={ group } />
								<div className="flex-1 min-w-0">
									<div className="flex items-center gap-2 flex-wrap">
										<span className="text-[15px] font-bold text-[#1a1a1f] dark:text-ink-text">{ group.label }</span>
										<span className="text-[10px] font-extrabold text-[#6d6d74] bg-[#f4f4f6] dark:bg-ink-subtle dark:text-ink-muted py-[2px] px-2 rounded-full">
											{ group.meta.category }
										</span>
										<StatusPill status={ group.status } />
									</div>
									<div className="flex items-center gap-[10px] flex-wrap mt-1 text-[12px] text-[#6d6d74] dark:text-ink-muted">
										<span>
											{ sprintf(
												/* translators: 1: filled, 2: total fields */
												__( '%1$d / %2$d fields', 'ultimate-post-kit' ),
												group.filled,
												group.inputs.length
											) }
										</span>
										{ group.video_url ? (
											<a href={ group.video_url } target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 font-semibold hover:text-brand">
												<PlayIcon size={ 13 } />
												{ __( 'Video guide', 'ultimate-post-kit' ) }
											</a>
										) : null }
									</div>
								</div>
								<button
									type="button"
									onClick={ () => setOpenKey( group.key ) }
									className={ `flex items-center gap-[7px] py-[9px] px-[15px] rounded-full text-[12.5px] font-bold flex-none border transition-all duration-150 ${
										group.status === 'connected'
											? 'bg-white dark:bg-ink-card border-[#e2e2e6] dark:border-ink-border text-[#3f3f46] dark:text-ink-text hover:border-brand hover:text-brand'
											: 'bg-brand border-brand text-white hover:opacity-90'
									}` }
								>
									{ group.status === 'connected' ? __( 'Configure', 'ultimate-post-kit' ) : __( 'Connect', 'ultimate-post-kit' ) }
								</button>
							</div>
						</div>
					) ) }
				</div>

				<ConfigDialog
					group={ openGroup }
					values={ draft }
					onChange={ onChange }
					onClose={ closeDialog }
					onSave={ onSave }
					isDirty={ isDirty }
					isSaving={ isSaving }
				/>

				<SaveBar visible={ isDirty && ! openGroup } saving={ isSaving } onSave={ onSave } onReset={ () => setDraft( saved ) } />
			</div>
		</div>
	);
}
