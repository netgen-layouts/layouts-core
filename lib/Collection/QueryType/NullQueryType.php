<?php

namespace Netgen\BlockManager\Collection\QueryType;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;

final class NullQueryType implements QueryTypeInterface
{
    use ParameterDefinitionCollectionTrait;

    /**
     * @var string
     */
    private $queryType;

    /**
     * @param string $queryType
     */
    public function __construct($queryType)
    {
        $this->queryType = $queryType;
    }

    public function getType()
    {
        return $this->queryType;
    }

    public function isEnabled()
    {
        return true;
    }

    public function getName()
    {
        return 'Invalid query type';
    }

    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return [];
    }

    public function getCount(Query $query)
    {
        return 0;
    }

    public function isContextual(Query $query)
    {
        return false;
    }
}
