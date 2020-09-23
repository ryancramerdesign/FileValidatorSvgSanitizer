<?php namespace ProcessWire;

$config = array(
	'removeRemoteReferences' => array(
		'type' => 'checkbox',
		'label' => __('Remove remote references?'),
		'description' => __('This will stop HTTP leaks but can increase the time it takes to sanitize.'), 
		'value' => 1 
	),
	'minify' => array(
		'type' => 'checkbox',
		'label' => __('Minify sanitized SVG files?'),
		'description' => __('This will perform minification on whitespace in the sanitized SVG markup, potentially reducing the file size somewhat.'),
		'value' => 0
	)
);

