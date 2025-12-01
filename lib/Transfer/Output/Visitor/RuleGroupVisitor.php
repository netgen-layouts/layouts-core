<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Transfer\EntityType;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Rule group value visitor.
 *
 * @see \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\LayoutResolver\RuleGroup>
 */
final class RuleGroupVisitor implements VisitorInterface
{
    use StatusStringTrait;

    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    public function accept(object $value): bool
    {
        return $value instanceof RuleGroup;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            '__type' => EntityType::RuleGroup->value,
            'id' => $value->id->toString(),
            'status' => $this->getStatusString($value),
            'parent_id' => $value->parentId?->toString(),
            'name' => $value->name,
            'description' => $value->description,
            'is_enabled' => $value->isEnabled,
            'priority' => $value->priority,
            'groups' => [...$this->visitSubGroups($value, $outputVisitor)],
            'rules' => [...$this->visitRules($value, $outputVisitor)],
            'conditions' => [...$this->visitConditions($value, $outputVisitor)],
        ];
    }

    /**
     * Visit the given $ruleGroup subgroups into hash representation.
     *
     * @return iterable<array<string, mixed>>
     */
    private function visitSubGroups(RuleGroup $ruleGroup, OutputVisitor $outputVisitor): iterable
    {
        foreach ($this->layoutResolverService->loadRuleGroups($ruleGroup) as $subGroup) {
            yield $outputVisitor->visit($subGroup);
        }
    }

    /**
     * Visit the given $ruleGroup rules into hash representation.
     *
     * @return iterable<array<string, mixed>>
     */
    private function visitRules(RuleGroup $ruleGroup, OutputVisitor $outputVisitor): iterable
    {
        foreach ($ruleGroup->rules as $rule) {
            yield $outputVisitor->visit($rule);
        }
    }

    /**
     * Visit the given $ruleGroup conditions into hash representation.
     *
     * @return iterable<array<string, mixed>>
     */
    private function visitConditions(RuleGroup $ruleGroup, OutputVisitor $outputVisitor): iterable
    {
        foreach ($ruleGroup->conditions as $condition) {
            yield $outputVisitor->visit($condition);
        }
    }
}
