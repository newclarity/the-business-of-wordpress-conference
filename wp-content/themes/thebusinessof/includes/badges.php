<?php
add_shortcode('badge', 'tbow_the_badge');

function tbow_the_badge($args) {
	$slug = strtolower($args['title']);
	$title = $args['title'];
	$html=<<<HTML
<div id="$slug-badge" class="badge">
<h3>$title Badge Embed Code</h3>
<p><img src="http://www.thebusinessof.net/wordpress/wp-content/themes/thebusinessof/images/badges/thebizofwp-$slug.jpg" alt="The Business Of WordPress Conference supporter Badge" width="180" height="200" /></p>
<p><textarea cols="40" rows="10"><a href="http://www.thebusinessof.net/wordpress/" title="$title: The Business Of WordPress Conference"><img src="http://www.thebusinessof.net/wordpress/wp-content/themes/thebusinessof/images/badges/thebizofwp-$slug.jpg" width="180" height="200" alt="The Business Of WordPress Conference supporter Badge"></a></textarea></p>
</div>
<div style="clear:both;"></div>
HTML;
	return $html;
}