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

    /**
     * Returns the rule ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the status of the value.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns resolved layout.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Returns if the rule is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns if the rule is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns the rule priority.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Returns the rule comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Returns the target this rule applies to.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target[]
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Returns rule conditions.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Returns if the rule can be enabled.
     *
     * @return bool
     */
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
