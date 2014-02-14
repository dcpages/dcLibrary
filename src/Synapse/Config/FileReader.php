<?php

namespace Synapse\Config;

class FileReader
{
    public function __construct($filename)
    {
        $this->_filename = $filename;
    }

    public function load()
    {
        return include $filename;
    }
}
