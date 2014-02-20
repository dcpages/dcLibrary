<?php

namespace Synapse\Upgrade;

/**
 * Abstract upgrade class to be extended by all upgrades
 */
abstract class AbstractUpgrade
{
    /**
     * Execute the upgrade
     */
    abstract public function execute();
}
