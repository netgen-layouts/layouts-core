<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Transfer\Serializer\Visitor;

/**
 * QueryType value visitor.
 *
 * @see \Netgen\BlockManager\Collection\QueryTypeInterface
 */
class QueryType extends Visitor
{
    public function accept($value)
    {
        return $value instanceof QueryTypeInterface;
    }

    public function visit($queryType, Visitor $subVisitor = null)
    {
        /* @var \Netgen\BlockManager\Collection\QueryTypeInterface $queryType */

        return $queryType->getType();
    }
}
