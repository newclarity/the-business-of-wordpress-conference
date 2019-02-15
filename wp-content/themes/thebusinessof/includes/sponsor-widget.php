<?php

register_widget('EventPressSponsorWidget');

class EventPressSponsorWidget extends WP_Widget {
	function __construct() {
		 parent::__construct(false, $name = 'Sponsors');
	}

	function form($instance) {
		$id = $this->get_field_id('title');
		$label = __('Title:');
		$value = esc_attr($instance['title']);
		$name = $this->get_field_name('title');
		$html =<<<HTML
<p>
<label for="$id">$label</label>
<input type="text" class="widefat" id="$id" name="$name" value="$value" />
</p>
HTML;
		echo $html;
	}
	function update($new_instance, $old_instance) {
		 return $new_instance;
	}
	function show_sponsor_list($title,$type) {
		echo '<h3 class="' . $type.'-sponsor-header sponsor-header">'.$title.'</h3>';
		$base_q = 'showposts=50&post_type=company&order=ASC&orderby=menu_order&taxonomy=partner-level&term=sponsor';
		$q = "{$base_q}&taxonomy=partner-type&term=$type";
		$q = new WP_Query($q);
		$rows = array();
		$rows[] = '<div id="eventpress-widget-sponsor-list">';
		foreach ($q->posts as $post) {
			$GLOBALS['post'] = $post;
			ob_start();
			echo self::get_sponsor_image_and_title($post);
			$rows[] = ob_get_clean();
		}
		$rows[] = '</div>';
		echo implode("\n",$rows);
	}

	function widget($args, $instance) {
		$save_post = $GLOBALS['post'];
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		$this->show_sponsor_list('Gold Sponsor','gold');
		$this->show_sponsor_list('Silver Sponsors','silver');
		$this->show_sponsor_list('Media Sponsors','media');
		echo $after_widget;
	}

	function get_sponsor_image_and_title($post) {
		//$permalink = str_replace('/companies/','/sponsors/',get_permalink($post));
		$permalink = get_post_permalink($post);
		if (has_post_thumbnail( $post->ID )) {
			$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
			list($src,$width,$height) = wp_get_attachment_image_src($post_thumbnail_id, '225x225');
			$post_thumbnail = get_post($post_thumbnail_id);
			$img_link =<<<HTML
			<div id="sponsor-{$post_thumbnail->ID}" class="sponsor-entry">
				<a href="$permalink"  title="{$post_thumbnail->post_title}">
				<img src="$src" alt="{$post_thumbnail->post_title}"
				width="$width" height="$height" class="wp-image-$post_thumbnail_id" />
				</a>
			</div>
HTML;
		}
		return $img_link;
	}
}
