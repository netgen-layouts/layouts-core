<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Doctrine\Common\Collections\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LazyPropertyTrait;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Rule implements Value
{
    use HydratorTrait;
    use LazyPropertyTrait;
    use ValueStatusTrait;

    private UuidInterface $id;

    private UuidInterface $ruleGroupId;

    /**
     * @var \Netgen\Layouts\API\Values\Layout\Layout|\Closure|null
     */
    private $layout;

    private bool $enabled;

    private int $priority;

    private string $description;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \Netgen\Layouts\API\Values\LayoutResolver\Target>
     */
    private Collection $targets;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \Netgen\Layouts\API\Values\LayoutResolver\Condition>
     */
    private Collection $conditions;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Returns the UUID of the rule group where this rule belongs.
     */
    public function getRuleGroupId(): UuidInterface
    {
        return $this->ruleGroupId;
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
     *
     * @deprecated use self::getDescription
     */
    public function getComment(): string
    {
        return $this->description;
    }

    /**
     * Returns the rule description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Returns all the targets in the rule.
     */
    public function getTargets(): TargetList
    {
        return new TargetList($this->targets->toArray());
    }

    /**
     * Returns all conditions in the rule.
     */
    public function getConditions(): ConditionList
    {
        return new ConditionList($this->conditions->toArray());
    }
}
