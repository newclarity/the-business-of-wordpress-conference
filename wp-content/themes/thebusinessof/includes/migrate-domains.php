<?php

add_action('migrate_webhosts', 'tboc_migrate_webhosts');
function tboc_migrate_webhosts($old,$new) {
	$header = (array)get_option('mods_Twenty Ten',array('header_image'=>''));
	$header['header_image'] = preg_replace("#http://$old/(.*)#","http://$new/$1",$header['header_image']);
	update_option('mods_Twenty Ten',$header);

	$header = (array)get_option('mods_The Business of',array('header_image'=>''));
	$header['header_image'] = preg_replace("#http://$old/(.*)#","http://$new/$1",$header['header_image']);
	update_option('mods_The Business of',$header);
	return;
}

if ( function_exists('register_webhost_migrator') ) {
	register_webhost_migrator( 'wpGoogleMaps' );
	add_action( 'migrate_webhosts', 'update_googlemaps_apikey_on_webhost_migrate' );
	function update_googlemaps_apikey_on_webhost_migrate( $old, $new ) {
		migrate_webhost_option( 'wpGoogleMaps_api_key', 'googlemaps_apikey' );
	}
}
add_action('migrate_webhosts','update_elitwee_cache_on_webhost_migrate');
function update_elitwee_cache_on_webhost_migrate($old,$new) {
	$current = get_current_webhost();
	$current['elitwee_cache_fullpath'] = (substr($current['elitwee_cache'],0,2)=='~/' ?
		$current['rootdir'] . substr($current['elitwee_cache'],1) . '/' :
		$current['elitwee_cache']);
	set_current_webhost($current);
	migrate_webhost_option('elitwee_cache','elitwee_cache_fullpath','location');
}
