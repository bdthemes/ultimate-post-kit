/**
 * License: activation, status and deactivation, laid out like Element Pack's
 * License page.
 *
 * Ultimate Post Kit has no REST store: Ultimate Post Kit Pro hands the page its data
 * through the `ultimate_post_kit/admin/react_pages` filter (panel
 * `ultimate_post_kit_license_settings`). Activation and deactivation post the same
 * admin-post forms as Pro's PHP license view (action + `upk-license` nonce), so
 * the licence server request stays in Pro's own handlers; the handler
 * redirects back here. "Refresh" reloads the page.
 */
import { useState } from '@wordpress/element';
import { __, sprintf, _n } from '@wordpress/i18n';
import PageHeader from '../../components/shell/PageHeader';
import EmptyState from '../../components/EmptyState';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Alert from '../../components/ui/Alert';
import {
	RefreshIcon,
	RocketIcon,
	BrushIcon,
	KeyIcon,
} from '../../components/ui/Icons';
import config from '../../utils/config';
import PanelPage from '../PanelPage';

const INPUT_CLASS =
	'w-full py-[10px] px-[14px] border border-[#e2e2e6] dark:border-[#2a313c] rounded-btn text-sm bg-white dark:bg-ink-card text-[#18181b] dark:text-ink-text outline-none placeholder:text-[#6d6d74] dark:placeholder:text-ink-muted focus:border-brand focus:shadow-[0_0_0_3px_rgba(8,201,114,.15)]';

const HERO_BUTTON_CLASS =
	'inline-flex items-center gap-[7px] bg-white/10 border border-white/20 text-white py-[9px] px-[18px] rounded-full text-[13px] font-semibold cursor-pointer transition-colors hover:bg-white/20 disabled:opacity-50 disabled:cursor-not-allowed';

const PLAN_CARD_CLASS =
	'rounded-card p-[22px] border border-[#fde0d0] dark:border-ink-border [background:linear-gradient(135deg,#eafaf1,#ecfdf3)] dark:[background:linear-gradient(135deg,#0f2a1c,#0f2318)]';

const PLAN_LINK_CLASS =
	'block text-center w-full bg-white dark:bg-ink-card border border-[#fde0d0] dark:border-ink-border text-brand py-[10px] rounded-full text-[13px] font-bold cursor-pointer transition-colors hover:bg-brand hover:text-white';

const STEP_BADGE_CLASS =
	'w-6 h-6 rounded-full flex-none grid place-items-center bg-[#eafaf1] text-brand dark:bg-[#0f2a1c] text-[11.5px] font-extrabold';

const STEP_TEXT_CLASS =
	'text-[13px] leading-[1.55] text-[#3f3f46] dark:text-[#cdd0d6]';

function productName() {
	return config.title || 'Ultimate Post Kit';
}

/**
 * The hidden fields Pro's PHP forms carry: the admin-post action, the
 * `upk-license` nonce (as wp_nonce_field() prints it) and the referer.
 */
function FormFields( { form, action } ) {
	return (
		<>
			<input type="hidden" name="action" value={ action } />
			<input
				type="hidden"
				name={ form.nonce_field || '_wpnonce' }
				value={ form.nonce || '' }
			/>
			{ form.referer ? (
				<input
					type="hidden"
					name={ form.referer_field || '_wp_http_referer' }
					value={ form.referer }
				/>
			) : null }
		</>
	);
}

function HeroMeta( { label, value } ) {
	return (
		<div>
			<div className="text-[11px] font-semibold text-white/50">
				{ label }
			</div>
			<div className="text-base font-bold mt-[3px]">{ value }</div>
		</div>
	);
}

/**
 * White-label upsell for licence tiers that don't include the feature. Pro
 * reports eligibility; when it can't tell (null), the card is left out.
 */
