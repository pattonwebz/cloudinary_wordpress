/**
 * External dependencies
 */
import classNames from 'classnames';

export default ( { name, children, icon, onChange, current } ) => {
	return (
		<button onClick={ () => onChange( name ) } className={ classNames( 'radio-select', { 'radio-select--active': current === name } ) }>
			{ icon }
			<div className="radio-select__label">{ children }</div>
		</button>
	);
};
