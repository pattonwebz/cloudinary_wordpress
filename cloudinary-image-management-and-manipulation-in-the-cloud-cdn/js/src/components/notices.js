/* global window wp  */
const Notices = {
    _init: function() {
        let self = this;
        if ( typeof CLDIS !== 'undefined' ) {
            let triggers = document.getElementsByClassName( 'cld-notice' );
            [ ...triggers ].forEach( ( trigger ) => {
                trigger.addEventListener( 'click', ( ev ) => {
                    // WordPress has an onClick that cannot be unbound.
                    // So, we have the click on our Notice, and act on the
                    // button as a target.
                    if ( 'notice-dismiss' === ev.target.className ) {
                        self._dismiss( trigger );
                    }
                } );
            } );
        }
    },
    _dismiss: function( trigger ) {
        let token    = trigger.dataset.dismiss;
        let duration = trigger.dataset.duration;
        wp.ajax.send( {
            url  : CLDIS.url,
            data : {
                token    : token,
                duration : duration,
                _wpnonce : CLDIS.nonce
            }
        } );
    },
};

export default Notices;

// Init.
window.addEventListener( 'load', Notices._init() );
