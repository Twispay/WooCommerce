// Document ready statement.
jQuery( document ).ready( function( $ ) {
    const liveMode = $( '#live_mode' );
    const liveSiteId = $( '#live_site_id' );
    const livePrivateKey = $( '#live_private_key' );
    const stagingSiteId = $( '#staging_site_id' );
    const stagingPrivateKey = $( '#staging_private_key' );

    // Hiding and showing on start LIVE/STAGING INPUTS
    if ( liveMode.val() == '1' ) {
        liveSiteId.fadeIn();
        livePrivateKey.fadeIn();
    }
    else if ( liveMode.val() == '0' ) {
        stagingSiteId.fadeIn();
        stagingPrivateKey.fadeIn();
    }

    // Trigger for hiding and showing LIVE/STAGING INPUTS
    liveMode.on( 'change', function() {
        if ( this.value == '1' ) {
            stagingSiteId.fadeOut(0);
            stagingPrivateKey.fadeOut(0);

            liveSiteId.fadeIn(0);
            livePrivateKey.fadeIn(0);
        }
        else if ( this.value == '0' ) {
            liveSiteId.fadeOut(0);
            livePrivateKey.fadeOut(0);

            stagingSiteId.fadeIn(0);
            stagingPrivateKey.fadeIn(0);
        }
    } );
} );
