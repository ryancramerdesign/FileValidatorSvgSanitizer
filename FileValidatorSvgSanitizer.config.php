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
	),
	'customTags' => array(
		'type' => 'textarea',
		'label' => __('Add or remove tags (1 per line)'), 
		'description' => __('To add whitelisted tags, enter one per line. To remove tags, do the same but prefix line with a minus sign.'), 
		'collapsed' => Inputfield::collapsedBlank
	),
	'customAttrs' => array(
		'type' => 'textarea',
		'label' => __('Add or remove attributes (1 per line)'),
		'description' => __('To add whitelisted attributes, enter one per line. To remove attributes, do the same but prefix line with a minus sign.'),
		'collapsed' => Inputfield::collapsedBlank
	)
);

