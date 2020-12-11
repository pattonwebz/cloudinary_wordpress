/* global */

const UI = {
    _init() {
        const conditions = document.querySelectorAll( '[data-bind]' );
        const inputs = document.querySelectorAll( '[data-bound]' );
        const self = this;
        conditions.forEach( self._bind );
        inputs.forEach( self._trigger );

    },
    _bind( element ) {
        const condition = JSON.parse( element.dataset.condition );
        const input = document.querySelector( 'input[data-bound="' + element.dataset.bind + '"]' );
        input.addEventListener( 'change', function() {
            const id = input.id;
            let check = false;
            if ( input.type === 'checkbox' || input.type === 'radio' ) {
                check = input.checked === condition[ id ];
            }
            else {
                check = input.value === condition[ id ];
            }

            if ( true === check ) {
                element.style.display = '';
            }
            else {
                element.style.display = 'none';
            }

        } );
    },
    _trigger( input ) {
        input.dispatchEvent( new Event( 'change' ) );
    }
};
// Init.
window.addEventListener( 'load', UI._init() );

export default UI;
