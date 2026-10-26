/**
 * Rounded progress track from the mockup (health strip, checklist, license
 * activations). Width and fill are data-driven, so both go through inline
 * styles rather than Tailwind classes.
 */
const DEFAULT_FILL = 'linear-gradient(90deg,#60df54,#08c972)';

export default function ProgressBar( {
	percent = 0,
	fill = DEFAULT_FILL,
	height = 7,
	className = '',
} ) {
	const clamped = Math.max( 0, Math.min( 100, Number( percent ) || 0 ) );

	return (
		<div
			className={ `rounded-full bg-[#eef0f3] dark:bg-ink-subtle overflow-hidden ${ className }` }
			style={ { height: `${ height }px` } }
		>
			<div
				className="h-full rounded-full transition-[width] duration-500 ease-upk"
				style={ { width: `${ clamped }%`, background: fill } }
			/>
		</div>
	);
}
