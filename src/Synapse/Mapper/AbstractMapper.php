<?php

namespace Synapse\Mapper;

use Synapse\Stdlib\Arr;
use Synapse\Entity\AbstractEntity as AbstractEntity;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;

abstract class AbstractMapper
{
    /**
     * Sql query builder
     *
     * @var Zend\Db\Sql\Sql
     */
    protected $sql;

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
     * @param Sql            $db        Query builder object
     * @param AbstractEntity $prototype Entity prototype
     */
    public function __construct(Sql $sql, AbstractEntity $prototype = null)
    {
        $this->prototype = $prototype;
        $this->sql       = $sql;
    }

    public function findBy(array $fields)
    {
        $query = $this->select()->from($this->tableName);

        foreach ($fields as $name => $value) {
            $query->where([$name => $value]);
        }

        $data = $this->sql->prepareStatementForSqlObject($query)
            ->execute()
            ->current();

        if (! $data) {
            return false;
        }

        return $this->fromArray(clone $this->getPrototype(), $data);
    }

    public function findById($id)
    {
        return $this->findBy(['id' => $id]);
    }

    public function findAllBy($fields, array $options = [])
    {
        $query = $this->select()->from($this->tableName);

        foreach ($fields as $name => $value) {
            $query->where([$name => $value]);
        }

        $this->setOrder($query, $options);

        $results = $this->sql->prepareStatementForSqlObject($query)->execute();

        $entities = [];

        foreach ($results as $data) {
            if (! $data) {
                $data = [];
            }

            $entities[] = $this->fromArray(clone $this->getPrototype(), (array) $data);
        };

        return $entities;
    }

    public function findAll(array $options = [])
    {
        return $this->findAllBy([], $options);
    }

    public function fromArray($entity, array $data)
    {
        foreach ($data as $field => $value) {
            $setter = 'set'.ucfirst($field);
            $entity->$setter($value);
        }

        return $entity;
    }

    public function persist(AbstractEntity $entity)
    {
        if ($entity->getId()) {
            return $this->update($entity);
        } else {
            return $this->insert($entity);
        }
    }

    public function insert(AbstractEntity $entity)
    {
        $values = $entity->getDbValues();

        $columns = array_keys($values);

        $query = new Insert;

        $query->into($this->tableName)
            ->columns($columns)
            ->values($values);

        $result = $this->sql->prepareStatementForSqlObject($query)
            ->execute();

        $entity->setId($result->getGeneratedValue());

        return $entity;
    }

    public function update(AbstractEntity $entity)
    {
        $dbValueArray = $entity->getDbValues();

        unset($dbValueArray['id']);

        $query = new Update;

        $query->table($this->tableName)
            ->set($dbValueArray)
            ->where(['id' => $entity->getId()]);

        $result = $this->sql->prepareStatementForSqlObject($query)
            ->execute();

        return $entity;
    }

    public function getPrototype()
    {
        return $this->prototype;
    }

    public function setPrototype($prototype)
    {
        $this->prototype = $prototype;
        return $this;
    }

    protected function select()
    {
        return new Select;
    }

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
}
