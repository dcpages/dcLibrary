<?php

namespace Synapse\Mapper;

use Synapse\Stdlib\Arr;
use Synapse\Entity\AbstractEntity as AbstractEntity;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\PreparableSqlInterface;

/**
 * An abstract class for mapping database records to entity objects
 */
abstract class AbstractMapper
{
    /**
     * Database adapter
     *
     * @var DbAdapter
     */
    protected $dbAdapter;

    /**
     * Entity prototype used to return hydrated entities
     *
     * @var Entity
     */
    protected $prototype;

    /**
     * Name of the table for which this mapper is responsible
     *
     * @var string
     */
    protected $tableName;

    /**
     * Set injected objects as properties
     *
     * @param DbAdapter      $db        Query builder object
     * @param AbstractEntity $prototype Entity prototype
     */
    public function __construct(DbAdapter $dbAdapter, AbstractEntity $prototype = null)
    {
        $this->dbAdapter = $dbAdapter;
        $this->prototype = $prototype;
    }

    /**
     * Create a new entity of this type, populating its data from an array
     *
     * @param  AbstractEntity $entity
     * @param  array          $data
     * @return AbstractEntity
     */
    public function fromArray($entity, array $data)
    {
        foreach ($data as $field => $value) {
            $setter = 'set'.ucfirst($field);
            $entity->$setter($value);
        }

        return $entity;
    }

    /**
     * Persist this entity, inserting it if new and otherwise updating it
     *
     * @param  AbstractEntity $entity
     * @return AbstractEntity
     */
    public function persist(AbstractEntity $entity)
    {
        if ($entity->isNew()) {
            return $this->insert($entity);
        }

        return $this->update($entity);
    }

    /**
     * Return the entity prototype
     *
     * @return AbstractEntity
     */
    public function getPrototype()
    {
        return $this->prototype;
    }

    /**
     * Set the entity prototype for this mapper
     *
     * @param AbstractEntity $prototype
     */
    public function setPrototype(AbstractEntity $prototype)
    {
        $this->prototype = $prototype;
        return $this;
    }

    /**
     * Execute a given query
     *
     * @param  PreparableSqlInterface $query Query to be executed
     * @return Result
     */
    protected function execute(PreparableSqlInterface $query)
    {
        $statement = $this->sql()->prepareStatementForSqlObject($query);

        return $statement->execute();
    }

    /**
     * Return a new Sql object with Zend Db Adapter and table name injected
     *
     * @return Sql
     */
    protected function sql()
    {
        return new Sql($this->dbAdapter, $this->tableName);
    }
}
