<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Stubs;

use Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface;
use Netgen\BlockManager\LayoutResolver\Target\TargetInterface;

class ConditionMatcher implements ConditionMatcherInterface
{
    /**
     * @var bool
     */
    protected $matches = true;

    /**
     * @var bool
     */
    protected $supports = true;

    /**
     * Constructor.
     *
     * @param bool $matches
     * @param bool $supports
     */
    public function __construct($matches = true, $supports = true)
    {
        $this->matches = $matches;
        $this->supports = $supports;
    }

    /**
     * Returns the unique identifier of the condition this matcher matches.
     *
     * @return string
     */
    public function getConditionIdentifier()
    {
        return 'condition';
    }

    /**
     * Returns if this condition matches provided value identifier and values.
     *
     * @param string $valueIdentifier
     * @param array $values
     *
     * @return bool
     */
    public function matches($valueIdentifier, array $values)
    {
        return $this->matches;
    }

    /**
     * Returns if this condition supports the given target.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Target\TargetInterface
     *
     * @return bool
     */
    public function supports(TargetInterface $target)
    {
        return $this->supports;
    }
}
