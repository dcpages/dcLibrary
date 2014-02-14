<?php

namespace Synapse\View;

abstract class AbstractView
{
    /**
     * Return the string representation of this rendered view
     * @return string
     */
    abstract public function render();
}
