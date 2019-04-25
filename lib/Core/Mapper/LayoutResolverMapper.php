<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LazyCollection;
use Netgen\Layouts\Exception\Layout\ConditionTypeException;
use Netgen\Layouts\Exception\Layout\TargetTypeException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType;
use Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Ramsey\Uuid\Uuid;

final class LayoutResolverMapper
{
    /**
     * @var \Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface
     */
    private $layoutResolverHandler;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    private $conditionTypeRegistry;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(
        LayoutResolverHandlerInterface $layoutResolverHandler,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry,
        LayoutService $layoutService
    ) {
        $this->layoutResolverHandler = $layoutResolverHandler;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
        $this->layoutService = $layoutService;
    }

    /**
     * Builds the API rule value from persistence one.
     */
    public function mapRule(PersistenceRule $rule): Rule
    {
        $ruleData = [
            'id' => Uuid::fromString($rule->uuid),
            'status' => $rule->status,
            'layout' => function () use ($rule): ?Layout {
                try {
                    // Layouts used by rule are always in published status
                    return $rule->layoutUuid !== null ?
                        $this->layoutService->loadLayout(Uuid::fromString($rule->layoutUuid)) :
                        null;
                } catch (NotFoundException $e) {
                    return null;
                }
            },
            'enabled' => $rule->enabled,
            'priority' => $rule->priority,
            'comment' => $rule->comment,
            'targets' => new LazyCollection(
                function () use ($rule): array {
                    return array_map(
                        function (PersistenceTarget $target): Target {
                            return $this->mapTarget($target);
                        },
                        $this->layoutResolverHandler->loadRuleTargets($rule)
                    );
                }
            ),
            'conditions' => new LazyCollection(
                function () use ($rule): array {
                    return array_map(
                        function (PersistenceCondition $condition): Condition {
                            return $this->mapCondition($condition);
                        },
                        $this->layoutResolverHandler->loadRuleConditions($rule)
                    );
                }
            ),
        ];

        return Rule::fromArray($ruleData);
    }

    /**
     * Builds the API target value from persistence one.
     */
    public function mapTarget(PersistenceTarget $target): Target
    {
        try {
            $targetType = $this->targetTypeRegistry->getTargetType(
                $target->type
            );
        } catch (TargetTypeException $e) {
            $targetType = new NullTargetType();
        }

        $targetData = [
            'id' => $target->id,
            'status' => $target->status,
            'ruleId' => Uuid::fromString($target->ruleUuid),
            'targetType' => $targetType,
            'value' => $target->value,
        ];

        return Target::fromArray($targetData);
    }

    /**
     * Builds the API condition value from persistence one.
     */
    public function mapCondition(PersistenceCondition $condition): Condition
    {
        try {
            $conditionType = $this->conditionTypeRegistry->getConditionType(
                $condition->type
            );
        } catch (ConditionTypeException $e) {
            $conditionType = new NullConditionType();
        }

        $conditionData = [
            'id' => $condition->id,
            'status' => $condition->status,
            'ruleId' => Uuid::fromString($condition->ruleUuid),
            'conditionType' => $conditionType,
            'value' => $condition->value,
        ];

        return Condition::fromArray($conditionData);
    }
}
