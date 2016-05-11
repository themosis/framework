<?php

namespace Themosis\View\Extensions\Tags;

use Twig_Token;
use Twig_TokenParser;

class Loop_TokenParser extends Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        return new Loop_Node();
    }

    public function getTag()
    {
        return 'loop';
    }

}
