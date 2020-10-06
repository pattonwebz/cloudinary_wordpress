export const publicIdFromUrl = ( url ) => {
	const halved = url.split( 'upload/' )[ 1 ];
	const parts = halved.split( '/' );

	parts.shift();

	return parts.join( '/' ).split( '.' )[ 0 ];
};
