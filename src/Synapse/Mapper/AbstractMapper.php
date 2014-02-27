<?php

namespace Synapse\Mapper;

use Synapse\Stdlib\Arr;
use Synapse\Db\TableGatewayFactory;
use Synapse\Entity\AbstractEntity as AbstractEntity;
use Zend\Db\Adapter\Adapter as DbAdapter;

abstract class AbstractMapper
{
    /**
     * Database adapter
     *
     * @var Zend\Db\Adapter\Adapter
     */
    protected $db;

    /**
     * Database table gateway
     *
     * @var Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var string
     */
    protected $dbName;

    /**
     * @var Entity
     */
    protected $prototype;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Set injected objects as properties
     * @param DbAdapter      $db        Database adapter
     * @param AbstractEntity $prototype Entity prototype
     */
    public function __construct(TableGatewayFactory $tableGatewayFactory, AbstractEntity $prototype = null)
    {
        $this->prototype = $prototype;

        $this->tableGateway = $tableGatewayFactory->factory($this->tableName);
    }

    public function findBy(array $fields)
    {
        $query = $this->tableGateway->select()
            ->from($this->_table_name);

        foreach ($fields as $name => $value) {
            $query->where($name, '=', $value);
        }

        $data = $query
            ->execute($this->_db)
            ->current();

        if (! $data) {
            $data = [];
        }

        return $this->from_array(clone $this->get_prototype(), $data);
    }

    public function findById($id)
    {
        return $this->findBy(['id' => $id]);
    }

    public function findAllBy($fields, array $options = [])
    {
        $query = $this->tableGateway->select();

        foreach ($fields as $name => $value) {
            $query->where($name, '=', $value);
        }

        $this->setOrder($query, $options);

        $results = $query;

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
        if ($entity->get_id()) {
            return $this->update($entity);
        } else {
            return $this->insert($entity);
        }
    }

    public function insert(AbstractEntity $entity)
    {
        $db_value_array = $entity->getDbValues();

        $keys = array_keys($db_value_array);
        $values = array_values($db_value_array);

        list($id, $_) = $this->tableGateway->insert($this->_table_name, $keys)
            ->values($values)
            ->execute($this->_db);

        $entity->setId($id);

        return $entity;
    }

    public function update(AbstractEntity $entity)
    {
        $db_value_array = $entity->getDbValues();

        unset($db_value_array['id']);

        $this->tableGateway->update($this->_table_name)
            ->set($db_value_array)
            ->where('id', '=', $entity->getId())
            ->execute($this->_db);

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

    protected function setOrder($query, $options)
    {
        if (! Arr::get($options, 'orderBy')) {
            return $query;
        }

        // Can specify order as [['column', 'direction'], ['column', 'direction']].
        if (is_array($options['orderBy'])) {
            foreach ($options['orderBy'] as $orderBy) {
                if (is_array($orderBy)) {
                    $query->orderBy(
                        Arr::get($orderBy, 0),
                        Arr::get($orderBy, 1)
                    );
                } else {
                    $query->orderBy($orderBy);
                }
            }
        } else { // Also support just a single ascending value
            return $query->orderBy($options['orderBy']);
        }

        return $query;
    }
}
