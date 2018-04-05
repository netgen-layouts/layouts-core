<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;

final class QueryTypeWithParameterDefinitions implements QueryTypeInterface
{
    use ParameterCollectionTrait;

    public function __construct(array $parameterDefinitions)
    {
        $this->parameterDefinitions = $parameterDefinitions;
    }

    public function getType()
    {
    }

    public function getName()
    {
    }

    public function getForms()
    {
    }

    public function hasForm($formName)
    {
    }

    public function getForm($formName)
    {
    }

    public function getValues(Query $query, $offset = 0, $limit = null)
    {
    }

    public function getCount(Query $query)
    {
    }

    public function isContextual(Query $query)
    {
    }
}
