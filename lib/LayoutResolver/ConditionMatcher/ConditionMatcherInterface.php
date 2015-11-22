<?php

namespace Netgen\BlockManager\LayoutResolver\ConditionMatcher;

interface ConditionMatcherInterface
{
    /**
     * Returns the unique identifier of this condition matcher.
     *
     * @return string
     */
    public function getIdentifier();

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
