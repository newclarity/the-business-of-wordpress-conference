<?php

add_shortcode('eventbrite', 'eventbrite_show_widget');

function eventbrite_show_widget($args) {
	$valid_types = array('ticket-form',);
	$div_style = 'border:1px solid black;color:white;background:red;padding:10px;';
	$default = array(
		'type' => 'ticket-form',
		'url' => '',
		'src' => '',
		'width' => '100%',
		'height' => '500',
	);
	$args = array_merge($default,$args);
	extract($args);
	if (empty($url) && empty($src)) {
		$html =<<<HTML
<div style="$div_style">
<p>The "eventbrite" shortcode much have an attribute of either "<b><i>src</i></b>" or "<b><i>url</i></b>", i.e.:</p>
<ul>
<li>[eventbrite type="ticket-form" <b><i>src</i></b>="http://www.eventbrite.com/tickets-external?eid=582216425&ref=etckt"]</li>
<li>[eventbrite type="ticket-form" <b><i>url</i></b>="http://www.eventbrite.com/tickets-external?eid=582216425&ref=etckt"]</li>
</ul>
</div>
HTML;
	} else if (!empty($url) && !empty($src)) {
		$html =<<<HTML
<div style="$div_style">
You should only the "<b><i>src</i></b>" attribute or the "<b><i>url</i></b>" attribute when using the "eventbrite" shortcode.
</div>
HTML;
	} else if (!in_array($args['type'],$valid_types)) {
		$valid_types = implode('</b></i>"</li><li>"<i><b>',$valid_types);
		$html =<<<HTML
<div style="$div_style">
<p>When using the "eventbrite" shortcode you must specifiy an attribute of "<b><i>type</i></b>" with one of the following valid values:</p>
<ul><li>"<i><b>$valid_types</b></i>"</li></ul>
<p>i.e.:</p>
<ul>
<li>[eventbrite <b><i>type</i></b>="<b><i>ticket-form</i></b>" src="$url$src"]</li>
</ul>
</div>
HTML;
	} else  {
	$html = <<<HTML
<div id="eventbrite">
	<iframe src="$src$url" width="$width" height="$height" allowtransparency="true" scrolling="auto"></iframe>
</div>
HTML;
	}
	return $html;
}

