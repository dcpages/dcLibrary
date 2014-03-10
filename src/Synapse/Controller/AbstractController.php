<?php

namespace Synapse\Controller;

use Synapse\Application\UrlGeneratorAwareInterface;
use Synapse\Application\UrlGeneratorAwareTrait;

abstract class AbstractController implements UrlGeneratorAwareInterface
{
    use UrlGeneratorAwareTrait;
}
