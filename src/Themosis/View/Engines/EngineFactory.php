<?php
namespace Themosis\View\Engines;

use Themosis\View\ViewData;

class EngineFactory {

    /**
     * The instantiated engine.
     *
     * @var \Themosis\View\Engines\Engine
     */
    public $engine;

    /**
     * Creates an EngineFactory instance.
     *
     * @param string $engine The engine type - 'file', 'db',...
     * @param \Themosis\View\ViewData $view The view datas.
     */
    public function __construct($engine, ViewData $view)
    {
        $this->engine = $this->setEngine($engine, $view);
    }

    /**
     * Build the engine instance.
     *
     * @param string $engine The engine type.
     * @param \Themosis\View\ViewData $view The view datas
     * @return \Themosis\View\Engines\Engine
     */
    private function setEngine($engine, $view)
    {
        switch($engine){
            case 'db':
                return new DatabaseEngine($view);
            break;

            case 'file':
                return new FileEngine($view);
            break;

            default:
                return new DatabaseEngine($view);
        }
    }

}