function WhiteLabelUpsellCard( { data } ) {
	if ( data.white_label_eligible !== false ) {
		return null;
	}

	return (
		<Card className="p-5">
			<div className="flex items-center gap-3 mb-3">
				<span className="w-[38px] h-[38px] rounded-tile flex-none grid place-items-center bg-[#f5f0fb] text-[#7f54b3] dark:text-[#c4a5ec] dark:bg-[#241d3a]">
					<BrushIcon size={ 20 } />
				</span>
				<div className="text-base font-extrabold text-[#0f1115] dark:text-ink-text">
					{ __( 'Unlock White Label', 'ultimate-post-kit' ) }
				</div>
			</div>
			<p className="text-[12.5px] text-[#6c6c75] dark:text-ink-muted m-0 mb-4 leading-[1.55]">
				{ __(
					'Rebrand Ultimate Post Kit for your clients — custom name, logo and hidden branding. Available on Agency, Extended, Developer and other eligible licenses.',
					'ultimate-post-kit'
				) }
			</p>
			<a
				href={ data.links?.pricing }
				target="_blank"
				rel="noreferrer"
				className="block text-center w-full bg-white dark:bg-ink-card border border-[#e2d9f3] dark:border-ink-border text-[#7f54b3] dark:text-[#c4a5ec] py-[10px] rounded-full text-[13px] font-bold cursor-pointer transition-colors hover:bg-[#7f54b3] hover:text-white"
			>
				{ __( 'Upgrade license', 'ultimate-post-kit' ) }
			</a>
		</Card>
	);
}

/**
 * Right-column account + plan cards. Only real licence fields are shown.
 */
function AccountColumn( { data } ) {
	const initial = ( data.email || 'E' ).charAt( 0 ).toUpperCase();

	return (
		<div className="flex flex-col gap-4">
			<Card className="p-5">
				<div className="text-[17px] font-extrabold text-[#0f1115] dark:text-ink-text mb-[14px]">
					{ __( 'Account', 'ultimate-post-kit' ) }
				</div>
				<div className="flex items-center gap-3">
					<div className="w-[46px] h-[46px] rounded-full bg-gradient-brand grid place-items-center text-white text-lg font-bold">
						{ initial }
					</div>
					<div className="min-w-0">
						{ data.license_title ? (
							<div className="text-sm font-semibold text-[#27272a] dark:text-ink-text">
								{ data.license_title }
							</div>
						) : null }
						{ data.email ? (
							<div className="text-[12.5px] text-[#6d6d74] dark:text-ink-muted break-all">
								{ data.email }
							</div>
						) : null }
					</div>
				</div>
				<a
					href={ data.links?.account }
					target="_blank"
					rel="noreferrer"
					className="block text-center w-full mt-4 bg-[#f4f6f9] dark:bg-ink-subtle border border-[#e6e8ec] dark:border-ink-border text-[#3f3f46] dark:text-ink-text py-[10px] rounded-full text-[13px] font-semibold cursor-pointer transition-colors hover:border-brand hover:text-brand"
				>
					{ __( 'Manage on bdthemes.com', 'ultimate-post-kit' ) }
				</a>
			</Card>

			<WhiteLabelUpsellCard data={ data } />

			{ config.isPro ? (
				<div className={ PLAN_CARD_CLASS }>
					<div className="w-[42px] h-[42px] rounded-[12px] bg-gradient-brand grid place-items-center text-white mb-[14px]">
						<RocketIcon size={ 22 } />
					</div>
					<div className="text-base font-bold text-[#18181b] dark:text-ink-text">
						{ __(
							"You're on the Pro plan",
							'ultimate-post-kit'
						) }
					</div>
					<p className="text-[12.5px] text-[#52525b] dark:text-ink-muted mt-[6px] mb-4 leading-[1.55]">
						{ __(
							'Updates, priority support and every future widget — already yours.',
							'ultimate-post-kit'
						) }
					</p>
					<a
						href={ data.links?.account }
						target="_blank"
						rel="noreferrer"
						className={ PLAN_LINK_CLASS }
					>
						{ __( 'Manage subscription', 'ultimate-post-kit' ) }
					</a>
				</div>
			) : (
				<div className={ PLAN_CARD_CLASS }>
					<div className="w-[42px] h-[42px] rounded-[12px] bg-gradient-brand grid place-items-center text-white mb-[14px]">
						<RocketIcon size={ 22 } />
					</div>
					<div className="text-base font-bold text-[#18181b] dark:text-ink-text">
						{ __( 'Go further with Pro', 'ultimate-post-kit' ) }
					</div>
					<p className="text-[12.5px] text-[#52525b] dark:text-ink-muted mt-[6px] mb-4 leading-[1.55]">
						{ __(
							'Unlock every widget, feature and premium support.',
							'ultimate-post-kit'
						) }
					</p>
					<a
						href={ data.links?.pricing || config.pricingUrl }
						target="_blank"
						rel="noreferrer"
						className={ PLAN_LINK_CLASS }
					>
						{ __( 'View pricing', 'ultimate-post-kit' ) }
					</a>
				</div>
			) }
		</div>
	);
}

