<?php

namespace Synapse\Mapper;

use Synapse\Entity\AbstractEntity;

/**
 * Use this trait to add create functionality to AbstractMappers.
 */
trait InserterTrait
{
    /**
     * Insert the given entity into the database
     *
     * @param  AbstractEntity $entity
     * @return AbstractEntity         Entity with ID populated
     */
    public function insert(AbstractEntity $entity)
    {
        $values = $entity->getDbValues();

        $columns = array_keys($values);

        $query = $this->sql()
            ->insert()
            ->columns($columns)
            ->values($values);

        $result = $this->execute($query);

        $entity->setId($result->getGeneratedValue());

        return $entity;
    }
}
