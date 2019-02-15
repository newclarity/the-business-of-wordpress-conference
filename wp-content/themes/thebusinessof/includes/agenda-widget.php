<?php

EventPress::initClass();
add_shortcode('eventpress-agenda', 'eventpress_agenda_widget');

function eventpress_agenda_widget($args) {
	$args = (empty($args) ? array() : $args);
	if (isset($args['date']))
		EventPress::$date = $args['date'];
	$default = array(
		'sessions' => ($sessions = EventPress::get_sessions_by_time_and_room()),
		'omit-session-col' => false,
		'date' => '',
	);
	$args = array_merge($default,$args);
	$agenda = EventPress::generate_agenda_table($args);

	return $agenda;
}
class EventPress {
	static $show_presenters = false;
	static $date;
	static function initClass() {
		self::$show_presenters = isset($_GET['presenters'])
			? $_GET['presenters']=='yes'
			: false;
		add_filter('get_posts_callback_add_taxonomy_terms',array(__CLASS__,'get_posts_callback_add_taxonomy_terms'),10,3);
	}

	static function generate_agenda_table($args) {
		$default = array(
			'table_id' => 'agenda-' . sanitize_title_with_dashes(self::$date),
			'table_class' => 'agenda',
		);
		extract(array_merge($default,$args));
		$args['caption_row'] = self::generate_agenda_caption_row($args);
		$html = array();
		$html[] = '<div id="' . $table_id . '" class="' . $table_class . '">';
		$html[] = '<table>';
		//		$html[] = '<div class="session-type-legends">';
		//		$html[] = '</div>';
		$html[] = self::generate_agenda_cols($args);
		$html[] = self::generate_agenda_head($args,'top');
		$html[] = self::generate_agenda_body($args);
		$html[] = self::generate_agenda_head($args,'bottom');
		$html[] = '</table>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
	static function generate_session_types_legend() {
		$session_types = get_terms('session-type');
		foreach($session_types as $key => $session_type) {
			$permalink = get_term_link(get_term_by('id',$session_type->term_id,'session-type'),'session-type');
			$session_types[$key] = '<div class="legend"><a href="'.$permalink.'"><span class="session-type '. $session_type->slug . '">'.$session_type->name.'</span>&nbsp;' . $session_type->name . '</div>';
		}
		$colspan = count(self::get_room_names_by_order())+3;
		$row = array('<tr class="session-type-legends"><td colspan="'.$colspan.'">');
		$row[] = implode("\n",$session_types);
		$row[] = '</td></tr>';
		return implode('',$row);
	}
	static function generate_agenda_cols($args) {
		$cols = array();
		$cols[] = "<colgroup>\n";
		$cols[] = '<col id="start-time-col" class="time-col" />';
		$cols[] = '<col id="end-time-col"   class="time-col" />';
		$cols[] = '<col id="session-name-col" />';
		foreach(current($args['sessions'])->rooms as $slug => $room) {
			$cols[] = '<col id="' . $slug . '-room-col" class="session-col"' . " />\n";
		}
		$cols[] = "</colgroup>\n";
		return implode("\n",$cols);
	}

	static function generate_agenda_head($args,$position='top') {
		$html = array();
		$html[] = '<thead>';
		if ($position=='top') {
			$html[] = self::generate_session_types_legend();
			$html[] = $args['caption_row'];
		} else {
			$html[] = $args['caption_row'];
			$html[] = self::generate_session_types_legend();
		}
		$html[] = "</thead>\n";
		return implode('',$html);
	}

	static function generate_agenda_foot($args) {
		$html = array();
		$html[] = '<tfoot>';
		$html[] = $args['caption_row'];
		$html[] = "</tfoot>\n";
		return implode('',$html);
	}

	static function generate_agenda_caption_row($args) {
		$html = array();
		$html[] = "<tr>\n";
		$html[] = '<th>Start</th><th>End</th><th>Session</th>';
		foreach(current($args['sessions'])->rooms as $room) {
			$html[] = "<th>{$room->name}</th>";
		}
		$html[] = "\n</tr>\n";
		return implode('',$html);
	}

	static function generate_agenda_body($args) {
		extract($args);
		$body = array();
		$body[] = "<tbody>\n";
		foreach($sessions as $time_slot) 
			$body[] = self::generate_agenda_body_row($args,$time_slot);
		$body[] = "</tbody>\n";
		return implode('',$body);
	}

	static function generate_agenda_body_row($args,$time_slot) {
		extract($args);
		$row = array();
		$row[] =  '<tr class="' . $time_slot->slug . '">' .
							'<th class="start-time">' . "{$time_slot->start}</th>" .
							'<th class="end-time">' . "{$time_slot->end}</th>" .
							'<th class="time-slot">' . "{$time_slot->title}</th>";
		foreach($time_slot->rooms as $room) {
			$td_class = isset($room->slug)
				? array($room->slug)
				: array(null);
			$td_class[] = (count($room->sessions)==0 ? 'no-session' : '');
			$td_class = ' class="session ' . implode(' ',$td_class) . '"';
			if ( !isset($room->rowspan) ) {
				$room->rowspan = 1;
			}
			$row_span = ($room->rowspan>1 ? ' rowspan="' . $room->rowspan . '"' : '');
			$col_span = ($room->colspan>1 ? ' colspan="' . $room->colspan . '"' : '');
			if (($room->rowspan==1 && $room->colspan>0) ||
				  ($room->colspan==1 && $room->rowspan>0) ||
				  ($room->colspan>1 && $room->rowspan>1)) {
				$row[] = "<td$td_class$row_span$col_span>";
				foreach($room->sessions as $session) {
					$row[] = '<a href="' . get_permalink($session->ID) . '">' . $session->title . "</a>\n";
					//--- Presenters ---//
					if (self::$show_presenters) {
						$plural = (count($session->presenters)==1 ? '' : 's');
						$presenters = implode(', ',element_array($session->presenters,'html'));
						$row[] = (empty($presenters) ? '' : "<div class=\"presenters\"><label>Presenter$plural:</label> $presenters</div>");
					}
					//--- Session Types ---//
					$plural = (count($session->session_types)==1 ? '' : 's');
					$session_types = implode('<span>, </span>',element_array($session->session_types,'html'));
					$row[] = (empty($session_types) ? '' : "<div class=\"session-types\"><label>Session Type$plural:</label> $session_types</div>");
				}
				$row[] = (count($room->sessions)==0 ? ' &mdash;' : '');
				$row[] = '<div class="clearboth"></div></td>';
			}
		}
		$row[] = '</tr>';
		return implode("\n",$row);
	}

	static function get_time_slots_and_rooms_for_session($taxonomy_terms) {
		$meta = array('time-slots'=>array());
		$rooms = array();
		$session_types = array();
		$presenters = array();
		$taxonomy_terms = explode(',',$taxonomy_terms);
		foreach($taxonomy_terms as $taxonomy_term) {
			if (substr($taxonomy_term,0,10)=='time-slot/') {
				$meta['time-slots'][str_replace('time-slot/','',$taxonomy_term)]= array();
			} else if (substr($taxonomy_term,0,5)=='room/') {
				$rooms[] = str_replace('room/','',$taxonomy_term);
			} else if (substr($taxonomy_term,0,10)=='presenter/') {
				$presenters[] = str_replace('presenter/','',$taxonomy_term);
			} else if (substr($taxonomy_term,0,13)=='session-type/') {
				$session_types[] = str_replace('session-type/','',$taxonomy_term);
			}
		}
		foreach($meta['time-slots'] as $time_slot => $value) {
			$meta['time-slots'][$time_slot]['rooms'] = $rooms;
		}
		$meta['presenters'] = $presenters;
		$meta['session-types'] = $session_types;
		return $meta;
	}

	/*
	 This function assumes the patch provided with this trac ticket.
	  http://core.trac.wordpress.org/ticket/12731
	 This function is an alternate to using the function that follows.
	*/
	function get_posts_callback_add_taxonomy_terms( $value,$query,$tag ) {
		global $wpdb;
		switch($tag) {
			case 'posts_fields':
				$value .= ',taxonomy_terms.taxonomy_terms';
				break;
			case 'posts_join':
				$value .= " LEFT OUTER JOIN (SELECT $wpdb->posts.ID AS post_ID,
						GROUP_CONCAT(CONCAT_WS('/',$wpdb->term_taxonomy.taxonomy,$wpdb->terms.slug)
				    ORDER BY $wpdb->terms.name SEPARATOR ',') AS taxonomy_terms FROM $wpdb->posts
				    LEFT OUTER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.object_id=$wpdb->posts.ID
				    LEFT OUTER JOIN $wpdb->term_taxonomy ON
				    $wpdb->term_taxonomy.term_taxonomy_id=$wpdb->term_relationships.term_taxonomy_id
				    LEFT OUTER JOIN $wpdb->terms ON $wpdb->terms.term_id=$wpdb->term_taxonomy.term_id
				    WHERE 1=1 GROUP BY $wpdb->posts.ID) taxonomy_terms ON $wpdb->posts.ID=taxonomy_terms.post_ID ";
				break;
		}
		return $value;
	}

