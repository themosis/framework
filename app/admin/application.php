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
        Field::checkbox('enabled', array('title' => 'Activate'))
    )
));

$metabox->validate(array(
    'author'    => array('textfield'),
    'age'       => array('num'),
    'email'     => array('email'),
    'website'   => array('url')
));