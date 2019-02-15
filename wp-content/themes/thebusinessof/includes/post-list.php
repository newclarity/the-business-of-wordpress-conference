<?php

add_shortcode( 'list-posts', 'tboc_list_posts' );
add_shortcode( 'post-list', 'tboc_list_posts' );

function tboc_list_posts( $args ) {
	if ( ! isset( $args[ 'type' ] ) ) {
		return "You must specify a post_type using the \"type\" attribute of the post-list/list-posts shortcode.";
	}

	$save_post = isset( $GLOBALS[ 'post' ] )
		? $GLOBALS[ 'post' ]
		: null;

	$post_type = isset( $args[ 'type' ] )
		? $args[ 'type' ]
		: 'post';

	$args = wp_parse_args( $args, array(
		'type'     => 'post',
		'order'    => 'ASC',
		'filters'  => '',
		'orderby'  => 'title',
		'template' => "{$post_type}.php",
		'count'    => 50,
		'extra'    => null,
		'class'    => '',
	) );
	extract( $args );
	if ( ! empty( $class ) ) {
		$class = " $class";
	}

	$q = "showposts={$args['count']}&post_type={$args['type']}";
	$args[ 'orderby' ] = strtolower( $args[ 'orderby' ] );
	if ( ! in_array( $args[ 'orderby' ], array(
		'rand',
		'author',
		'date',
		'title',
		'modified',
		'menu_order',
		'parent',
		'ID',
		'rand',
		'none',
		'comment_count'
	) ) ) {
		$args[ 'orderby' ] = 'title';
	} else if ( 'meta_value' === $args[ 'orderby' ] && isset( $args[ 'meta_key' ] ) ) {
		$args[ 'orderby' ] = "meta_value&meta_key={$args['meta_key']}";
	}
	$q .= "&orderby={$args['orderby']}";

	if ( 'rand' !== $args[ 'orderby' ] ) {
		$order = strtoupper( $args[ 'orderby' ] );
		if ( ! in_array( $order, array( 'ASC', 'DESC', 'RAND' ) ) ) {
			$order = 'ASC';
		}
		if ( $order == 'RAND' ) {
			$q .= "&orderby=RAND";
		} // Avoid the confusion of $order or $args['orderby'] should be 'RAND'
		else {
			$q .= "&order={$args['order']}";
		}
	}
	if ( isset( $args[ 'subtype' ] ) && false !== strpos( $args[ 'subtype' ], '/' ) ) {
		list( $taxonomy, $term ) = explode( '/', $args[ 'subtype' ] );
		$taxonomy = tboc_get_taxomony_name( $taxonomy );
		$q        .= "&taxonomy={$taxonomy}&term={$term}";
	}

	foreach ( explode( ',', $args[ 'filters' ] ) as $filter ) {
		list( $key, $value ) = explode( ':', "{$filter}:" );
		if ( 'tt' === $key ) {
			list( $taxonomy, $term ) = explode( '/', $value );
			$taxonomy = tboc_get_taxomony_name( $taxonomy );
			$q  .= "&taxonomy={$taxonomy}&term={$term}";
		}
	}
	$q      = new WP_Query( $q );
	$rows   = array();
	$rows[] = "<div class=\"post-list {$args['type']}-type-list{$class} \">";
	foreach ( $q->posts as $post ) {
		$GLOBALS[ 'post' ] = $post;
		ob_start();
		include( realpath( dirname( __FILE__ ) . "/../{$args['template']}" ) );
		$rows[] = ob_get_clean();
	}
	$rows[]            = '</div>';
	$GLOBALS[ 'post' ] = $save_post;

	return implode( "\n", $rows );
}