	static function add_taxonomies_to_posts($sql) {
		global $wpdb;
		//$where = " AND tt.taxonomy IN ('room','time-slot')";
		$where ='';
		$sql = str_replace('wp_posts.* FROM',"wp_posts.*,tt.taxonomy_terms FROM",$sql);
		$sql = str_replace('FROM wp_posts',"FROM wp_posts LEFT OUTER JOIN (
			SELECT p.ID AS post_ID,GROUP_CONCAT(CONCAT_WS('/',tt.taxonomy,t.slug)
			ORDER BY t.name SEPARATOR ',') AS taxonomy_terms FROM wp_posts p
			LEFT OUTER JOIN wp_term_relationships tr ON tr.object_id=p.ID
			LEFT OUTER JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id=tr.term_taxonomy_id
			LEFT OUTER JOIN wp_terms t ON t.term_id=tt.term_id WHERE 1=1 $where
			GROUP BY p.ID) tt ON wp_posts.ID=tt.post_ID",$sql);
		$wpdb->query($wpdb->prepare('SET SESSION group_concat_max_len = %d;',10240));
		//echo $sql; exit;
		return $sql;
	}

	static function get_sessions_by_time_and_room() {
		$time_slots = self::get_time_slots();

		/* 	 This line assumes the patch provided with this trac ticket. 
	          http://core.trac.wordpress.org/ticket/12731
	       This line is an alternate to the three lines that follow it
				$q = new WP_Query('post_type=session&posts_per_page=9999&callback=add_taxonomy_terms');
	  */
		add_filter('posts_request',array(__CLASS__,'add_taxonomies_to_posts'));
		$date_query = (is_null(self::$date) ? '' : '&taxonomy=date&term='.sanitize_title_with_dashes(self::$date));
		$q = new WP_Query("post_type=session&posts_per_page=9999$date_query");
		remove_filter('posts_request',array(__CLASS__,'add_taxonomies_to_posts'));

		foreach($q->posts as $post) {
			if (!is_null($post->taxonomy_terms)) {
				$session_meta = self::get_time_slots_and_rooms_for_session($post->taxonomy_terms);
				$session = (object) array(
					'ID'      => $post->ID,
					'slug'    => $post->post_name,
					'title'   => $post->post_title,
					'content' => $post->post_content,
					'excerpt' => $post->post_excerpt,
					'presenters'    => self::expand_presenters($session_meta['presenters']),
					'session_types' => self::expand_session_types($session_meta['session-types']),
				);
				foreach($session_meta['time-slots'] as $slug => $time_slot) {
					foreach($time_slot['rooms'] as $room) {
						$assignments = get_post_meta($post->ID,'Time-Slot/Room Assignments',true);
						if (!empty($assignments))
							$assignments = explode(',',$assignments);
						if (empty($assignments) || in_array("$time_slot/$room",$assignments)) {
							$time_slots[$slug]->rooms[$room]->sessions[] = $session;
						}
					}
				}
			}
		}
		self::set_colspans($time_slots);
		self::set_rowspans($time_slots);
		return $time_slots;
	}
	static function expand_presenters($these_presenters) {
		static $presenters = array();
		$expanded_presenters = array();
		foreach($these_presenters as $presenter) {
			if (isset($presenters[$presenter])) {
				$term = $presenters[$presenter]['term'];
				$link = $presenters[$presenter]['link'];
				$html = $presenters[$presenter]['html'];
			} else {
				$presenters[$presenter]['term'] = $term = get_term_by('slug',$presenter,'presenter');
				$presenters[$presenter]['link'] = $link = get_term_link($term,'presenter');
				$presenters[$presenter]['html'] = $html = "<a href=\"$link\">{$term->name}</a>";
			}
			$expanded_presenters[] = array(
				'term' => $term,
				'link' => $link,
				'html' => $html,
			);
		}
		return $expanded_presenters;
	}
	static function expand_session_types($these_session_types) {
		static $session_types = array();
		$expanded_session_types = array();
		foreach($these_session_types as $session_type) {
			if (isset($session_types[$session_type])) {
				$term = $session_types[$session_type]['term'];
				$link = $session_types[$session_type]['link'];
				$html = $session_types[$session_type]['html'];
			} else {
				$session_types[$session_type]['term'] = $term = get_term_by('slug',$session_type,'session-type');
				if ($term===false) {
					$session_types[$session_type]['link'] = '';
					$session_types[$session_type]['html'] = '';
				} else {
					$session_types[$session_type]['link'] = $link = get_term_link($term,'session-type');
					$session_types[$session_type]['html'] = $html = "<a href=\"$link\" title=\"{$term->name}\"><span class=\"session-type $session_type\">{$term->name}</span></a>";
				}
			}
			$expanded_session_types[] = array(
				'term' => $term,
				'link' => $link,
				'html' => $html,
			);
		}
		return $expanded_session_types;
	}
	static function set_rowspans(&$time_slots) {
		$rooms = reset($time_slots)->rooms; // the current one is as good as any
		foreach($rooms as $r_slug => $room) {
			$prior = null;
			foreach($time_slots as $ts_slug => $time_slot) {
				$time_slot->rooms[$r_slug]->rowspan = 1;
				$current = $time_slot;
				if (is_null($prior)) {
					$prior = $current;
					$same = array($current);
				} else if (count($current->rooms[$r_slug]->sessions)==0 || !isset($prior->rooms[$r_slug]->sessions[0]->ID) || $prior->rooms[$r_slug]->sessions[0]->ID != $current->rooms[$r_slug]->sessions[0]->ID) {
					$prior = $current;
					$same = array($current);
				} else if ($prior->rooms[$r_slug]->sessions[0]->ID == $current->rooms[$r_slug]->sessions[0]->ID) {
					$same[] = $current;
					$time_slots[$same[0]->slug]->rooms[$r_slug]->rowspan = count($same);
					$time_slots[$ts_slug]->rooms[$r_slug]->rowspan = 0;
				}
			}
		}
	}
	static function set_colspans(&$time_slots) {
		foreach($time_slots as $time_slot) {
			$prior = null;
			foreach($time_slot->rooms as $slug => $room) {
				$time_slot->rooms[$slug]->colspan = 1;
				$current = $room;
				if (is_null($prior)) {
					$prior = $current;
					$same = array($current);
				} else if (count($current->sessions)==0 || !isset($prior->sessions[0]) || $prior->sessions[0]->ID != $current->sessions[0]->ID) {
					$prior = $current;
					$same = array($current);
				} else if ($prior->sessions[0]->ID == $current->sessions[0]->ID) {
					$same[] = $current;
					$time_slot->rooms[$same[0]->slug]->colspan = count($same);
					$time_slot->rooms[$slug]->colspan = 0;
				}
			}
		}
	}

