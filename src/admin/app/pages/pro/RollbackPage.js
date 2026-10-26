/**
 * Rollback to the previous backed-up Ultimate Post Kit Pro version — a port of
 * Element Pack's Rollback page.
 *
 * Data comes from Ultimate Post Kit Pro's `ultimate_post_kit/admin/react_pages` filter
 * (UltimatePostKit_Rollback_Version::react_page()); the rollback itself posts to
 * Pro's existing `upk_rollback_version` AJAX action, then reloads the page.
 */
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import PageHeader from '../../components/shell/PageHeader';
import EmptyState from '../../components/EmptyState';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Alert from '../../components/ui/Alert';
import { useToast } from '../../components/ui/Toast';
import { ChevronRightIcon, ClockIcon, WarnIcon } from '../../components/ui/Icons';
import config from '../../utils/config';

const GENERIC_ERROR = () =>
	__( 'An error occurred during rollback. Please try again.', 'ultimate-post-kit' );

/**
 * POST the rollback to admin-ajax.php. Resolves with the success message,
 * rejects with an Error carrying the server's message.
 *
 * @param {Object} data    Page data from the filter.
 * @param {string} version Version to roll back to.
 * @return {Promise<string>} Success message.
 */
async function requestRollback( data, version ) {
	const body = new window.FormData();
	body.append( 'action', data.action || 'upk_rollback_version' );
	body.append( 'version', version );
	body.append( 'nonce', data.nonce || '' );

	let json;
	try {
		const response = await window.fetch( config.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body,
		} );
		json = await response.json();
	} catch ( e ) {
		throw new Error( GENERIC_ERROR() );
	}

	if ( ! json || ! json.success ) {
		const message = json && json.data && json.data.message;
		throw new Error(
			message
				? sprintf(
						/* translators: %s: error message */
						__( 'Error: %s', 'ultimate-post-kit' ),
						message
				  )
				: GENERIC_ERROR()
		);
	}

	return ( json.data && json.data.message ) || '';
}

function MetaCell( { label, value } ) {
	return (
		<div className="py-[14px] px-6 max-[640px]:px-5">
			<div className="text-[10.5px] font-extrabold text-[#6d6d74] dark:text-ink-muted uppercase tracking-[.1em]">
				{ label }
			</div>
			<div className="text-[14px] font-bold text-[#1a1a1f] dark:text-ink-text mt-[5px]">
				{ value }
			</div>
		</div>
	);
}

