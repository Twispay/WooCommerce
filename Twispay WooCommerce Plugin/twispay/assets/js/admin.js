// Document ready statement.
jQuery( document ).ready( function( $ ) {
    // Hiding and showing on start LIVE/STAGING INPUTS
    if ( $( '#live_mode' ).val() == '1' ) {
        $( '#live_site_id' ).fadeIn();
        $( '#live_private_key' ).fadeIn();
    }
    else if ( $( '#live_mode' ).val() == '0' ) {
        $( '#staging_site_id' ).fadeIn();
        $( '#staging_private_key' ).fadeIn();
    }
    
    // Trigger for hiding and showing LIVE/STAGING INPUTS
    $( '#live_mode' ).on( 'change', function() {
        if ( this.value == '1' ) {
            $( '#staging_site_id' ).fadeOut(0);
            $( '#staging_private_key' ).fadeOut(0);
            
            $( '#live_site_id' ).fadeIn(0);
            $( '#live_private_key' ).fadeIn(0);
        }
        else if ( this.value == '0' ) {
            $( '#live_site_id' ).fadeOut(0);
            $( '#live_private_key' ).fadeOut(0);
            
            $( '#staging_site_id' ).fadeIn(0);
            $( '#staging_private_key' ).fadeIn(0);
        }
    } );
} );