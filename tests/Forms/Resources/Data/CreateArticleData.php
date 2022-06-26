<?php

namespace Themosis\Tests\Forms\Resources\Data;

class CreateArticleData
{
    public $title;

    public $content;

    private $author;

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param  mixed  $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }
}
