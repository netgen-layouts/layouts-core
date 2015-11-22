<?php

namespace Netgen\BlockManager\LayoutResolver;

class Rule
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var \Netgen\BlockManager\LayoutResolver\Condition[]
     */
    public $conditions;

    /**
     * Constructor.
     *
     * @param int|string $layoutId
     * @param \Netgen\BlockManager\LayoutResolver\Condition[] $conditions
     */
    public function __construct($layoutId, array $conditions = array())
    {
        $this->layoutId = $layoutId;
        $this->conditions = $conditions;
    }
}
