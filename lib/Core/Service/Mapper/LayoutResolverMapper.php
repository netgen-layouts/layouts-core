<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Service\Mapper\Proxy\LazyLoadedCollection;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\Persistence\HandlerInterface;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;

final class LayoutResolverMapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\HandlerInterface
     */
    private $persistenceHandler;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    private $layoutMapper;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    private $conditionTypeRegistry;

    public function __construct(
        HandlerInterface $persistenceHandler,
        LayoutMapper $layoutMapper,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->layoutMapper = $layoutMapper;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Builds the API rule value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function mapRule(PersistenceRule $rule)
    {
        $handler = $this->persistenceHandler->getLayoutResolverHandler();

        $ruleData = array(
            'id' => $rule->id,
            'status' => $rule->status,
            'layout' => function () use ($rule) {
                try {
                    // Layouts used by rule are always in published status
                    $layout = $this->persistenceHandler->getLayoutHandler()->loadLayout(
                        $rule->layoutId,
                        Value::STATUS_PUBLISHED
                    );

                    return $this->layoutMapper->mapLayout($layout);
                } catch (NotFoundException $e) {
                    return null;
                }
            },
            'enabled' => $rule->enabled,
            'priority' => $rule->priority,
            'comment' => $rule->comment,
            'targets' => new LazyLoadedCollection(
                function () use ($handler, $rule) {
                    return array_map(
                        function (PersistenceTarget $target) {
                            return $this->mapTarget($target);
                        },
                        $handler->loadRuleTargets($rule)
                    );
                }
            ),
            'conditions' => new LazyLoadedCollection(
                function () use ($handler, $rule) {
                    return array_map(
                        function (PersistenceCondition $condition) {
                            return $this->mapCondition($condition);
                        },
                        $handler->loadRuleConditions($rule)
                    );
                }
            ),
        );

        return new Rule($ruleData);
    }

    /**
     * Builds the API target value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function mapTarget(PersistenceTarget $target)
    {
        $targetData = array(
            'id' => $target->id,
            'status' => $target->status,
            'ruleId' => $target->ruleId,
            'targetType' => $this->targetTypeRegistry->getTargetType(
                $target->type
            ),
            'value' => $target->value,
        );

        return new Target($targetData);
    }

    /**
     * Builds the API condition value from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function mapCondition(PersistenceCondition $condition)
    {
        $conditionData = array(
            'id' => $condition->id,
            'status' => $condition->status,
            'ruleId' => $condition->ruleId,
            'conditionType' => $this->conditionTypeRegistry->getConditionType(
                $condition->type
            ),
            'value' => $condition->value,
        );

        return new Condition($conditionData);
    }
}
