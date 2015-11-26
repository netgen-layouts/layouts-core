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
     * Returns if this condition matches provided parameters.
     *
     * @param array $parameters
     *
     * @return bool
     */
    public function matches(array $parameters);
}
