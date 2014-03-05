<?php

namespace Synapse;

use Silex\Application as SilexApp;
use Silex\Application\SecurityTrait;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\Request;

class Application extends SilexApp
{
    use SecurityTrait;

    public function run(Request $request = null)
    {
        if (php_sapi_name() !== 'cli') {
            return parent::run($request);
        }

        $this['console']->run();
    }

    public function command($command)
    {
        if (! $command instanceof Command) {
            $command = $this[$command];
        }

        $this['console']->add($command);
    }
}
