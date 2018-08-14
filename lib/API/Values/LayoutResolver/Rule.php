<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LazyPropertyTrait;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\ValueStatusTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Rule implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;
    use LazyPropertyTrait;

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout|null
     */
    private $layout;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $targets;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $conditions;

    public function __construct()
    {
        $this->targets = $this->targets ?? new ArrayCollection();
        $this->conditions = $this->conditions ?? new ArrayCollection();
    }

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
     * Returns the layout mapped to this rule.
     */
    public function getLayout(): ?Layout
    {
        return $this->getLazyProperty($this->layout);
    }

    /**
     * Returns if the rule is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Returns the rule priority.
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Returns the rule comment.
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Returns all the targets in the rule.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetList
     */
    public function getTargets(): TargetList
    {
        return new TargetList($this->targets->toArray());
    }

    /**
     * Returns all conditions in the rule.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionList
     */
    public function getConditions(): ConditionList
    {
        return new ConditionList($this->conditions->toArray());
    }

    /**
     * Returns if the rule can be enabled.
     *
     * Rule can be enabled if it is published and has a mapped layout and at least one target.
     */
    public function canBeEnabled(): bool
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
