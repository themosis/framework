<?php

namespace Themosis\Tests\Field;

use PHPUnit\Framework\TestCase;
use Themosis\Field\Factory;
use Themosis\Tests\Application;
use Themosis\Tests\ViewFactory;

class FieldTest extends TestCase
{
    use Application;
    use ViewFactory;

    protected function getFieldFactory()
    {
        return new Factory(
            $this->getApplication(),
            $this->getViewFactory($this->getApplication(), [__DIR__.'/views']),
        );
    }

    public function test_field_view_can_have_custom_data()
    {
        $factory = $this->getFieldFactory();

        $text = $factory->text('someone')
            ->setView('textfield')
            ->with(['title' => 'myvarvalue']);

        $this->assertTrue(false !== strpos($text->render(), 'myvarvalue'));

        $email = $factory->email('email')
            ->with(['email' => 'contactme@company.org'])
            ->setView('emailfield');

        $this->assertTrue(false !== strpos($email->render(), 'contactme@company.org'));
    }
}
