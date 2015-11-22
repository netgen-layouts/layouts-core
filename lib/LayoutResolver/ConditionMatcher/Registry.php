<?php

namespace Netgen\BlockManager\LayoutResolver\ConditionMatcher;

use InvalidArgumentException;

class Registry implements RegistryInterface
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface[]
     */
    protected $conditionMatchers = array();

    /**
     * Adds the condition matcher to the registry.
     *
     * @param \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface $conditionMatcher
     */
    public function addConditionMatcher(ConditionMatcherInterface $conditionMatcher)
    {
        $this->conditionMatchers[$conditionMatcher->getConditionIdentifier()] = $conditionMatcher;
    }

    /**
     * Returns the condition matcher from the registry.
     *
     * @param string $identifier
     *
     * @throws \InvalidArgumentException If condition matcher with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface
     */
    public function getConditionMatcher($identifier)
    {
        if (!isset($this->conditionMatchers[$identifier])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Condition matcher with "%s" identifier does not exist.',
                    $identifier
                )
            );
        }

        return $this->conditionMatchers[$identifier];
    }

    /**
     * Returns all condition matchers from the registry.
     *
     * @return \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface[]
     */
    public function getConditionMatchers()
    {
        return $this->conditionMatchers;
    }
}
