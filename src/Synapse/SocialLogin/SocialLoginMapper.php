<?php

namespace Synapse\SocialLogin;

use Synapse\Mapper;

/**
 * User mapper
 */
class SocialLoginMapper extends Mapper\AbstractMapper
{
    /**
     * Use all mapper traits, making this a general purpose mapper
     */
    use Mapper\InserterTrait;
    use Mapper\FinderTrait;
    use Mapper\UpdaterTrait;
    use Mapper\DeleterTrait;

    /**
     * Name of user table
     *
     * @var string
     */
    protected $tableName = 'user_social_logins';

    public function findByUserId($id)
    {
        $entity = $this->findBy(['user_id' => $id]);
        return $entity;
    }

    public function findByProviderUserId($provider, $id)
    {
        $entity = $this->findBy([
            'provider'         => $provider,
            'provider_user_id' => $id
        ]);
        return $entity;
    }
}
