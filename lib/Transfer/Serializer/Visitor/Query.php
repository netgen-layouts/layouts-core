<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\API\Values\Collection\Query as QueryValue;
use Netgen\BlockManager\Transfer\Serializer\Visitor;
use RuntimeException;

/**
 * Query value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Query
 */
class Query extends Visitor
{
    public function accept($value)
    {
        return $value instanceof QueryValue;
    }

    public function visit($query, Visitor $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Query $query */

        return array(
            'id' => $query->getId(),
            'status' => $this->getStatusString($query),
            'is_published' => $query->isPublished(),
            'is_contextual' => $query->isContextual(),
            'internal_limit' => $query->getInternalLimit(),
            'parameters' => $this->visitParameterValues($query, $subVisitor),
            'query_type' => $query->getQueryType()->getType(),
        );
    }

    /**
     * Visit the given $query parameters into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $subVisitor
     *
     * @return array
     */
    private function visitParameterValues(QueryValue $query, Visitor $subVisitor)
    {
        $parameterValues = $query->getParameters();
        if (empty($parameterValues)) {
            return null;
        }

        $hash = array();

        foreach ($parameterValues as $parameterValue) {
            $hash[$parameterValue->getName()] = $subVisitor->visit($parameterValue);
        }

        return $hash;
    }
}
