<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Stubs;

use Netgen\BlockManager\LayoutResolver\Rule\RuleInterface;

class Rule implements RuleInterface
{
    /**
     * @var int|string
     */
    protected $layoutId;

    /**
     * @var bool
     */
    protected $matches = true;

    /**
     * Constructor.
     *
     * @param int|string $layoutId
     * @param bool $matches
     */
    public function __construct($layoutId, $matches = true)
    {
        $this->layoutId = $layoutId;
        $this->matches = $matches;
    }

    /**
     * Returns the layout ID attached to this rule.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns the targets from this rule.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface[]
     */
    public function getTargets()
    {
    }

    /**
     * Returns if any of this rule targets match.
     *
     * @return bool
     */
    public function matches()
    {
        return $this->matches;
    }
}
