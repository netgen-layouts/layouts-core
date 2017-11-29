<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Collection\Query as QueryValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;

/**
 * Query value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Query
 */
final class Query extends Visitor
{
    public function accept($value)
    {
        return $value instanceof QueryValue;
    }

    public function visit($query, Visitor $subVisitor = null, array $context = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Query $query */

        return array(
            'id' => $query->getId(),
            'status' => $this->getStatusString($query),
            'is_translatable' => $query->isTranslatable(),
            'is_always_available' => $query->isAlwaysAvailable(),
            'main_locale' => $query->getMainLocale(),
            'available_locales' => $query->getAvailableLocales(),
            'parameters' => $this->visitParameterValues($query, $subVisitor),
            'query_type' => $query->getQueryType()->getType(),
        );
    }

    /**
     * Visit the given $query parameters into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\Transfer\Output\Visitor $subVisitor
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
