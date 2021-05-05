<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\EntityHandler;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Transfer\EntityHandlerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class RuleEntityHandler implements EntityHandlerInterface
{
    private LayoutResolverService $layoutResolverService;

    private TargetTypeRegistry $targetTypeRegistry;

    private ConditionTypeRegistry $conditionTypeRegistry;

    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistry $targetTypeRegistry,
        ConditionTypeRegistry $conditionTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    public function loadEntity(UuidInterface $uuid): Rule
    {
        return $this->layoutResolverService->loadRule($uuid);
    }

    public function entityExists(UuidInterface $uuid): bool
    {
        return $this->layoutResolverService->ruleExists($uuid);
    }

    public function deleteEntity(UuidInterface $uuid): void
    {
        $this->layoutResolverService->deleteRule(
            $this->layoutResolverService->loadRule($uuid),
        );
    }

    public function importEntity(array $data, bool $keepUuid): Rule
    {
        $createStruct = $this->layoutResolverService->newRuleCreateStruct();

        $createStruct->enabled = $data['is_enabled'];
        $createStruct->description = $data['description'] ?? $data['comment'] ?? '';
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
                Uuid::fromString($data['rule_group_id'] ?? RuleGroup::ROOT_UUID),
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
