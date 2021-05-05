<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\EntityHandler;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Transfer\EntityHandlerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class RuleGroupEntityHandler implements EntityHandlerInterface
{
    private LayoutResolverService $layoutResolverService;

    private RuleEntityHandler $ruleEntityHandler;

    private ConditionTypeRegistry $conditionTypeRegistry;

    public function __construct(
        LayoutResolverService $layoutResolverService,
        RuleEntityHandler $ruleEntityHandler,
        ConditionTypeRegistry $conditionTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->ruleEntityHandler = $ruleEntityHandler;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    public function loadEntity(UuidInterface $uuid): RuleGroup
    {
        return $this->layoutResolverService->loadRuleGroup($uuid);
    }

    public function entityExists(UuidInterface $uuid): bool
    {
        return $this->layoutResolverService->ruleGroupExists($uuid);
    }

    public function deleteEntity(UuidInterface $uuid): void
    {
        $this->layoutResolverService->deleteRuleGroup(
            $this->layoutResolverService->loadRuleGroup($uuid),
        );
    }

    public function importEntity(array $data, bool $keepUuid): RuleGroup
    {
        $createStruct = $this->layoutResolverService->newRuleGroupCreateStruct($data['name']);

        $createStruct->description = $data['description'];
        $createStruct->enabled = $data['is_enabled'];
        $createStruct->priority = $data['priority'];

        if ($keepUuid) {
            $createStruct->uuid = Uuid::fromString($data['id']);
        }

        $ruleGroupDraft = $this->layoutResolverService->createRuleGroup(
            $createStruct,
            $this->layoutResolverService->loadRuleGroup(
                Uuid::fromString($data['parent_id'] ?? RuleGroup::ROOT_UUID),
            ),
        );

        $this->createConditions($ruleGroupDraft, $data['conditions']);

        $ruleGroup = $this->layoutResolverService->publishRuleGroup($ruleGroupDraft);

        // subgroups and rules can be imported only below published groups
        $this->createSubGroups($ruleGroup, $data['groups'], $keepUuid);
        $this->createRules($ruleGroup, $data['rules'], $keepUuid);

        return $ruleGroup;
    }

    /**
     * Create subgroups in the given $ruleGroup from the given $ruleGroupsData.
     *
     * @param array<string, mixed> $ruleGroupsData
     */
    private function createSubGroups(RuleGroup $ruleGroup, array $ruleGroupsData, bool $keepUuid): void
    {
        foreach ($ruleGroupsData as $ruleGroupData) {
            $ruleGroupData['parent_id'] = $ruleGroup->getId()->toString();
            $this->importEntity($ruleGroupData, $keepUuid);
        }
    }

    /**
     * Create rules in the given $ruleGroup from the given $rulesData.
     *
     * @param array<string, mixed> $rulesData
     */
    private function createRules(RuleGroup $ruleGroup, array $rulesData, bool $keepUuid): void
    {
        foreach ($rulesData as $ruleData) {
            $ruleData['rule_group_id'] = $ruleGroup->getId()->toString();
            $this->ruleEntityHandler->importEntity($ruleData, $keepUuid);
        }
    }

    /**
     * Create conditions in the given $ruleGroup from the given $conditionsData.
     *
     * @param array<string, mixed> $conditionsData
     */
    private function createConditions(RuleGroup $ruleGroup, array $conditionsData): void
    {
        foreach ($conditionsData as $conditionData) {
            $conditionCreateStruct = $this->buildConditionCreateStruct($conditionData);
            $this->layoutResolverService->addRuleGroupCondition($ruleGroup, $conditionCreateStruct);
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
