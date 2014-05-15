<?php

/**
 * application.php - Write your custom code below.
*/

/*Metabox::make('Development', 'post')->set(array(
    Field::checkbox('prout')
));*/

//$t = new Themosis\Field\Fields\TextField();
//echo($t->metabox());


Metabox::make('Informations', 'post')->set(array(
    'main' => array(
        Field::text('author', array('info' => 'Un message pour l\'auteur.')),
        Field::text('age')
    )
));

?>