	static function get_time_slots() {
		$time_slots = get_terms('time-slot',array(
			'hide_empty'  => false,
			'orderby'     => 'name',
			'order'       => 'ASC')
		);
		$rooms = self::get_rooms_by_room_order();
		foreach($time_slots as $time_slot) {
			list($time_slot->start_time,$time_slot->end_time,$time_slot->time_order) =
					self::parse_start_end_times($time_slot->description);
			if (!is_null($time_slot->start_time)) {
				$this_rooms = array();
				$i = 0;
				foreach($rooms as $slug => $room)
					$this_rooms[$slug] = (object)array(
						'order'=> $i++,
						'ID' => $room->term_id,
						'slug'=> $room->slug,
						'name'=> $room->name,
						'sessions' => array(),
					);
				$new_time_slots[$time_slot->slug] = (object) array(
					'title'   => $time_slot->name,
					'slug'    => $time_slot->slug,
					'order'   => $time_slot->time_order,
					'start'   => $time_slot->start_time,
					'end'     => $time_slot->end_time,
					'rooms'   => $this_rooms,
					);
			}
		}
		property_sort($new_time_slots,'order');
		return $new_time_slots;
	}

	static function get_room_names_by_order() {
		$dates = get_terms('date',array('hide_empty' => false));
		foreach($dates as $date) {
			if (isset($date->name) && ($date->name==self::$date || $date->slug==self::$date)) {
				$rooms = str_replace('Rooms: ','',$date->description);
				$room_order = array_flip(explode(',',preg_replace('#\s?,\s?#',',',$rooms)));
				break;
			}
		}
		return $room_order;
	}
	static function get_rooms_by_room_order() {
		$rooms = get_terms('room',array('hide_empty'=>false));
		$room_order = self::get_room_names_by_order();
		$new_rooms = array();
		foreach($rooms as $room) {
			if (isset($room_order[$room->name])) {
/*					echo "ERROR: There is no {$room->name} in the list [" . implode(', ',$room_order) . '].';
				exit;
			} else {
*/
				$room->order = $room_order[$room->name];
				$new_rooms[$room->slug] = $room;
			}
		}
		property_sort($new_rooms,'order');
		return $new_rooms;
	}
	static function parse_start_end_times($description) {
		$colon_count = substr_count($description,':');          // 3 colons means "{day}: hh:mm-hh:mm", 2 colons means "hh:mm-hh:mm"
		$description = explode(':',$description);
		$date = array_shift($description);
		$times = array(null,null,null);
		if (is_null(self::$date) || $date==self::$date) {
			$description = trim(implode(':',$description));
			if (preg_match('/(1[0-2]|[1-9]):([0-5][0-9])([aA]|[pP])[mM]\-(1[0-2]|[1-9]):([0-5][0-9])([aA]|[pP])[mM](, [Dd][Aa][Yy] #?([0-9]+)(\s+)?)?/',$description,$m)) {
				$start_hour =     (int)$m[1];
				$start_minutes =  ($m[2]==0?'00':$m[5]);
				$start_minutes =  $m[2];
				$start_am_pm =    strtolower($m[3]);
				$end_hour =       $m[4];
				$end_minutes =    ($m[5]==0?'00':$m[5]);
				$end_am_pm =      strtolower($m[6]);
				$day_num =        (empty($m[8])?1:$m[8]);
			}
			$times[0] = "$start_hour:$start_minutes{$start_am_pm}m";
			$times[1] = "$end_hour:$end_minutes{$end_am_pm}m";
			$times[2] = 1440*$day_num + $start_hour + (int)$start_minutes/60.0 + ($start_am_pm=='a' || $start_hour==12 ? 0 : 12);
		}
		return $times;
	}

}

