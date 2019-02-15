<?php

add_action('the_content', 'insert_post_thumbnail_into_content');
add_action('the_excerpt', 'insert_post_thumbnail_into_content');
function insert_post_thumbnail_into_content($content) {
	global $post;
	$caption = get_post_thumbnail_with_caption($post);
	return "$caption$content";
}
function get_post_thumbnail_with_caption($post) {
	$alignment = get_value(get_post_meta($post,'Post Thumbnail Alignment'),'alignright');
	$meta = get_post_meta($post->ID,'Post Thumbnail Size',true);
	$size = '225x225'; //get_value($meta,'full');
	$permalink = get_permalink($post->ID);
	if (!has_post_thumbnail( $post->ID )) {
		$caption = '';
	} else {
		$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
		list($src,$width,$height) = wp_get_attachment_image_src($post_thumbnail_id, $size);
		$post_thumbnail = get_post($post_thumbnail_id);
		$img_link =<<<HTML
<a href="$permalink">
<img src="$src" alt="{$post_thumbnail->post_title}" title="{$post_thumbnail->post_title}"
width="$width" height="$height" class="size-$size wp-image-$post_thumbnail_id" />
</a>
HTML;
		$caption = img_caption_shortcode(array(
			'id'	    => "attachment_$post_thumbnail_id",
			'align'	  => $alignment,
			'width'	  => $width,
			'caption' => $post_thumbnail->post_excerpt,
		),$img_link);
	}
	return $caption;
}

