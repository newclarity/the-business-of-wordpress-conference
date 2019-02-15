<?php

add_shortcode('slideshow', 'tbow_slideshow_widget');

function tbow_slideshow_widget($args) {
	$default = array(
		'id' =>   'slideshow',
		'tags' => 'slideshow',
		'width' => '256',
		'height' => '192',
		'wait' => '5000',
		'speed' => '750',
	);
	$args = (!is_array($args) ? $default : array_merge($default,$args));
	extract($args);
	$args['media_tags'] = $tags;
	$args['call_source'] = 'shortcode';
	$args['orderby'] = 'rand';
	//$args['return_type'] = 'li';
	if (is_array($attachments = get_attachments_by_media_tags($args))) {
		$images = array();
		foreach($attachments as $attachment) {
			$image = (object)image_get_intermediate_size($attachment->ID,'screenshot-image');
			$link = 'http://' . get_post_meta($attachment->ID, '_wp_attachment_image_alt',true);
			$callout_width = $image->width - 20;
			$images[] =<<<IMAGE
<li class="$id-image" id="$id-image-{$attachment->ID}">
<a target="_blank" href="$link"><img src="{$image->url}" height="{$image->height}" width="{$image->width}" title="{$attachment->post_title}" /></a>
<div class="info" style="width:{$callout_width}px;">
<h3><a target="_blank" href="$link">{$attachment->post_title}</a></h3>
<p style="width:{$callout_width}px;">{$attachment->post_content}</p>
</div>
<div class="callout" style="width:{$callout_width}px"></div>
</li>
IMAGE;
		}
		$images = implode("\n",$images);
	}
	$html =<<<HTML
<div id="$id" class="slideshow">
<ul>
$images
</ul>
</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#slideshow').jCarouselLite({
		 visible: 1,
		 auto: $wait,
     speed: $speed
	});
});
</script>
HTML;
	return $html;
}

add_action('init', 'init_slideshow');
function init_slideshow(){
	if(is_admin()) return;  //so that we're not loading unecessary scripts in the admin
	wp_enqueue_script('jquery');
	$theme_url = get_stylesheet_directory_uri();
	wp_enqueue_script('jcarousellite', "$theme_url/js/jcarousellite/jcarousellite_1.0.1.js", array('jquery'), '1.0.1');
}