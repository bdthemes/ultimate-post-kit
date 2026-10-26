/**
 * Dashboard promo: Ultimate Post Kit Pro on the free plugin; the latest release
 * once Pro is active. Hidden under white label.
 */
import { __, sprintf } from '@wordpress/i18n';
import { Link } from 'react-router-dom';
import config from '../../utils/config';
import { SparklesIcon } from '../ui/Icons';

export default function PromoCard() {
	if ( config.whiteLabel ) {
		return null;
	}

	const pro = ! config.isPro;

	return (
		<div className="rounded-card p-5 relative overflow-hidden text-white [background:linear-gradient(135deg,#0a1912,#0f3a26)]">
			<div className="absolute inset-0 pointer-events-none [background:radial-gradient(circle_at_85%_0%,rgba(8,201,114,.3),transparent_55%)]" />
			<div className="relative">
				<div className="flex items-center gap-2 text-[11px] font-bold tracking-[.12em] uppercase text-[#86efac]">
					<SparklesIcon size={ 14 } />
					{ pro
						? __( 'Unlock premium features', 'ultimate-post-kit' )
						: sprintf(
								/* translators: %s: plugin version */
								__( 'New in %s', 'ultimate-post-kit' ),
								config.version
						  ) }
				</div>
				<div className="text-[17px] font-bold mt-[10px] tracking-[-.01em]">
					{ pro
						? __( 'Create your sleek website with Ultimate Post Kit Pro', 'ultimate-post-kit' )
						: __( 'Fresh widgets & fixes in the latest release', 'ultimate-post-kit' ) }
				</div>
				<p className="text-[12.5px] text-white/70 mt-2 mb-4 leading-[1.55]">
					{ pro
						? __( 'Every Pro widget, Template Builder, Dynamic Content, custom code and white label.', 'ultimate-post-kit' )
						: __( 'See everything added, fixed and improved in this update.', 'ultimate-post-kit' ) }
				</p>
				{ pro ? (
					<Link to="/get-pro" className="inline-block bg-gradient-brand text-white py-2 px-[15px] rounded-full text-[12.5px] font-bold shadow-glow">
						{ __( 'See what’s in Pro', 'ultimate-post-kit' ) }
					</Link>
				) : (
					<a href={ config.whatsNewUrl } target="_blank" rel="noreferrer" className="inline-block bg-white/[.12] border border-white/[.18] text-white py-2 px-[15px] rounded-full text-[12.5px] font-semibold transition-colors hover:bg-white/20">
						{ __( 'View changelog', 'ultimate-post-kit' ) }
					</a>
				) }
			</div>
		</div>
	);
}
