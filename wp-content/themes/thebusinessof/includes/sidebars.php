<?php

if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name'=>'Home - Sessions',
		'id'=>'home-sessions',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	));

	register_sidebar(array(
		'name'=>'Home - Why Attend',
		'id'=>'home-why-attend',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));


}
add_filter('dynamic_sidebar_params','home_sessions_widget_callback');
function home_sessions_widget_callback($args) {
	if ($args[0]['id']=='primary-widget-area') {
		$args[0]['before_title'] = '<h2 class="widget-title">';
		$args[0]['after_title'] = '</h2>';
	}
	if ($args[0]['id']=='home-sessions') {
		$args[0]['before_title'] = '<h3>';
		$args[0]['after_title'] = '</h3>';
	}
	return $args;
}