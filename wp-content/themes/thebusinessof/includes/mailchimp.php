<?php

EventPressMailChimpWidget::on_load();

class EventPressMailChimpWidget extends WP_Widget {
	static function on_load() {
		register_widget('EventPressMailChimpWidget');
		add_action('wp_enqueue_scripts',array(__CLASS__,'_wp_enqueue_scripts'));
	}

	static function _wp_enqueue_scripts() {
		wp_enqueue_script('jquery');
		$theme_url = get_stylesheet_directory_uri();
		wp_enqueue_script('jquery-validate',"$theme_url/js/jquery.validate.js",array('jquery'),'1.5.1',false);
		wp_enqueue_script('jquery-form',false,array('jquery','jquery-validate'),'2.02',false);
		wp_enqueue_script('mailchimp','http://gmail.us1.list-manage.com/subscribe/xs-js?u=fbead6e2fd7427762675b83ff&amp;id=50bc413ba0',
			array('jquery','jquery-validate','jquery-form'));
	}

	function __construct() {
		parent::__construct(false, $name = 'MailChimp for EventPress');
	}

	function form($instance) {
		$id = $this->get_field_id('title');
		$label = __('Title:');
		$value = esc_attr($instance['title']);
		if (empty($value))
			$value = 'Join Our Mailing List';
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
	function widget($args, $instance) {
		$theme_url = get_stylesheet_directory_uri();
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		if ( $title )
			$title = "<h3 class=\"title\">$before_title$title$after_title</h3>";
$html =<<<HTML
$before_widget
<div id="box-mailing-list">
	<div class="inner">
		$title
		<!-- Begin MailChimp Signup Form -->
			<!--[if IE]>
			<style type="text/css" media="screen">
			#mc_embed_signup fieldset {position: relative;}
			#mc_embed_signup legend {position: absolute; top: -1em; left: .2em;}
			</style>
			<![endif]-->
			<!--[if IE 7]>
			<style type="text/css" media="screen">
			.mc-field-group {overflow:visible;}
			</style>
			<![endif]-->
			<div id="mc_embed_signup">
				<form target="_blank" class="validate" name="mc-embedded-subscribe-form" id="mc-embedded-subscribe-form" method="post" action="http://gmail.us1.list-manage.com/subscribe/post?u=fbead6e2fd7427762675b83ff&amp;id=50bc413ba0">
						<div class="mc-field-group">
							<input type="text" id="mce-EMAIL" class="required email" name="EMAIL" value="Enter email address" alt="Enter email address" >
						</div>
					<div class="mc-end-group">
						<input type="image" src="$theme_url/images/btn-go.gif" name="subscribe" value="Subscribe" class="btn-image" id="mc-embedded-subscribe">
					</div>
					<div id="mce-responses">
						<div style="display: none;" id="mce-error-response" class="response"></div>
						<div style="display: none;" id="mce-success-response" class="response"></div>
					</div>
				</form>
			</div>
		<!--End mc_embed_signup-->
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
	var defaultSearchValue = jQuery('#mce-EMAIL').val();
	$('#mce-EMAIL').click(function() {
		if( this.value == defaultSearchValue ) {
			$(this).val("");
		}
	});
});
</script>
HTML;
		echo "$html$after_widget";
	}

}
