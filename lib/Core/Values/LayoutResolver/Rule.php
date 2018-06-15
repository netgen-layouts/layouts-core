<?php

declare(strict_types=1);

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
     * @var \Netgen\BlockManager\API\Values\Layout\Layout|null
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

    public function getTargets(): array
    {
        return $this->targets->toArray();
    }

    public function getConditions(): array
    {
        return $this->conditions->toArray();
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
