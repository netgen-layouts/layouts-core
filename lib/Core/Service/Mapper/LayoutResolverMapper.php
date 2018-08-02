<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Core\Values\LazyCollection;
use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use Netgen\BlockManager\Exception\Layout\TargetTypeException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;

final class LayoutResolverMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface
     */
    private $layoutResolverHandler;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    private $conditionTypeRegistry;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
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
    public function mapRule(PersistenceRule $rule): APIRule
    {
        $ruleData = [
            'id' => $rule->id,
            'status' => $rule->status,
            'layout' => function () use ($rule): ?Layout {
                try {
                    // Layouts used by rule are always in published status
                    return $rule->layoutId !== null ? $this->layoutService->loadLayout($rule->layoutId) : null;
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
                        function (PersistenceTarget $target): APITarget {
                            return $this->mapTarget($target);
                        },
                        $this->layoutResolverHandler->loadRuleTargets($rule)
                    );
                }
            ),
            'conditions' => new LazyCollection(
                function () use ($rule): array {
                    return array_map(
                        function (PersistenceCondition $condition): APICondition {
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
    public function mapTarget(PersistenceTarget $target): APITarget
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
            'ruleId' => $target->ruleId,
            'targetType' => $targetType,
            'value' => $target->value,
        ];

        return Target::fromArray($targetData);
    }

    /**
     * Builds the API condition value from persistence one.
     */
    public function mapCondition(PersistenceCondition $condition): APICondition
    {
        try {
            $conditionType = $this->conditionTypeRegistry->getConditionType(
                $condition->type
            );
        } catch (ConditionTypeException $e) {
            $conditionType = new NullConditionType($condition->type);
        }

        $conditionData = [
            'id' => $condition->id,
            'status' => $condition->status,
            'ruleId' => $condition->ruleId,
            'conditionType' => $conditionType,
            'value' => $condition->value,
        ];

        return Condition::fromArray($conditionData);
    }
}
