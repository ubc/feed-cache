<?php
/*
Plugin Name: Feed Cache
Plugin URI: https://github.com/ubc/feed-cache
Description: This plugin allows administrator of a WordPress site to set the RSS feed cache duration.
Version: 0.1
Author: CTLT
Author URI: http://ctlt.ubc.ca
License: GPLv2
*/

class FeedCache {

	function __construct() {
		add_action('admin_init', array( __CLASS__, 'feed_cache_init' ));
		add_filter( 'wp_feed_cache_transient_lifetime', array( __CLASS__, 'feed_cache_filter' ));
	}

	function feed_cache_filter($time) {

		$feed_cache_options = get_option( 'feed_cache' );
		$cache_in_sec = $feed_cache_options['duration'] * 60;

		if ( is_null($cache_in_sec) ) {
			$cache_in_sec = 300;
			update_option( 'feed_cache', 5 );
		}
		
		if($cache_in_sec > 300) {
			return 300;
		}
		else {
			return $cache_in_sec;
		}
	}

	function feed_cache_init(){
		register_setting(
			'general',                 // settings page
			'feed_cache',          // option name
			array( __CLASS__, 'feed_cache_validate_options')  // validation callback
		);

		add_settings_field(
			'feed_cache_add_duration',      // id
			'Feed Cache Duration',              // setting title
			array( __CLASS__, 'feed_cache_setting_input'),    // display callback
			'general',                 // settings page
			'default'                  // settings section
		);

	}

	// Display and fill the form field
	function feed_cache_setting_input() {
		// get option 'duration' value from the database
		$options = get_option( 'feed_cache' );
		$value = $options['duration'];

		// echo the field
		echo "<input id='duration' name='feed_cache[duration]'
 type='text' value='".esc_attr( $value )."' size='2' /> minutes";
 		echo "<p class='description'>The default and maximum value of feed cache duration is 5 minutes.</p>";

	}



	function feed_cache_validate_options( $input ) {
		$valid = array();
		$valid['duration'] = $input['duration'];

		// Something dirty entered? Warn user.
		if( !is_numeric( $valid['duration'] ) ) {
			add_settings_error(
				'feed_cache_duration',           // setting title
				'feed_cache_texterror',            // error ID
				'The Feed Cache is not a number!',   // error message
				'error'                        // type of message
			);
		}
		if( $valid['duration'] > 5 ) {
			add_settings_error(
				'feed_cache_duration',           // setting title
				'feed_cache_texterror',            // error ID
				'The feed cache cannot be more than 5 minutes!',   // error message
				'error'                        // type of message
			);

		}
		return $valid;
	}
}

$feedcache = new FeedCache();