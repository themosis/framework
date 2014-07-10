<?php

/**
 * application.php - Write your custom code below.
 */
$posts = array();
foreach(PostModel::all() as $post)
{
    $posts[$post->ID] = $post->post_title;
}

Metabox::make('Link', 'post')->set(array(

    Field::select('related', array($posts), false, array('title' => 'Related post')),
    Field::infinite('things', array(
        Field::text('sock')
    ))

));

PostType::make('jl_books', 'Books', 'Book')->set();