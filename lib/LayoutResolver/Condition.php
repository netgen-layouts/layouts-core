<?php

namespace Netgen\BlockManager\LayoutResolver;

use Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface;

class Condition
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface
     */
    public $conditionMatcher;

    /**
     * @var string
     */
    public $valueIdentifier;

    /**
     * @var array
     */
    public $values;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface $conditionMatcher
     * @param string $valueIdentifier
     * @param array $values
     */
    public function __construct(ConditionMatcherInterface $conditionMatcher, $valueIdentifier, array $values)
    {
        $this->conditionMatcher = $conditionMatcher;
        $this->valueIdentifier = $valueIdentifier;
        $this->values = $values;
    }
}
