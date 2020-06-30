<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\EntityImporter;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Transfer\Input\EntityImporterInterface;
use Ramsey\Uuid\Uuid;
use function is_string;

final class RuleEntityImporter implements EntityImporterInterface
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry
     */
    private $conditionTypeRegistry;

    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistry $targetTypeRegistry,
        ConditionTypeRegistry $conditionTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    public function importEntity(array $data, bool $overwriteExisting): Value
    {
        $createStruct = $this->layoutResolverService->newRuleCreateStruct();

        $createStruct->enabled = $data['is_enabled'];
        $createStruct->comment = $data['comment'];
        $createStruct->priority = $data['priority'];
        $createStruct->layoutId = $data['layout_id'] !== null ?
            Uuid::fromString($data['layout_id']) :
            null;

        return $this->layoutResolverService->transaction(
            function () use ($createStruct, $data, $overwriteExisting): Rule {
                if ($overwriteExisting && is_string($data['id'] ?? null)) {
                    $uuid = Uuid::fromString($data['id']);

                    try {
                        $this->layoutResolverService->deleteRule(
                            $this->layoutResolverService->loadRule($uuid)
                        );
                    } catch (NotFoundException $e) {
                        // Do nothing
                    }
                }

                $ruleDraft = $this->layoutResolverService->createRule($createStruct);
                $this->createTargets($ruleDraft, $data['targets']);
                $this->createConditions($ruleDraft, $data['conditions']);

                return $this->layoutResolverService->publishRule($ruleDraft);
            }
        );
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
            $this->layoutResolverService->addCondition($rule, $conditionCreateStruct);
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
