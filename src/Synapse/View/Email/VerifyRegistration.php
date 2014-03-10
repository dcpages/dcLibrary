<?php

namespace Synapse\View\Email;

use Synapse\View\AbstractView;
use Synapse\User\Entity\User;
use Synapse\User\Entity\UserToken;
use Symfony\Component\Routing\Generator\UrlGenerator;

class VerifyRegistration extends AbstractView
{
    const VERIFY_REGISTRATION_ROUTE = 'verify-registration';

    protected $userToken;
    protected $urlGenerator;

    public function setUserToken(UserToken $userToken)
    {
        $this->userToken = $userToken;
    }

    public function setUrlGenerator(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function url()
    {
        $parameters = [
            'id'    => $this->userToken->getUserId(),
            'token' => $this->userToken->getToken(),
        ];

        $url = $this->urlGenerator->generate(
            self::VERIFY_REGISTRATION_ROUTE,
            $parameters,
            UrlGenerator::ABSOLUTE_URL
        );

        return $url;
    }
}
