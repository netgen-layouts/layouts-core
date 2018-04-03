<?php

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\Core\Values\LazyLoadedPropertyTrait;
use Netgen\BlockManager\Core\Values\Value;

final class Rule extends Value implements APIRule
{
    use LazyLoadedPropertyTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    protected $layout;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $targets;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $conditions;

    public function getId()
    {
        return $this->id;
    }

    public function getLayout()
    {
        return $this->getLazyLoadedProperty($this->layout);
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
        if (!$this->isPublished()) {
            return false;
        }

        if (!$this->layout instanceof Layout) {
            return false;
        }

        return !empty($this->targets);
    }
}
