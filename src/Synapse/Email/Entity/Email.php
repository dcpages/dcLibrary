<?php

namespace Synapse\Email\Entity;

use Synapse\Entity\AbstractEntity;

/**
 * Email entity
 */
class Email extends AbstractEntity
{
    /**
     * {@inheritDoc}
     */
    protected $object = [
        'id'              => null,
        'hash'            => null,
        'status'          => null,
        'subject'         => null,
        'recipient_email' => null,
        'recipient_name'  => null,
        'sender'          => null,
        'template_name'   => null,
        'template_data'   => null,
        'message'         => null,
        'bcc'             => null,
        'attachments'     => null,
        'headers'         => null,
        'date_sent'       => null,
        'date_created'    => null,
        'date_updated'    => null,
    ];

    /**
     * {@inheritDoc}
     */
    public function fromArray(array $values)
    {
        $entity = parent::fromArray($values);

        $entity->setDateCreated(time());

        return $entity;
    }
}
