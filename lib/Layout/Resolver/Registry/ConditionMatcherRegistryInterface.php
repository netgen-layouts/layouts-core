<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface;

interface ConditionMatcherRegistryInterface
{
    /**
     * Adds the condition matcher to the registry.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface $conditionMatcher
     */
    public function addConditionMatcher(ConditionMatcherInterface $conditionMatcher);

    /**
     * Returns the condition matcher from the registry.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If condition matcher with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface
     */
    public function getConditionMatcher($identifier);

    /**
     * Returns all condition matchers from the registry.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionMatcher\ConditionMatcherInterface[]
     */
    public function getConditionMatchers();
}
