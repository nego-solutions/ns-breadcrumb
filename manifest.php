<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( '(NS) Breadcrumb', 'fw' );
$manifest['description'] = __( 'Display breadcrumb trail. It supports custom post types and taxonomies. Use <code>do_action("ns_show_breadcrumb")</code> where you want to display it.', 'fw' );
$manifest['version'] = '1.0.0';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['thumbnail'] = plugin_dir_url(__FILE__).'ns-avatar.png';

$manifest['github_update'] = 'nego-solutions/ns-breadcrumb';
