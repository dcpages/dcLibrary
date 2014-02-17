<?php

namespace Synapse\Provider;

use Silex\Application;

/**
 * Enables config:config_namespace syntax for loading config values.
 */
class ConfigServiceResolver
{
    const SERVICE_PATTERN = "/config:[A-Za-z0-9\._\-]+/";

    protected $resolver;
    protected $app;

    /**
     * Constructor.
     *
     * @param ControllerResolverInterface $resolver A ControllerResolverInterface instance to delegate to
     * @param Application                 $app      An Application instance
     */
    public function __construct($resolver, Application $app)
    {
        var_dump($resolver);die();
        $this->resolver = $resolver;
        $this->app      = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $controller = $request->attributes->get('_controller', null);

        if (!is_string($controller) || !preg_match(static::SERVICE_PATTERN, $controller)) {
            return $this->resolver->getController($request);
        }

        $service = str_replace(':rest', '', $controller);

        if (!isset($this->app[$service])) {
            throw new \InvalidArgumentException(sprintf('Service "%s" does not exist.', $service));
        }

        // Bubble to the next resolver maybe?
        if (!($this->app[$service] instanceof AbstractRestController)) {
            return null;
        }

        return array($this->app[$service], 'execute');
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(Request $request, $controller)
    {
        return $this->resolver->getArguments($request, $controller);
    }
}
