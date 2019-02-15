<?php

add_action('init', 'tboc_post_types');

function tboc_post_types() {

	register_post_status('hidden',array(
		'label' => __( 'Hidden', 'post' ),
		'capability_type' => 'post',
		'internal'        => false,
		'public'          => true,
		'private'         => false,
		'protected'       => false,
		'hierarchical'    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all'         => true,
		'publicly_queryable'        => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
	));

	register_post_type('event',
		array(
			'label'           => __('Event'),
			'singular_label'  => __('Event'),
			'public'          => true,
			'show_ui'         => true,
			'query_var'       => 'event',
			'rewrite'         => array('slug' => 'events'),
			'supports'        => array(
				'title',
				'editor',
				'revisions',
				'custom-fields',
				),
			)
	);

	register_post_type('session',
		array(
			'label'           => __('Sessions'),
			'singular_label'  => __('Session'),
			'public'          => true,
			'show_ui'         => true,
			'query_var'       => 'session',
			'rewrite'         => array('slug' => 'sessions'),
			'hierarchical'    => true,
			'supports'        => array(
				'title',
				'editor',
				//'page-attributes',
				'excerpts',
				'custom-fields',
				),
			)
	);

	register_taxonomy('session-type', 'session', array(
		'hierarchical'    => true,
		'label'           => __('Session Types'),
		'singular_label'  => __('Session Type'),
		'query_var'       => 'session-type',
		'rewrite'         => array('slug' => 'session-types' ),
		)
	);

	register_taxonomy('presenter', 'session', array(
		'hierarchical'    => true,
		'label'           => __('Presenters'),
		'singular_label'  => __('Presenter'),
		'query_var'       => 'presenter',
		'rewrite'         => array('slug' => 'presenters' ),
		)
	);

	register_taxonomy('audience', 'session', array(
		'hierarchical'    => true,
		'label'           => __('Audience'),
		'query_var'       => 'audience',
		'rewrite'         => array('slug' => 'audience' ),
		)
	);

	register_taxonomy('time-slot', 'session', array(
		'hierarchical'    => true,
		'label'           => __('Time Slots'),
		'singular_label'  => __('Time Slot'),
		'query_var'       => 'time-slot',
		'rewrite'         => array('slug' => 'time-slots' ),
		)
	);

	register_taxonomy('room', 'session', array(
		'hierarchical'    => true,
		'label'           => __('Rooms'),
		'singular_label'  => __('Room'),
		'query_var'       => 'room',
		'rewrite'         => array('slug' => 'rooms' ),
		)
	);

	register_taxonomy('date', 'session', array(
		'hierarchical'    => true,
		'label'           => __('Dates'),
		'singular_label'  => __('Date'),
		'query_var'       => 'date',
		'rewrite'         => array('slug' => 'dates' ),
		)
	);

	register_post_type('offering',
		array(
			'label'           => __('Offerings'),
			'singular_label'  => __('Offering'),
			'public'          => true,
			'show_ui'         => true,
			'query_var'       => 'offering',
			'rewrite'         => array('slug' => 'offerings'),
			'supports'        => array(
				'title',
				'editor',
				'page-attributes',
				'thumbnail',
				'excerpts',
				'revisions',
				'custom-fields',
				),
			)
	);

	register_post_type('company',
		array(
			'label'           => __('Companies'),
			'singular_label'  => __('Company'),
			'public'          => true,
			'show_ui'         => true,
			'query_var'       => 'company',
			'rewrite'         => array('slug' => 'companies'),
			'supports'        => array(
				'title',
				'editor',
				'page-attributes',
				'thumbnail',
				'excerpts',
				'revisions',
				'custom-fields',
				),
			)
	);

	register_taxonomy('partner-level', 'company', array(
		'hierarchical' => true,
		'label' => __('Partner Levels'),
		'singular_label'  => __('Partner Level'),
		'query_var' => 'partner-level',
		'rewrite' => array(
			'slug' => 'partner-levels',
			),
		)
	);

	register_taxonomy('partner-type', 'company', array(
		'hierarchical'    => false,
		'label'           => __('Partner Types'),
		'singular_label'  => __('Partner Type'),
		'query_var'       => 'partner-types',
		'rewrite'         => array('slug' => 'partner-types' ),
		)
	);

	register_post_type('person',
		array(
			'label'           => __('People'),
			'singular_label'  => __('Person'),
			'public'          => true,
			'show_ui'         => true,
			'query_var'       => 'person',
			'rewrite'         => array('slug' => 'people'),
			'supports' => array(
				'title',
				'editor',
				'page-attributes',
				'thumbnail',
				'featured-image',
				//'custom-fields',
				'revisions',),
			)
	);


	register_taxonomy('role', 'person', array(
		'hierarchical'    => false,
		'label'           => __('Roles'),
		'singular_label'  => __('Role'),
		'query_var'       => 'roles',
		'rewrite'         => array('slug' => 'roles' ),
		)
	);


	if (client_is_localhost()) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules(false);
	}
}

