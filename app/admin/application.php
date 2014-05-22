<?php

/**
 * application.php - Write your custom code below.
*/
$metabox = Metabox::make('Informations', 'post')->set(array(
    'main' => array(
        Field::text('author', array('info' => 'Un message pour l\'auteur.')),
        Field::text('age'),
        Field::text('email', array('info' => 'Please insert your email address.')),
        Field::text('website', array('info' => 'Please specify your website address.')),
        Field::checkbox('enabled', array('title' => 'Activate')),
        Field::textarea('content', array('info' => 'Insert content here.', 'title' => 'Summary')),
        Field::text('color', array('info' => 'Define an hexadecimal color.')),
        Field::checkboxes('sizes', array('small', 'medium', 'large'), array('info' => 'Choose one or more sizes.')),
        Field::radio('transport', array('none', 'postal', 'ups'), array('title' => 'Delivery', 'info' => 'Choose one delivery service.')),
        Field::select('country', array(array('belgique', 'portugal', 'canada'))),
        Field::select('country-invoice', array(array('belgique', 'portugal', 'canada')), true, array('title' => 'Invoice country'))
    )
));

$metabox->validate(array(
    'author'    => array('textfield'),
    'age'       => array('num'),
    'email'     => array('email'),
    'website'   => array('url:http'),
    'content'   => array('kses:a|href|title,p,h3'),
    'color'     => array('color')
));