<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface;
use RuntimeException;

class ConditionMatcherRegistry implements ConditionMatcherRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface[]
     */
    protected $conditionMatchers = array();

    /**
     * Adds the condition matcher to the registry.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface $conditionMatcher
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
     * @throws \RuntimeException If condition matcher with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface
     */
    public function getConditionMatcher($identifier)
    {
        if (!isset($this->conditionMatchers[$identifier])) {
            throw new RuntimeException(
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
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface[]
     */
    public function getConditionMatchers()
    {
        return $this->conditionMatchers;
    }
}
