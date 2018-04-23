<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;

final class NullQueryType implements QueryTypeInterface
{
    use ParameterDefinitionCollectionTrait;

    public function getType()
    {
        return 'null';
    }

    public function getName()
    {
        return 'Invalid query type';
    }

    public function getForms()
    {
        return [];
    }

    public function hasForm($formName)
    {
        return false;
    }

    public function getForm($formName)
    {
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