function renewMessage( renew ) {
	if ( ! renew.show ) {
		return '';
	}

	if ( renew.expired ) {
		return sprintf(
			/* translators: 1: product name, 2: license expiry date */
			__(
				'Your %1$s license expired on %2$s.',
				'ultimate-post-kit'
			),
			productName(),
			renew.expire_date
		);
	}

	const days = Math.max( 0, renew.days_left || 0 );

	return sprintf(
		/* translators: 1: product name, 2: number of days until expiry, 3: expiry date */
		_n(
			'Your %1$s license expires in %2$d day (%3$s).',
			'Your %1$s license expires in %2$d days (%3$s).',
			days,
			'ultimate-post-kit'
		),
		productName(),
		days,
		renew.expire_date
	);
}

export default function LicensePage( { panel, data } ) {
	// Licence key and email typed into the activation form. The email starts
	// as Pro's stored licence email (or the admin email), like Element Pack.
	const [ licenseKey, setLicenseKey ] = useState( '' );
	const [ licenseEmail, setLicenseEmail ] = useState( null );
	const [ submitting, setSubmitting ] = useState( false );
	const [ refreshing, setRefreshing ] = useState( false );
	const [ showMessage, setShowMessage ] = useState( true );

	// No data from Pro (older Pro): keep the PHP panel.
	if ( ! data ) {
		return <PanelPage panel={ panel } />;
	}

	if ( data.page_hidden ) {
		return (
			<div>
				<PageHeader
					title={ __( 'License', 'ultimate-post-kit' ) }
				/>
				<EmptyState
					title={ __(
						'License management is hidden',
						'ultimate-post-kit'
					) }
					description={ __(
						'This install uses white-label licensing. Manage the license from your agency dashboard.',
						'ultimate-post-kit'
					) }
				/>
			</div>
		);
	}

	const form = data.form || {};
	const links = data.links || {};
	const renew = data.renew || {};
	const canManage = data.can_manage !== false;

	const reload = () => {
		setRefreshing( true );
		window.location.reload();
	};

	const onDeactivate = ( event ) => {
		/* eslint-disable no-alert */
		if (
			! window.confirm(
				__(
					'Deactivate this license on this site?',
					'ultimate-post-kit'
				)
			)
		) {
			event.preventDefault();
			return;
		}
		/* eslint-enable no-alert */
		setSubmitting( true );
	};

	const onActivate = ( event ) => {
		if ( ! licenseKey.trim() ) {
			event.preventDefault();
			return;
		}
		setSubmitting( true );
	};

	return (
		<div>
			<PageHeader
				title={ __( 'License', 'ultimate-post-kit' ) }
				subtitle={ __(
					'Manage your activation, updates and subscription.',
					'ultimate-post-kit'
				) }
			/>

			<div className="flex flex-col gap-4">
				{ data.message && showMessage && (
					<Alert tone="error" onDismiss={ () => setShowMessage( false ) }>
						{ data.message }
					</Alert>
				) }

				{ renew.show && (
					<Alert tone={ renew.expired ? 'error' : 'warning' }>
						{ renewMessage( renew ) }{ ' ' }
						<a
							href={ renew.renew_url }
							target="_blank"
							rel="noreferrer"
							className="font-bold underline text-current"
						>
							{ __( 'Renew now', 'ultimate-post-kit' ) }
						</a>
					</Alert>
				) }

				{ data.activated ? (
					<div className="grid grid-cols-[1.5fr_1fr] max-[1024px]:grid-cols-1 gap-4 items-start">
						<div className="flex flex-col gap-4 min-w-0">
							<div className="bg-[linear-gradient(135deg,#0a1912,#0f3a26)] text-white rounded-card p-7 relative overflow-hidden">
								<span
									className="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-[radial-gradient(circle,rgba(132,237,146,.25),transparent_70%)]"
									aria-hidden="true"
								/>
								<div className="relative">
									<div className="flex items-center justify-between gap-3 flex-wrap">
										<div
											className={ `flex items-center gap-[9px] text-[11px] font-bold tracking-[.12em] uppercase ${
												data.valid
													? 'text-[#84ed92]'
													: 'text-[#fda4af]'
											}` }
											title={ data.status || undefined }
										>
											<span
												className={ `w-2 h-2 rounded-full ${
													data.valid
														? 'bg-[#84ed92] shadow-[0_0_0_3px_rgba(132,237,146,.25)]'
														: 'bg-[#f43f5e] shadow-[0_0_0_3px_rgba(244,63,94,.25)]'
												}` }
											/>
											{ data.valid
												? __(
														'Active',
														'ultimate-post-kit'
												  )
												: __(
														'Invalid license',
														'ultimate-post-kit'
												  ) }
										</div>
										{ data.license_title && (
											<span className="text-[12.5px] font-bold bg-white/[.12] py-[5px] px-3 rounded-full">
												{ data.license_title }
											</span>
										) }
									</div>

									{ data.license_key_masked && (
										<>
											<div className="text-xs text-white/[.55] mt-[18px] font-semibold">
												{ __(
													'License key',
													'ultimate-post-kit'
												) }
											</div>
											<div className="mt-[7px]">
												<code className="text-[15px] font-semibold tracking-[.06em] font-mono">
													{ data.license_key_masked }
												</code>
											</div>
										</>
									) }

									<div className="grid grid-cols-3 max-[640px]:grid-cols-1 gap-[14px] mt-[22px] pt-[18px] border-t border-white/[.12]">
										{ data.expire_date && (
											<HeroMeta
												label={ __(
													'License expires',
													'ultimate-post-kit'
												) }
												value={ data.expire_date }
											/>
										) }
										{ data.support_end && (
											<HeroMeta
												label={ __(
													'Support expires',
													'ultimate-post-kit'
												) }
												value={ data.support_end }
											/>
										) }
										{ data.license_title && (
											<HeroMeta
												label={ __(
													'License type',
													'ultimate-post-kit'
												) }
												value={ data.license_title }
											/>
										) }
										{ data.email && (
											<HeroMeta
												label={ __(
													'License email',
													'ultimate-post-kit'
												) }
												value={ data.email }
											/>
										) }
									</div>

									<form
										method="post"
										action={ form.url }
										onSubmit={ onDeactivate }
										className="flex gap-[10px] mt-[22px] pt-[18px] border-t border-white/[.12]"
									>
										<FormFields
											form={ form }
											action={ form.deactivate_action }
										/>
										{ canManage && (
											<button
												type="submit"
												className={ HERO_BUTTON_CLASS }
												disabled={ submitting }
											>
												{ __(
													'Deactivate license',
													'ultimate-post-kit'
												) }
											</button>
										) }
										<button
											type="button"
											className={ HERO_BUTTON_CLASS }
											disabled={ refreshing || submitting }
											onClick={ reload }
										>
											<RefreshIcon
												size={ 14 }
												className={
													refreshing
														? 'animate-upk-spin'
														: ''
												}
											/>
											{ __(
												'Refresh',
												'ultimate-post-kit'
											) }
										</button>
									</form>
								</div>
							</div>
						</div>
						<AccountColumn data={ data } />
					</div>
				) : (
					<div className="grid grid-cols-[1.5fr_1fr] max-[1024px]:grid-cols-1 gap-4 items-start">
						{ /* Activation form card — SectionCard-style header. */ }
						<Card className="overflow-hidden">
							<div className="flex items-center gap-3 py-[18px] px-7 max-[640px]:px-5 border-b border-[#f3f3f6] dark:border-ink-border">
								<span className="w-[38px] h-[38px] rounded-tile flex-none grid place-items-center bg-[#ecfdf3] text-[#1a7d3d] dark:text-[#4ade80] dark:bg-[#0f2318]">
									<KeyIcon size={ 20 } />
								</span>
								<span className="text-base font-extrabold text-[#0f1115] dark:text-ink-text tracking-[-.01em]">
									{ __(
										'Activate your license',
										'ultimate-post-kit'
									) }
								</span>
							</div>

							<div className="p-7 max-[640px]:p-5">
								<p className="text-[13.5px] text-[#6c6c75] dark:text-ink-muted mt-0 mb-5 leading-[1.55]">
									{ __(
										'Enter your license key to activate Ultimate Post Kit Pro and receive updates and premium support.',
										'ultimate-post-kit'
									) }
								</p>

								<form
									method="post"
									action={ form.url }
									onSubmit={ onActivate }
									className="flex flex-col gap-4"
								>
									<FormFields
										form={ form }
										action={ form.activate_action }
									/>
									<div>
										<label
											htmlFor="upk-license-email"
											className="block text-[13px] font-bold text-[#1a1a1f] dark:text-ink-text mb-[6px]"
										>
											{ __(
												'License email',
												'ultimate-post-kit'
											) }
										</label>
										<input
											id="upk-license-email"
											type="email"
											name={
												form.email_field ||
												'ultimate_post_kit_license_email'
											}
											autoComplete="email"
											placeholder="e.g. yourname@gmail.com"
											required
											disabled={ ! canManage }
											className={ INPUT_CLASS }
											value={
												licenseEmail ??
												( data.default_email || '' )
											}
											onChange={ ( event ) =>
												setLicenseEmail(
													event.target.value
												)
											}
										/>
									</div>
									<div>
										<label
											htmlFor="upk-license-key"
											className="block text-[13px] font-bold text-[#1a1a1f] dark:text-ink-text mb-[6px]"
										>
											{ __(
												'License code',
												'ultimate-post-kit'
											) }
										</label>
										<input
											id="upk-license-key"
											type="text"
											name={
												form.key_field ||
												'ultimate_post_kit_license_key'
											}
											autoComplete="off"
											placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx"
											required
											disabled={ ! canManage }
											className={ `${ INPUT_CLASS } font-mono tracking-[.04em]` }
											value={ licenseKey }
											onChange={ ( event ) =>
												setLicenseKey(
													event.target.value
												)
											}
										/>
									</div>

									<div>
										<Button
											type="submit"
											variant="gradient"
											size="lg"
											disabled={
												! canManage ||
												submitting ||
												! licenseKey.trim()
											}
										>
											{ submitting
												? __(
														'Activating…',
														'ultimate-post-kit'
												  )
												: __(
														'Activate license',
														'ultimate-post-kit'
												  ) }
										</Button>
									</div>
								</form>
							</div>
						</Card>

						{ /* Right column: numbered how-to + pricing card. */ }
						<div className="flex flex-col gap-4">
							<Card className="p-5">
								<div className="text-[17px] font-extrabold text-[#0f1115] dark:text-ink-text mb-[14px]">
									{ __(
										'Where is my key?',
										'ultimate-post-kit'
									) }
								</div>
								<ol className="m-0 p-0 flex flex-col gap-[14px]">
									<li className="flex items-start gap-3">
										<span className={ STEP_BADGE_CLASS }>
											1
										</span>
										<span className={ STEP_TEXT_CLASS }>
											{ __(
												'Log in to your',
												'ultimate-post-kit'
											) }{ ' ' }
											<a
												href={ links.account }
												target="_blank"
												rel="noreferrer"
												className="text-brand font-bold hover:underline"
											>
												{ __(
													'bdthemes account',
													'ultimate-post-kit'
												) }
											</a>{ ' ' }
											{ __(
												'or',
												'ultimate-post-kit'
											) }{ ' ' }
											<a
												href={ links.fastspring }
												target="_blank"
												rel="noreferrer"
												className="text-brand font-bold hover:underline"
											>
												{ __(
													'FastSpring account',
													'ultimate-post-kit'
												) }
											</a>
											.
										</span>
									</li>
									<li className="flex items-start gap-3">
										<span className={ STEP_BADGE_CLASS }>
											2
										</span>
										<span className={ STEP_TEXT_CLASS }>
											{ __(
												'Copy the license key from your purchase.',
												'ultimate-post-kit'
											) }
										</span>
									</li>
									<li className="flex items-start gap-3">
										<span className={ STEP_BADGE_CLASS }>
											3
										</span>
										<span className={ STEP_TEXT_CLASS }>
											{ __(
												'Paste it in the form and activate.',
												'ultimate-post-kit'
											) }
										</span>
									</li>
								</ol>
							</Card>

							<div className={ PLAN_CARD_CLASS }>
								<div className="w-[42px] h-[42px] rounded-[12px] bg-gradient-brand grid place-items-center text-white mb-[14px]">
									<RocketIcon size={ 22 } />
								</div>
								<div className="text-base font-bold text-[#18181b] dark:text-ink-text">
									{ __(
										"Don't have a license yet?",
										'ultimate-post-kit'
									) }
								</div>
								<p className="text-[12.5px] text-[#52525b] dark:text-ink-muted mt-[6px] mb-4 leading-[1.55]">
									{ __(
										'Unlock every widget, feature and premium support.',
										'ultimate-post-kit'
									) }
								</p>
								<a
									href={ links.get_pro || config.getProUrl }
									target="_blank"
									rel="noreferrer"
									className={ PLAN_LINK_CLASS }
								>
									{ __(
										'Get Ultimate Post Kit Pro',
										'ultimate-post-kit'
									) }
								</a>
							</div>
						</div>
					</div>
				) }
			</div>
		</div>
	);
}
