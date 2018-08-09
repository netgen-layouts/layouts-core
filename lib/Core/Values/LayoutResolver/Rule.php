<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionList;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetList;
use Netgen\BlockManager\Core\Values\LazyPropertyTrait;
use Netgen\BlockManager\Core\Values\ValueStatusTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Rule implements APIRule
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

    public function getId()
    {
        return $this->id;
    }

    public function getLayout(): ?Layout
    {
        return $this->getLazyProperty($this->layout);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getTargets(): TargetList
    {
        return new TargetList($this->targets->toArray());
    }

    public function getConditions(): ConditionList
    {
        return new ConditionList($this->conditions->toArray());
    }

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
