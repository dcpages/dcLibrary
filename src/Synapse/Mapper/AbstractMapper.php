<?php

namespace Synapse\Mapper;

use Synapse\Stdlib\Arr;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Synapse\Entity\AbstractEntity as AbstractEntity;

abstract class AbstractMapper
{
    /**
     * Database adapter
     *
     * @var Zend\Db\Adapter\Adapter
     */
    protected $db;

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
    protected $tableName = '';

    /**
     * Set injected objects as properties
     * @param DbAdapter      $db        Database adapter
     * @param AbstractEntity $prototype Entity prototype
     */
    public function __construct(DbAdapter $db, AbstractEntity $prototype = null)
    {
        $this->prototype = $prototype;

        $this->db = $db;
    }

    public function findBy(array $fields)
    {
        $query = DB::select()
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
        return $this->find_by(['id' => $id]);
    }

    public function findAllBy($fields, array $options = [])
    {
        $query = DB::select()
            ->from($this->_table_name);

        foreach ($fields as $name => $value) {
            $query->where($name, '=', $value);
        }

        $this->_set_order($query, $options);

        $results = $query
            ->execute($this->_db);

        $entities = [];

        foreach ($results as $data) {
            if (! $data) {
                $data = [];
            }

            $entities[] = $this->from_array(clone $this->get_prototype(), $data);
        };

        return $entities;
    }

    public function findAll(array $options = [])
    {
        return $this->find_all_by([], $options);
    }

    public function fromArray($entity, array $data)
    {
        foreach ($data as $field => $value) {
            $setter = 'set_'.$field;
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
        $db_value_array = $entity->get_db_values();

        $keys = array_keys($db_value_array);
        $values = array_values($db_value_array);

        list($id, $_) = DB::insert($this->_table_name, $keys)
            ->values($values)
            ->execute($this->_db);

        $entity->set_id($id);

        return $entity;
    }

    public function update(AbstractEntity $entity)
    {
        $db_value_array = $entity->get_db_values();

        unset($db_value_array['id']);

        DB::update($this->_table_name)
            ->set($db_value_array)
            ->where('id', '=', $entity->get_id())
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
        if (! Arr::get($options, 'order_by')) {
            return $query;
        }

        // Can specify order as [['column', 'direction'], ['column', 'direction']].
        if (is_array($options['order_by'])) {
            foreach ($options['order_by'] as $order_by) {
                if (is_array($order_by)) {
                    $query->order_by(
                        Arr::get($order_by, 0),
                        Arr::get($order_by, 1)
                    );
                } else {
                    $query->order_by($order_by);
                }
            }
        } else { // Also support just a single ascending value
            return $query->order_by($options['order_by']);
        }

        return $query;
    }
}
