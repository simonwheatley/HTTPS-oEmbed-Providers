<?php

/*
Plugin Name: HTTPS oEmbed Providers
Plugin URI: http://wordpress.org/extend/plugins/https-oembed-providers/
Description: Fix for WordPress oEmbeds for various services which now provide https URLs, these aren't recognised by the WP 3.4.2 list of oEmbed providers. Significant thanks to John James Jacoby for the code in his patch.
Version: 1.1
Author: Simon Wheatley (Code for the People), 
*/
 
/*  Copyright 2012 Simon Wheatley

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

/**
 * The version we predict WordPress core will have committed the
 * https fixes for oEmbed providers.
 * 
 * @see http://core.trac.wordpress.org/ticket/20102
 * 
 * @const string
 */
define( 'HOP_PREDICTED_OBSOLESENCE_VERSION', '3.5' );

/**
 * Hooks the WP admin_notices and network_admin_notices to warn users
 * when the version of WordPress passes the version where oEmbed 
 * Providers are predicted to have been fixed.
 * 
 * @return void
 */
function hop_admin_notices() {
	include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version
	if ( version_compare( $wp_version, HOP_PREDICTED_OBSOLESENCE_VERSION, '<=' ) )
		return;
	printf( '<div class="error"><p>Now you’ve upgraded to WordPress %s, please try deactivating the "HTTPS oEmbed Providers" plugin and see if your embedded media still work with <var>https</var> URLs.</p></div>', HOP_PREDICTED_OBSOLESENCE_VERSION );
}
add_action( 'network_admin_notices', 'hop_admin_notices' );
add_action( 'admin_notices', 'hop_admin_notices' );

/**
 * Hot fix for WordPress oEmbeds for various services which now
 * provide https URLs, these aren't recognised by the WP 3.4.2
 * list of oEmbed providers.
 * 
 * @param array $providers An array of regex => oEmbed provider URLs
 * @return array An array of regex => oEmbed provider URLs
 */
function hop_oembed_providers( $providers ) {
	// Don't replace the $providers array, instead merge in on top of it
	$providers = array_merge( $providers, array(
			'#https?://(www\.)?youtube.com/watch.*#i'            => array( 'http://www.youtube.com/oembed',                     true  ),
			'http://youtu.be/*'                                  => array( 'http://www.youtube.com/oembed',                     false ),
			'http://blip.tv/*'                                   => array( 'http://blip.tv/oembed/',                            false ),
			'#https?://(www\.)?vimeo\.com/.*#i'                  => array( 'http://vimeo.com/api/oembed.{format}',              true  ),
			'#https?://(www\.)?dailymotion\.com/.*#i'            => array( 'http://www.dailymotion.com/services/oembed',        true  ),
			'#https?://(www\.)?flickr\.com/.*#i'                 => array( 'http://www.flickr.com/services/oembed/',            true  ),
			'#https?://(.+\.)?smugmug\.com/.*#i'                 => array( 'http://api.smugmug.com/services/oembed/',           true  ),
			'#https?://(www\.)?hulu\.com/watch/.*#i'             => array( 'http://www.hulu.com/api/oembed.{format}',           true  ),
			'#https?://(www\.)?viddler\.com/.*#i'                => array( 'http://lab.viddler.com/services/oembed/',           true  ),
			'http://qik.com/*'                                   => array( 'http://qik.com/api/oembed.{format}',                false ),
			'http://revision3.com/*'                             => array( 'http://revision3.com/api/oembed/',                  false ),
			'http://i*.photobucket.com/albums/*'                 => array( 'http://photobucket.com/oembed',                     false ),
			'http://gi*.photobucket.com/groups/*'                => array( 'http://photobucket.com/oembed',                     false ),
			'#https?://(www\.)?scribd\.com/.*#i'                 => array( 'http://www.scribd.com/services/oembed',             true  ),
			'http://wordpress.tv/*'                              => array( 'http://wordpress.tv/oembed/',                       false ),
			'#https?://(.+\.)?polldaddy\.com/.*#i'               => array( 'http://polldaddy.com/oembed/',                      true  ),
			'#https?://(www\.)?funnyordie\.com/videos/.*#i'      => array( 'http://www.funnyordie.com/oembed',                  true  ),
			'#https?://(www\.)?twitter.com/.+?/status(es)?/.*#i' => array( 'http://api.twitter.com/1/statuses/oembed.{format}', true  ),
		) );
	return $providers;
}
add_filter( 'oembed_providers', 'hop_oembed_providers' );
