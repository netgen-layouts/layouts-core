<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Transfer\EntityType;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Rule value visitor.
 *
 * @see \Netgen\Layouts\API\Values\LayoutResolver\Rule
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\LayoutResolver\Rule>
 */
final class RuleVisitor implements VisitorInterface
{
    public function accept(object $value): bool
    {
        return $value instanceof Rule;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            '__type' => EntityType::Rule->value,
            'id' => $value->id->toString(),
            'status' => $value->status->value,
            'rule_group_id' => $value->ruleGroupId->toString(),
            'layout_id' => $value->layout?->id->toString(),
            'is_enabled' => $value->isEnabled,
            'priority' => $value->priority,
            'description' => $value->description,
            'targets' => [...$this->visitTargets($value, $outputVisitor)],
            'conditions' => [...$this->visitConditions($value, $outputVisitor)],
        ];
    }

    /**
     * Visit the given $rule targets into hash representation.
     *
     * @return iterable<array<string, mixed>>
     */
    private function visitTargets(Rule $rule, OutputVisitor $outputVisitor): iterable
    {
        foreach ($rule->targets as $target) {
            yield $outputVisitor->visit($target);
        }
    }

    /**
     * Visit the given $rule conditions into hash representation.
     *
     * @return iterable<array<string, mixed>>
     */
    private function visitConditions(Rule $rule, OutputVisitor $outputVisitor): iterable
    {
        foreach ($rule->conditions as $condition) {
            yield $outputVisitor->visit($condition);
        }
    }
}
