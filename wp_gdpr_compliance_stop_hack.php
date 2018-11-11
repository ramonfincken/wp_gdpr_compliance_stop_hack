<?php
/**
* Description: Stops update_options calls from wp-gdpr-compliance version 1.4.2 and lower. Install as mu-plugin if you are managing MANY sites or if you are an ISP/webhost. Best is to upgrade to 1.4.3 immediately.
*/

/**
* Stops update_options calls from wp-gdpr-compliance version 1.4.2 and lower
*/
function wp_gdpr_compliance_stop_hack( $new_value, $old_value ) {
	
	if ( ! function_exists( 'get_plugins' ) ) {
        	require_once ABSPATH . 'wp-admin/includes/plugin.php';
    	}
	
	$plugins = get_plugins();
	// Walk
	if( is_array( $plugins ) && count( $plugins ) > 0 ) {
		// Suspect
		if( isset( $plugins['wp-gdpr-compliance/wp-gdpr-compliance.php'] ) ) {
			// Version 1.4.2 and lower
			if ( ( version_compare( '1.4.2', $plugins['wp-gdpr-compliance/wp-gdpr-compliance.php']['Version'], '<=' ) ) ) {
				$tracing = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 4 );

				if( is_array( $tracing ) && count( $tracing ) == 4 ) {
					// Hit, block request NOW
					if( $tracing[3]['function'] == 'update_option' && substr_count( $tracing[3]['file'], 'wp-gdpr-compliance/Includes/Ajax.php' ) ) {
						wp_die( 'die' ); 
					}
				}				
			}
		}
	}

	return $new_value;
}
if( defined( 'DOING_AJAX' ) ) {
	add_filter( 'pre_update_option', 'wp_gdpr_compliance_stop_hack', 10, 2 );
}
