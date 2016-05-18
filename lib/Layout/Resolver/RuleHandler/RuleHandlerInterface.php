<?php

namespace Netgen\BlockManager\Layout\Resolver\RuleHandler;

interface RuleHandlerInterface
{
    /**
     * Loads rules with target identifier and provided values.
     *
     * @param string $targetIdentifier
     * @param array $values
     *
     * @return array
     */
    public function loadRules($targetIdentifier, array $values);
}
