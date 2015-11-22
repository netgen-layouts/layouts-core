<?php

namespace Netgen\BlockManager\LayoutResolver\ConditionMatcher;

interface ConditionMatcherInterface
{
    /**
     * Returns the unique identifier of the condition this matcher matches.
     *
     * @return string
     */
    public function getConditionIdentifier();

    /**
     * Returns if this condition matches provided value identifier and values.
     *
     * @param string $valueIdentifier
     * @param array $values
     *
     * @return bool
     */
    public function matches($valueIdentifier, array $values);
}
