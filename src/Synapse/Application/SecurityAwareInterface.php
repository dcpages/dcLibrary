<?php

namespace Synapse\Application;

use Symfony\Component\Security\Core\SecurityContext;

interface SecurityAwareInterface
{
    /**
     * @param SecurityContext $security
     */
    public function setSecurityContext(SecurityContext $urlGenerator);

    /**
     * Gets a user from the Security Context.
     *
     * @return mixed
     */
    public function user();
}
