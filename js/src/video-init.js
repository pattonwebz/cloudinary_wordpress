( function() {
	if ( ! cldVideos ) {
		return;
	}

	cldVideos = JSON.parse( cldVideos );

	for ( var videoInstance in cldVideos ) {
		var cldConfig = cldVideos[ videoInstance ];
		var cldId = 'cloudinary-video-' + videoInstance;
		cld.videoPlayer( cldId, cldConfig );
	}

	window.addEventListener( 'load', function() {
		for ( var videoInstance in cldVideos ) {
			var cldId = 'cloudinary-video-' + videoInstance;
			var videoContainer = document.getElementById( cldId );
			var videoElement = videoContainer.getElementsByTagName( 'video' );

			if ( videoElement.length === 1 ) {
				videoElement = videoElement[0];
				videoElement.style.width = '100%';
				if (
					videoFreeForm &&
					videoElement.src.indexOf( videoFreeForm ) === -1 &&
					! cldVideos[videoInstance]['overwrite_transformations']
				) {
					videoElement.src = videoElement.src.replace(
						'upload/',
						'upload/' + videoFreeForm + '/'
					);
				}
			}
		}
	} );
} )();


