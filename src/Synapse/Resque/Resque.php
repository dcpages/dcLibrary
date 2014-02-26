<?php

namespace Synapse\Resque;

use Synapse\Stdlib\Arr;
use Resque as ResqueLib;

class Resque
{
    public static function factory($config = [])
    {
        return new Resque($config);
    }

    protected function __construct($config = [])
    {
        ResqueLib::setBackend(Arr::get($config, 'host'));
    }

    public function enqueue($queue, $class, $args = null, $track_status = false)
    {
        ResqueLib::enqueue($queue, $class, $args, $track_status);
    }
}
