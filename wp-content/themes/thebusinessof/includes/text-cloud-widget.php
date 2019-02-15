<?php

register_widget('TextCloudWidget');
class TextCloudWidget extends WP_Widget {
	function TextCloudWidget() {
		 parent::WP_Widget(false, $name = 'Text Cloud');
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'list' => '' , 'width' => '50' ) );

		$title_id = $this->get_field_id('title');
		$title_label = __('Title:');
		$title_name = $this->get_field_name('title');
		$title_value = esc_attr(strip_tags($instance['title']));

		$list_id = $this->get_field_id('list');
		$list_name = $this->get_field_name('list');
		$list_value = format_to_edit($instance['list']);

		$width_id = $this->get_field_id('width');
		$width_label = __('Width:');
		$width_name = $this->get_field_name('width');
		$width_value = esc_attr($instance['width']);

		$html =<<<HTML
<p>
<label for="$title_id">$title_label</label>
<input type="text" class="widefat" id="$title_id" name="$title_name" value="$title_value" />
</p>
<textarea class="widefat" rows="16" cols="20" id="$list_id" name="$list_name">$list_value</textarea>
<p>
<label for="$width_id">$width_label</label>
<input type="text" class="widefat" id="$width_id" name="$width_name" value="$width_value" size="5" />
</p>
HTML;
		echo $html;
	}
	function update($new_instance, $old_instance) {
		 return $new_instance;
	}

	function widget($args, $instance) {
		$id = sanitize_title_with_dashes($instance['title']);
		echo "<div id=\"$id\" class=\"text-cloud\">";
		echo $args['before_title'];
		echo $instance['title'];
		echo $args['after_title'];
		$list = trim($instance['list'],"\n\r");
		$items = explode("\n",$list);
		$tag = 'strong';
		$output = array();
		$line = array();
		$len = 0;
		foreach($items as $item) {
			$len += strlen($item)+3;
			if ($len<intval($instance['width']))
				$line[] = "<$tag>$item</$tag>";
			else {
				$output[] = implode(' &bull; ',$line);
				$line = array();
				$line[] = $item = "<$tag>$item</$tag>";
				$len = strlen($item);
			}
			$tag = ($tag=='em' ? 'strong' : 'em');
		}
		if ($len>0)
			$output[] = implode(' &bull; ',$line);
		$output = implode("<br />\n",$output);
		echo $output;
		echo '</div>';
	}
}


