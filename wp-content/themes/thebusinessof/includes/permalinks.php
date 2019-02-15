<?php

add_action('term_link', 'tboc_term_link');
function tboc_term_link($permalink) {
	$permalink = str_replace('/roles/presenter/','/presenters/',$permalink);
	$permalink = str_replace('/partner-levels/sponsor/','/sponsors/',$permalink);
	return $permalink;
}