export default function RollbackPage( { data = {} } ) {
	const showToast = useToast();
	const [ isSaving, setIsSaving ] = useState( false );
	const [ error, setError ] = useState( null );

	const previous = data.previous;

	const onRollback = async () => {
		if ( ! previous || isSaving ) {
			return;
		}

		/* eslint-disable no-alert */
		if (
			! window.confirm(
				sprintf(
					/* translators: %s: version number */
					__(
						'Are you sure you want to rollback to version %s? This action cannot be undone.',
						'ultimate-post-kit'
					),
					previous.version
				)
			)
		) {
			return;
		}
		/* eslint-enable no-alert */

		setError( null );
		setIsSaving( true );

		try {
			const message = await requestRollback( data, previous.version );
			showToast(
				message || __( 'Rollback complete.', 'ultimate-post-kit' )
			);
			// Keep the button busy until the page reloads on the restored version.
			window.setTimeout( () => window.location.reload(), 1500 );
		} catch ( e ) {
			setIsSaving( false );
			setError( e );
			showToast( e.message, { tone: 'error' } );
		}
	};

	return (
		<div>
			<PageHeader
				title={ __( 'Rollback Version', 'ultimate-post-kit' ) }
				subtitle={ sprintf(
					/* translators: %s: current version */
					__( 'Current version: %s', 'ultimate-post-kit' ),
					data.current_version
				) }
			/>

			<div className="flex flex-col gap-4">
				{ error && (
					<Alert tone="error" onDismiss={ () => setError( null ) }>
						{ error.message }
					</Alert>
				) }

				{ ! data.can_rollback || ! previous ? (
					<EmptyState
						title={ __(
							'No rollback available',
							'ultimate-post-kit'
						) }
						description={ __(
							'No previous version is available for rollback. This usually means this is a fresh installation or no backup was created during the last update.',
							'ultimate-post-kit'
						) }
					/>
				) : (
					<>
						<Alert tone="warning">
							<strong>
								{ __( 'Warning:', 'ultimate-post-kit' ) }
							</strong>{ ' ' }
							{ __(
								'Please backup your database before making the rollback.',
								'ultimate-post-kit'
							) }
						</Alert>

						<Card className="overflow-hidden">
							<div className="p-6 max-[640px]:p-5">
								<div className="flex items-center gap-3">
									<span className="w-[38px] h-[38px] rounded-tile flex-none grid place-items-center bg-[#fff7ed] text-[#f59e0b] dark:bg-[#2a1c0e]">
										<ClockIcon size={ 20 } />
									</span>
									<span className="text-base font-extrabold text-[#0f1115] dark:text-ink-text tracking-[-.01em]">
										{ __(
											'Restore a previous version',
											'ultimate-post-kit'
										) }
									</span>
								</div>

								{ /* Version journey: current → rollback target. */ }
								<div className="flex items-stretch gap-3 flex-wrap mt-5">
									<div className="flex-1 min-w-[190px] border border-[#e9e9ed] dark:border-ink-border rounded-panel py-4 px-5">
										<div className="text-[10.5px] font-extrabold uppercase tracking-[.1em] text-[#6d6d74] dark:text-ink-muted">
											{ __(
												'Current version',
												'ultimate-post-kit'
											) }
										</div>
										<div className="text-[24px] font-black tracking-[-.02em] text-[#0f1115] dark:text-ink-text mt-[4px]">
											v{ data.current_version }
										</div>
									</div>

									<div className="grid place-items-center text-[#c4c4cc] flex-none">
										<ChevronRightIcon size={ 22 } />
									</div>

									<div className="flex-1 min-w-[190px] border border-[#fed7aa] dark:border-[#4a3419] bg-[#fffbf5] dark:bg-[#2a1c0e] rounded-panel py-4 px-5">
										<div className="text-[10.5px] font-extrabold uppercase tracking-[.1em] text-[#c2410c] dark:text-[#fdba74]">
											{ __(
												'Rolling back to',
												'ultimate-post-kit'
											) }
										</div>
										<div className="text-[24px] font-black tracking-[-.02em] text-[#9a3412] dark:text-[#fdba74] mt-[4px]">
											v{ previous.version }
										</div>
									</div>
								</div>
							</div>

							{ /* Backup facts strip. */ }
							<div className="grid grid-cols-4 max-[760px]:grid-cols-2 border-t border-[#f1f1f4] dark:border-ink-border divide-x divide-[#f1f1f4] dark:divide-ink-border max-[760px]:divide-x-0 bg-[#fafbfc] dark:bg-ink-subtle">
								<MetaCell
									label={ __(
										'Backup date',
										'ultimate-post-kit'
									) }
									value={ previous.created_at }
								/>
								<MetaCell
									label={ __(
										'Backup type',
										'ultimate-post-kit'
									) }
									value={ previous.backup_type }
								/>
								{ previous.file_count > 0 && (
									<MetaCell
										label={ __(
											'Files',
											'ultimate-post-kit'
										) }
										value={ previous.file_count }
									/>
								) }
								{ previous.file_size_label && (
									<MetaCell
										label={ __(
											'Size',
											'ultimate-post-kit'
										) }
										value={ previous.file_size_label }
									/>
								) }
							</div>

							{ /* Action footer. */ }
							<div className="flex items-center justify-between gap-3 flex-wrap p-6 max-[640px]:p-5 border-t border-[#f1f1f4] dark:border-ink-border">
								<Button
									variant="danger-solid"
									disabled={ isSaving }
									onClick={ onRollback }
								>
									{ isSaving
										? __(
												'Rolling back…',
												'ultimate-post-kit'
										  )
										: sprintf(
												/* translators: %s: version number */
												__(
													'Rollback to v%s',
													'ultimate-post-kit'
												),
												previous.version
										  ) }
								</Button>
								<span className="flex items-center gap-[7px] text-[12.5px] font-semibold text-[#6d6d74] dark:text-ink-muted">
									<WarnIcon size={ 14 } />
									{ __(
										'This cannot be undone — back up your database first.',
										'ultimate-post-kit'
									) }
								</span>
							</div>
						</Card>
					</>
				) }
			</div>
		</div>
	);
}
