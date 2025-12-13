<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\EntityHandler;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Transfer\EntityHandlerInterface;
use Symfony\Component\Uid\Uuid;

final class RuleEntityHandler implements EntityHandlerInterface
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
        private TargetTypeRegistry $targetTypeRegistry,
        private ConditionTypeRegistry $conditionTypeRegistry,
    ) {}

    public function loadEntity(Uuid $uuid): Rule
    {
        return $this->layoutResolverService->loadRule($uuid);
    }

    public function entityExists(Uuid $uuid): bool
    {
        return $this->layoutResolverService->ruleExists($uuid);
    }

    public function deleteEntity(Uuid $uuid): void
    {
        $this->layoutResolverService->deleteRule(
            $this->layoutResolverService->loadRule($uuid),
        );
    }

    public function importEntity(array $data, bool $keepUuid): Rule
    {
        $createStruct = $this->layoutResolverService->newRuleCreateStruct();

        $createStruct->isEnabled = $data['is_enabled'];
        $createStruct->description = $data['description'];
        $createStruct->priority = $data['priority'];
        $createStruct->layoutId = $data['layout_id'] !== null ?
            Uuid::fromString($data['layout_id']) :
            null;

        if ($keepUuid) {
            $createStruct->uuid = Uuid::fromString($data['id']);
        }

        $ruleDraft = $this->layoutResolverService->createRule(
            $createStruct,
            $this->layoutResolverService->loadRuleGroup(
                Uuid::fromString($data['rule_group_id']),
            ),
        );

        $this->createTargets($ruleDraft, $data['targets']);
        $this->createConditions($ruleDraft, $data['conditions']);

        return $this->layoutResolverService->publishRule($ruleDraft);
    }

    /**
     * Create targets in the given $rule from the given $targetsData.
     *
     * @param array<string, mixed> $targetsData
     */
    private function createTargets(Rule $rule, array $targetsData): void
    {
        foreach ($targetsData as $targetData) {
            $targetCreateStruct = $this->buildTargetCreateStruct($targetData);
            $this->layoutResolverService->addTarget($rule, $targetCreateStruct);
        }
    }

    /**
     * Builds the target create struct from provided $targetData.
     *
     * @param array<string, mixed> $targetData
     */
    private function buildTargetCreateStruct(array $targetData): TargetCreateStruct
    {
        $targetType = $this->targetTypeRegistry->getTargetType($targetData['type']);

        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct($targetType::getType());
        $targetCreateStruct->value = $targetType->import($targetData['value']);

        return $targetCreateStruct;
    }

    /**
     * Create conditions in the given $rule from the given $conditionsData.
     *
     * @param array<string, mixed> $conditionsData
     */
    private function createConditions(Rule $rule, array $conditionsData): void
    {
        foreach ($conditionsData as $conditionData) {
            $conditionCreateStruct = $this->buildConditionCreateStruct($conditionData);
            $this->layoutResolverService->addRuleCondition($rule, $conditionCreateStruct);
        }
    }

    /**
     * Builds the condition create struct from provided $conditionData.
     *
     * @param array<string, mixed> $conditionData
     */
    private function buildConditionCreateStruct(array $conditionData): ConditionCreateStruct
    {
        $conditionType = $this->conditionTypeRegistry->getConditionType($conditionData['type']);

        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct($conditionType::getType());
        $conditionCreateStruct->value = $conditionType->import($conditionData['value']);

        return $conditionCreateStruct;
    }
}
