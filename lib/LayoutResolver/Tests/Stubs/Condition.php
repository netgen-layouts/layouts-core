<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Stubs;

use Netgen\BlockManager\LayoutResolver\Rule\Condition as BaseCondition;
use Netgen\BlockManager\LayoutResolver\Rule\TargetInterface;

class Condition extends BaseCondition
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
     * Returns if this condition matches.
     *
     * @return bool
     */
    public function matches()
    {
        return $this->matches;
    }

    /**
     * Returns if this condition supports the given target.
     *
     * @param \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface
     *
     * @return bool
     */
    public function supports(TargetInterface $target)
    {
        return $this->supports;
    }
}
