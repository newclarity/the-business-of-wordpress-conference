/*
 Filename: scripts.js
 */
jQuery(document).ready(function($) {
	$('#content .post-list .wp-caption').each(function(i){
		//if (i%2==0) {
			$(this).removeClass('alignright').addClass('alignleft');
		//}
	});
	$('#content .post-list .entry-title a').each(function(i){
		var url = $(this).attr('href');
		var post_id = this.parentNode.parentNode.id;
		$('#' + post_id + ' .entry-content .wp-caption a').attr('href',url);
	});

	/* Remove the hyperlink on a post thumbnail */
	$('body.single #content .entry-content .wp-caption a img').each(function(i){
		var img = $(this);
		var parent = $(this).parent().parent();
		$(this).parent().replaceWith(img);
	});
});