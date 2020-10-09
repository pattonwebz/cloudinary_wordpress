export const publicIdFromUrl = ( url ) => {
	const halved = url.split( 'upload/' )[ 1 ];
	const parts = halved.split( '/' );

	parts.shift();

	return parts.join( '/' ).split( '.' )[ 0 ];
};

const dec2hex = ( dec ) => {
	return dec < 10 ?
		'0' + String( dec ) :
		dec.toString( 16 );
};

export const generateId = ( len ) => {
	const arr = new Uint8Array( ( len || 40 ) / 2 );
	window.crypto.getRandomValues( arr );
	return Array.from( arr, dec2hex ).join( '' );
};
