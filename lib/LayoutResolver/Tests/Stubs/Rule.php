<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Stubs;

use Netgen\BlockManager\LayoutResolver\Rule\RuleInterface;
use Netgen\BlockManager\API\Values\Page\Layout;

class Rule implements RuleInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Layout
     */
    protected $layout;

    /**
     * @var bool
     */
    protected $matches = true;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param bool $matches
     */
    public function __construct(Layout $layout, $matches = true)
    {
        $this->layout = $layout;
        $this->matches = $matches;
    }

    /**
     * Returns the layout attached to this rule.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout()
    {
        return $this->layout;
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
