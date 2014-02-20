<?php

namespace Synapse\View\Migration;

use Synapse\View\AbstractView;

class Create extends AbstractView
{
    protected $description;
    protected $classname;

    public function description($description = null)
    {
        if ($description === null)
            return $this->description;

        $this->description = $description;
    }

    public function classname($classname = null)
    {
        if ($classname === null)
            return $this->classname;

        $this->classname = $classname;
    }
}
