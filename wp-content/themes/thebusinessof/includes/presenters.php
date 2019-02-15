<?php

function the_presenters($args) {
	$args = wp_parse_args($args,array(
		'count' => 5,
		'template' => null,
		'per_row' => 2,
		'link' => '#'
	));
	$posts = get_posts("post_type=person&orderby=rand&taxonomy=role&term=presenter&numberposts={$args['count']}");
	foreach($posts as $i => $post) {
		$thumbnail_id = get_post_thumbnail_id($post->ID);
		$position = 1===(($i+1)%($args['per_row']))
			? 'left'
			:'right';
		$presenter = (object)array(
			'link' => get_permalink($post->ID),
			'thumb_img' => wp_get_attachment_image($thumbnail_id, '110x110'),
			'thumb_name' => esc_attr($post->post_title),
			'position' =>	$position,
			);
		if (empty($thumb_img))
			$thumb_img = '<img src="' . site_url('/wp-includes/images/blank.gif') .'" width="110" height="110" />';
		if (!$args['template']) {
			$class= ! empty( $position )
				? " class=\"{$position}\""
				: '';
			echo "<li$class><a href=\"{$args['link']}\">{$post->post_title}</a></li>";
		} else {
			$template_file = dirname(__FILE__) . "/../templates/{$args['template']}.php";
			include($template_file);
		}
	}
}
	/*
	<div class="wp-caption alignleft">
	<a href="$url"><img src="$thumb_src" alt="$thumb_name" width="$thumb_width" height="$thumb_height"></a>
	<p class="wp-caption-text">$thumb_name</p>
	</div>
	*/
