<?php

function the_sessions() {
	$pages = get_posts('post_type=session&orderby=rand&numberposts=999');
	shuffle($pages);
	$page_count = 1;
	foreach($pages as $page) {
		$url = get_permalink($page->ID);
		if (strpos($url,'/demos') || strpos($url,'/exhibits') || strpos($url,'/qa-') || strpos($url,'/lunch') || strpos($url,'/roundtable-')) {
		} else {
			echo '<tr><td>';
			echo "<a href=\"$url\">{$page->post_title}</a>";
			echo '</td></tr>';
			if ($page_count++ == 7)
				break;
		}
	}
}
