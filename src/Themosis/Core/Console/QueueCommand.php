<?php

namespace Themosis\Core\Console;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Themosis\Core\Bus\Dispatchable;

class QueueCommand implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * The data to pass to the console command.
     *
     * @var array
     */
    protected $data;

    /**
     * QueueCommand constructor.
     * Create a new job instance.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Handle the job.
     *
     * @param \Illuminate\Contracts\Console\Kernel $kernel
     */
    public function handle(\Illuminate\Contracts\Console\Kernel $kernel)
    {
        call_user_func_array([$kernel, 'call'], $this->data);
    }
}
