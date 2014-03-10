<?php

namespace Synapse\View\Email;

use Synapse\View\AbstractView;
use Synapse\User\Entity\User;
use Synapse\User\Entity\UserToken;

class VerifyRegistration extends AbstractView
{
    protected $userToken;

    public function setUserToken(UserToken $userToken)
    {
        $this->userToken = $userToken;
    }

    public function url()
    {
        return $this->userToken;
    }
}
