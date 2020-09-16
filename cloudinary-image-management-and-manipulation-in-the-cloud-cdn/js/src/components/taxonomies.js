const toggler = document.querySelector( '.cloudinary-collapsible__toggle' );

if ( toggler ) {
  toggler.addEventListener( 'click', function () {
    const content = document.querySelector( '.cloudinary-collapsible__content' );
    const isHidden = window.getComputedStyle( content, null ).getPropertyValue( 'display' ) === 'none';
    const arrowIcon = document.querySelector( '.cloudinary-collapsible__toggle button i' );
  
    content.style.display = isHidden ? 'block' : 'none'
  
    if ( arrowIcon.classList.contains( 'dashicons-arrow-down-alt2' ) ) {
      arrowIcon.classList.replace( 'dashicons-arrow-down-alt2', 'dashicons-arrow-up-alt2' );
    } else {
      arrowIcon.classList.replace( 'dashicons-arrow-up-alt2', 'dashicons-arrow-down-alt2' );
    }
  } );
}