/**
 * Tailwind config for the Ultimate Post Kit React admin dashboard (src/admin/app).
 *
 * - `important: '#upk-admin-app'` scopes every utility under the app's mount
 *   node so nothing leaks into the rest of wp-admin. Utilities placed on the
 *   mount node itself will not match; the app renders an inner `.upk-root`
 *   wrapper that carries the root-level utilities.
 * - Preflight is disabled (a global reset would restyle wp-admin); a scoped
 *   mini-reset lives in src/admin/app/styles/index.scss.
 * - Layout and design tokens follow the Element Pack dashboard; the accent is
 *   Ultimate Post Kit's own gradient.
 */
module.exports = {
	content: [ './src/admin/app/**/*.js' ],
	important: '#upk-admin-app',
	corePlugins: { preflight: false },
	darkMode: [ 'selector', '[data-upk-theme="dark"]' ],
	theme: {
		extend: {
			colors: {
				brand: {
					DEFAULT: '#00a85c',
					pink: '#60df54',
					soft: '#e8f9ef',
				},
				ink: {
					page: '#0b0e13',
					card: '#161a21',
					subtle: '#12161c',
					border: '#262c36',
					text: '#edeef0',
					muted: '#9aa0a8',
				},
			},
			fontFamily: {
				sans: [
					'"IBM Plex Sans"',
					'system-ui',
					'-apple-system',
					'BlinkMacSystemFont',
					'"Segoe UI"',
					'Roboto',
					'sans-serif',
				],
			},
			borderRadius: {
				btn: '10px',
				tile: '11px',
				panel: '16px',
				card: '18px',
				sheet: '22px',
			},
			boxShadow: {
				// Cards render flat (border only), as in Element Pack.
				resting: 'none',
				card: 'none',
				pop: '0 24px 60px -16px rgba(0,0,0,.32)',
				sheet: '0 30px 80px -20px rgba(0,0,0,.6)',
				glow: '0 6px 18px rgba(8,201,114,.32)',
				'glow-lg': '0 8px 24px rgba(8,201,114,.44)',
			},
			backgroundImage: {
				'gradient-brand': 'linear-gradient(135deg,#60df54,#08c972)',
			},
			transitionTimingFunction: {
				upk: 'cubic-bezier(.16,1,.3,1)',
			},
			keyframes: {
				upkPop: {
					from: { opacity: '0', transform: 'translateY(6px) scale(.98)' },
					to: { opacity: '1', transform: 'none' },
				},
				upkFadeUp: {
					from: { opacity: '0', transform: 'translateY(10px)' },
					to: { opacity: '1', transform: 'none' },
				},
				upkSpin: {
					to: { transform: 'rotate(360deg)' },
				},
				upkShimmer: {
					from: { backgroundPosition: '200% 0' },
					to: { backgroundPosition: '-200% 0' },
				},
			},
			animation: {
				'upk-pop': 'upkPop .25s cubic-bezier(.16,1,.3,1) both',
				'upk-fade-up': 'upkFadeUp .34s cubic-bezier(.16,1,.3,1) both',
				'upk-spin': 'upkSpin .8s linear infinite',
				'upk-shimmer': 'upkShimmer 1.6s linear infinite',
			},
		},
	},
};
