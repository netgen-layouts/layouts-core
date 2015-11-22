<?php

namespace Netgen\BlockManager\LayoutResolver\ConditionMatcher;

interface RegistryInterface
{
    /**
     * Adds the condition matcher to the registry.
     *
     * @param \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface $conditionMatcher
     */
    public function addConditionMatcher(ConditionMatcherInterface $conditionMatcher);

    /**
     * Returns the condition matcher from the registry.
     *
     * @param string $identifier
     *
     * @throws \InvalidArgumentException If condition matcher with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface
     */
    public function getConditionMatcher($identifier);

    /**
     * Returns all condition matchers from the registry.
     *
     * @return \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface[]
     */
    public function getConditionMatchers();
}
