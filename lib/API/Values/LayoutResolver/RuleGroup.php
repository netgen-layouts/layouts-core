<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Doctrine\Common\Collections\Collection;
use Netgen\Layouts\API\Values\LazyPropertyTrait;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class RuleGroup implements Value
{
    use HydratorTrait;
    use LazyPropertyTrait;
    use ValueStatusTrait;

    /**
     * The UUID of the root rule group.
     *
     * Only one root rule group can exist and all other rule groups are below it.
     */
    public const ROOT_UUID = '00000000-0000-0000-0000-000000000000';

    private UuidInterface $id;

    private ?UuidInterface $parentId;

    private string $name;

    private string $description;

    private bool $enabled;

    private int $priority;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \Netgen\Layouts\API\Values\LayoutResolver\Rule>
     */
    private Collection $rules;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \Netgen\Layouts\API\Values\LayoutResolver\Condition>
     */
    private Collection $conditions;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Returns the UUID of the parent rule group where this rule group is located.
     *
     * If rule group does not have a parent rule group, null is returned.
     */
    public function getParentId(): ?UuidInterface
    {
        return $this->parentId;
    }

    /**
     * Returns human readable name of the rule group.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return human readable description of the rule group.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Returns if the rule group is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Returns the rule group priority.
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Returns all the rules in the rule group.
     */
    public function getRules(): RuleList
    {
        return new RuleList($this->rules->toArray());
    }

    /**
     * Returns all conditions in the rule group.
     */
    public function getConditions(): ConditionList
    {
        return new ConditionList($this->conditions->toArray());
    }
}
