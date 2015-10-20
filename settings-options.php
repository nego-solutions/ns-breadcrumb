<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$post_types = get_post_types();

$templates = array(
	'category'=> 'category',
	'tag'=> 'tag',
	'archive'=> 'archive',
	'page' => 'page',
	'single' => 'single',
	'search' => 'search',
	'404' => '404',
	'author' => 'author'
);

//echo '<pre>'.print_r($templates, true).'</pre>';exit;

unset($post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);

$options = array(

	'general-tab' => array(
		'title'   => 'General',
		'type'    => 'tab',
		'options' => array(
			'title-length' => array(
				'label'   => __( 'Title max length', 'fw' ),
				'type'    => 'text',
				'attr'    => array('style' => 'width: 80px;'),
				'value'   => '40'
			),
			'hide-bdb-templates' => array(
				'label'   => __( 'Hide on the following templates', 'fw' ),
				'type'    => 'checkboxes',
				'choices'   => $templates,
			),
			'hide-bdb-types' => array(
				'label'   => __( 'Hide on the following types', 'fw' ),
				'type'    => 'checkboxes',
				'choices'   => $post_types
			),
		)
	),

	'labels-tab' => array(
		'title'   => 'Labels',
		'type'    => 'tab',
		'options' => array(
			'prefix-label' => array(
				'label'   => __( 'Prefix', 'fw' ),
				'type'    => 'text',
				'value'   => 'Home'
			),
			'category-label' => array(
				'label'   => __( 'For category', 'fw' ),
				'type'    => 'text',
				'value'   => 'Category:'
			),
			'tag-label' => array(
				'label'   => __( 'For Tag', 'fw' ),
				'type'    => 'text',
				'value'   => 'Posts Tagged:'
			),
			'search-label' => array(
				'label'   => __( 'For search', 'fw' ),
				'type'    => 'text',
				'value'   => 'Search Results for:'
			),
			'author-label' => array(
				'label'   => __( 'For author', 'fw' ),
				'type'    => 'text',
				'value'   => 'Archived Article(s) by Author:'
			),
			'error-label' => array(
				'label'   => __( 'For 404', 'fw' ),
				'type'    => 'text',
				'value'   => 'Error 404 - Not Found'
			),
		)
	),

);