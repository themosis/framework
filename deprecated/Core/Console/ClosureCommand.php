<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClosureCommand extends Command
{
    /**
     * The command callback.
     *
     * @var \Closure
     */
    protected $callback;

    public function __construct($signature, \Closure $callback)
    {
        $this->signature = $signature;
        $this->callback = $callback;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputs = array_merge($input->getArguments(), $input->getOptions());

        $parameters = [];

        foreach ((new \ReflectionFunction($this->callback))->getParameters() as $parameter) {
            if (isset($inputs[$parameter->name])) {
                $parameters[$parameter->name] = $inputs[$parameter->name];
            }
        }

        return $this->laravel->call(
            $this->callback->bindTo($this, $this),
            $parameters
        );
    }

    /**
     * Set the command description.
     *
     * @param $description
     *
     * @return $this
     */
    public function describe($description)
    {
        $this->setDescription($description);

        return $this;
    }
}
