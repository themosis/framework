<?php
namespace Themosis\Html;

class FormBuilder {

    /**
     * Output a text field
     */
    public function text()
    {
        ?>

        <input type="text" name="some-name" id="some-id" value="A default value.">

    <?php
    }

}