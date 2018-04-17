<?php

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\Core\Values\LazyPropertyTrait;
use Netgen\BlockManager\Core\Values\Value;

final class Rule extends Value implements APIRule
{
    use LazyPropertyTrait;

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

    public function __construct(array $properties = [])
    {
        parent::__construct($properties);

        if ($this->targets === null) {
            $this->targets = new ArrayCollection();
        }

        if ($this->conditions === null) {
            $this->conditions = new ArrayCollection();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLayout()
    {
        return $this->getLazyProperty($this->layout);
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
        return $this->targets->toArray();
    }

    public function getConditions()
    {
        return $this->conditions->toArray();
    }

    public function canBeEnabled()
    {
        if (!$this->isPublished()) {
            return false;
        }

        if (!$this->layout instanceof Layout) {
            return false;
        }

        return $this->targets->count() > 0;
    }
}
