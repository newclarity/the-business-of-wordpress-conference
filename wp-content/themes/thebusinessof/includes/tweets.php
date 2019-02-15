<?php

function the_tweets($args=array()) {
	$elitwee_cache = get_option('elitwee_cache');
	$elitwee_twitter = get_option('elitwee_twitter');
	$theme_dir = dirname(__FILE__)."/..";
	//echo $theme_dir; exit;
	$theme_url = dirname(get_stylesheet_uri());
	$default = array(
		'user' => $elitwee_twitter['user'],
		'password' => $elitwee_twitter['pass'],
		'count' => 3,
		'template' => "$theme_dir/templates/tweet.php",
	);
	$args = array_merge($default,$args);
	extract($args);
	//echo $template; exit;
	if (!file_exists($args['template'])) {
		$args['template'] = "$theme_dir/templates/tweet.php";
	}
	try {
		$et = new Elitwee($args['user'], $args['password'], 3);
		$et->set_cache_location($elitwee_cache['location']);
		$et->set_cache_time($elitwee_cache['life']);
		$et->set_user_timeline_format('json');
		$tweets = $et->get_user_timeline();
		$filename = array_pop(explode('/',$args['template']));
		$tweet_no = 1;
		foreach ($tweets as $tweet) {
			$screen_name = $tweet->user->screen_name;
			$tweet_id = $tweet->id;
			$tweet_url = "http://twitter.com/$user/statuses/{$tweet->id}";
			$since_tweet = relative_time(strtotime($tweet->created_at),'');
			$tweet_text = str_replace('<a href=','<a target="_blank" href=',$et->format($tweet->text));
			require($args['template']);
			if ($tweet_no++ > $args['count'])
				break;
		}
	}
	catch (Exception $e) {
		$screen_name = $user;
		$tweet_id = 0;
		$tweet_url = "http://twitter.com/$user";
		$since_tweet = relative_time(time(),'');
		$tweet_text = 'Twitter.com is currently unavailable.';
		require($args['template']);
	}
}
