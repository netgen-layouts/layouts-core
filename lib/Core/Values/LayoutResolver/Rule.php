<?php

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\ValueObject;

class Rule extends ValueObject implements APIRule
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    protected $layout;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Target[]
     */
    protected $targets = array();

    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Condition[]
     */
    protected $conditions = array();

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function isPublished()
    {
        return $this->published;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getTargets()
    {
        return $this->targets;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function canBeEnabled()
    {
        if (!$this->published) {
            return false;
        }

        if (!$this->layout instanceof Layout) {
            return false;
        }

        return !empty($this->targets);
    }
}
