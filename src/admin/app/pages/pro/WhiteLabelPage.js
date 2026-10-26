/**
 * White Label (Ultimate Post Kit Pro), laid out like Element Pack's White Label
 * page. `data` comes from Pro through the `ultimate_post_kit/admin/react_pages`
 * filter; without it the route shows the PHP panel instead.
 */
import { __ } from '@wordpress/i18n';
import PageHeader from '../../components/shell/PageHeader';
import WhiteLabelPanel from './white-label/WhiteLabelPanel';

export default function WhiteLabelPage( { data } ) {
	return (
		<div>
			<PageHeader
				title={ __( 'White Label', 'ultimate-post-kit' ) }
				subtitle={ __(
					'Rebrand Ultimate Post Kit across the admin for your clients.',
					'ultimate-post-kit'
				) }
			/>
			<WhiteLabelPanel data={ data || {} } />
		</div>
	);
}
