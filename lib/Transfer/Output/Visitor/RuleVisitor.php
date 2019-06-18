<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Rule value visitor.
 *
 * @see \Netgen\Layouts\API\Values\LayoutResolver\Rule
 */
final class RuleVisitor implements VisitorInterface
{
    use StatusStringTrait;

    public function accept($value): bool
    {
        return $value instanceof Rule;
    }

    /**
     * @param \Netgen\Layouts\API\Values\LayoutResolver\Rule $value
     * @param \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor $aggregateVisitor
     *
     * @return array
     */
    public function visit($value, AggregateVisitor $aggregateVisitor): array
    {
        $layout = $value->getLayout();

        return [
            'id' => $value->getId()->toString(),
            'status' => $this->getStatusString($value),
            'layout_id' => $layout instanceof Layout ? $layout->getId()->toString() : null,
            'is_enabled' => $value->isEnabled(),
            'priority' => $value->getPriority(),
            'comment' => $value->getComment(),
            'targets' => iterator_to_array($this->visitTargets($value, $aggregateVisitor)),
            'conditions' => iterator_to_array($this->visitConditions($value, $aggregateVisitor)),
        ];
    }

    /**
     * Visit the given $rule targets into hash representation.
     */
    private function visitTargets(Rule $rule, AggregateVisitor $aggregateVisitor): Generator
    {
        foreach ($rule->getTargets() as $target) {
            yield $target->getId()->toString() => $aggregateVisitor->visit($target);
        }
    }

    /**
     * Visit the given $rule conditions into hash representation.
     */
    private function visitConditions(Rule $rule, AggregateVisitor $aggregateVisitor): Generator
    {
        foreach ($rule->getConditions() as $condition) {
            yield $condition->getId()->toString() => $aggregateVisitor->visit($condition);
        }
    }
}
