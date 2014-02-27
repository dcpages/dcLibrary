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
     * Find a single entity by specific field values
     *
     * @param  array  $fields Associative array where key is field and value is the value
     * @return AbstractEntity|bool
     */
    public function findBy(array $fields)
    {
        $query = $this->sql()->select();

        foreach ($fields as $name => $value) {
            $query->where([$name => $value]);
        }

        $data = $this->execute($query)->current();

        if (! $data) {
            return false;
        }

        return $this->fromArray(clone $this->getPrototype(), $data);
    }

    /**
     * Find a single entity by ID
     *
     * @param  int|string $id Entity ID
     * @return AbstractEntity|bool
     */
    public function findById($id)
    {
        return $this->findBy(['id' => $id]);
    }

    /**
     * Find all entities matching specific field values
     *
     * @param  array $fields  Associative array where key is field and value is the value
     * @param  array $options Array of options for this request
     * @return array          Array of AbstractEntity objects
     */
    public function findAllBy($fields, array $options = [])
    {
        $query = $this->sql()->select();

        foreach ($fields as $name => $value) {
            $query->where([$name => $value]);
        }

        $this->setOrder($query, $options);

        $results = $this->execute($query);

        $entities = [];
        foreach ($results as $data) {
            if (! $data) {
                $data = [];
            }

            $entities[] = $this->fromArray(clone $this->getPrototype(), (array) $data);
        };

        return $entities;
    }

    /**
     * Find all entities in this table
     *
     * @param  array $options Array of options for this request
     * @return array          Array of AbstractEntity objects
     */
    public function findAll(array $options = [])
    {
        return $this->findAllBy([], $options);
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
     * Delete record corresponding to this entity
     *
     * @param  AbstractEntity $entity
     * @return Result
     */
    public function delete(AbstractEntity $entity)
    {
        $condition = [
            'id' => $entity->getId()
        ];

        $query = $this->sql()
            ->delete()
            ->where($condition);

        return $this->execute($query);
    }

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

    /**
     * Update the given entity in the database
     *
     * @param  AbstractEntity $entity
     * @return AbstractEntity
     */
    public function update(AbstractEntity $entity)
    {
        $dbValueArray = $entity->getDbValues();

        unset($dbValueArray['id']);

        $condition = ['id' => $entity->getId()];

        $query = $this->sql()
            ->update()
            ->set($dbValueArray)
            ->where($condition);

        $this->execute($query);

        return $entity;
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
     * Set the order on the given query
     *
     * @param Select $query
     * @param array  $options Array of options which may or may not include `order`
     */
    protected function setOrder($query, $options)
    {
        if (! Arr::get($options, 'order')) {
            return $query;
        }

        // Can specify order as [['column', 'direction'], ['column', 'direction']].
        if (is_array($options['order'])) {
            foreach ($options['order'] as $order) {
                if (is_array($order)) {
                    $query->order(
                        Arr::get($order, 0).' '.Arr::get($order, 1)
                    );
                } else {
                    $query->order($key.' '.$order);
                }
            }
        } else { // Also support just a single ascending value
            return $query->order($options['order']);
        }

        return $query;
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