function element_array($array,$element) {
	return array_map(create_function('$e', 'return $' . "e['$element'];"),$array);
}
function property_array($array,$property) {
	return array_map(create_function('$e', 'return $e->' . $property . ';'),$array);
}
function property_sort(&$array,$property) {
	$p = $property;
	uasort($array,create_function('$a,$b','return ($a->' . $p . '== $b->' . $p . ' ? 0 : ($a->' . $p . ' < $b->' . $p . '? -1 : 1));'));
}
function element_sort(&$array,$key) {
	$k = $key;
	uasort($array,create_function('$a,$b','return ($a[' . "'$k'" . ']== $b[' . "'$k'" . '] ? 0 : ($a[' . "'$k'" . '] < $b[' . "'$k'" . '] ? -1 : 1));'));
}



/*

ADD $wp_query->taxonomy_terms

EXAMPLE: $q = new WP_Query('post_type=session&posts_per_page=9999&taxonomy_terms=1&taxonomy=room,time-slot');

Line ~1129 /wp-includes/query.php [init_query_flags()]

		$this->is_taxonomy_terms = false;

Line ~1430 /wp-includes/query.php [parse_query()]

			if ( empty($qv['taxonomy_terms']) ) {
				$this->is_taxonomy_terms = false;
			} else {
				$this->is_taxonomy_terms = true;
			}

Line ~2000 /wp-includes/query.php [get_posts()]

		if ( $this->is_taxonomy_terms ) {
			$fields .= ',taxonomy_terms.taxonomy_terms';
			$taxonomies = (strpos($q['taxonomy'],',')===false ? $q['taxonomy'] : implode("','",explode(',',$q['taxonomy'])));
			$taxonomy_terms_where = (empty($taxonomies) ? '' : " AND {$wpdb->term_taxonomy}.taxonomy IN ('$taxonomies')");
			$join .= " LEFT OUTER JOIN (SELECT $wpdb->posts.ID AS post_ID,GROUP_CONCAT(CONCAT_WS('/',$wpdb->term_taxonomy.taxonomy,$wpdb->terms.slug) ORDER BY $wpdb->terms.name SEPARATOR ',') AS taxonomy_terms FROM $wpdb->posts LEFT OUTER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.object_id=$wpdb->posts.ID LEFT OUTER JOIN $wpdb->term_taxonomy ON $wpdb->term_taxonomy.term_taxonomy_id=$wpdb->term_relationships.term_taxonomy_id LEFT OUTER JOIN $wpdb->terms ON $wpdb->terms.term_id=$wpdb->term_taxonomy.term_id WHERE 1=1 $taxonomy_terms_where GROUP BY $wpdb->posts.ID) taxonomy_terms ON wp_posts.ID=taxonomy_terms.post_ID ";
		}

*/
