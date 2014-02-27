<?php

namespace Synapse\Db;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Sql;

/**
 * Factory for creating TableGateway objects
 */
class TableGatewayFactory
{
    /**
     * Database adapter
     *
     * @var Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * Set injected adapter as property
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Return new TableGateway object instantiated with given parameters
     *
     * @param  string $table
     * @param  AdapterInterface $adapter
     * @param  Feature\AbstractFeature|Feature\FeatureSet|Feature\AbstractFeature[] $features
     * @param  ResultSetInterface $resultSetPrototype
     * @param  Sql $sql
     * @return TableGateway
     */
    public function factory($table, $features = null, ResultSetInterface $resultSetPrototype = null, Sql $sql = null)
    {
        return new TableGateway($table, $this->adapter, $features, $resultSetPrototype, $sql);
    }
}
