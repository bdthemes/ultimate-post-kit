/**
 * Ultimate Post Kit admin dashboard entry point.
 *
 * Mounts the React root into the PHP-rendered #upk-admin-app node.
 */
import { createRoot } from '@wordpress/element';
import App from './app';
import './styles/index.scss';

const node = document.getElementById( 'upk-admin-app' );

if ( node ) {
	createRoot( node ).render( <App /> );
}
