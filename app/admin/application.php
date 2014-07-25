<?php

/**
 * application.php - Write your custom code below.
 */
$posts = array();
foreach(PostModel::all() as $post)
{
    $posts[$post->ID] = $post->post_title;
}

$metabox = Metabox::make('Link', 'post')->set(array(

    Field::select('related', array($posts), false, array('title' => 'Related post')),
    Field::text('actor'),
    Field::infinite('things', array(
        Field::text('sock')
    ))

));

$metabox->validate(array(
    'actor'     => array('textfield', 'min:5'),
    'things'    => array(
        'sock'  => array('num')
    )
));

PostType::make('jl_books', 'Books', 'Book')->set();