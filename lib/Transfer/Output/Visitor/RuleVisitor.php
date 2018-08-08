<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Generator;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\StatusStringTrait;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Rule value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\LayoutResolver\Rule
 */
final class RuleVisitor implements VisitorInterface
{
    use StatusStringTrait;

    public function accept($value): bool
    {
        return $value instanceof Rule;
    }

    public function visit($rule, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /** @var \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule */
        $layout = $rule->getLayout();

        return [
            'id' => $rule->getId(),
            'status' => $this->getStatusString($rule),
            'layout_id' => $layout instanceof Layout ? $layout->getId() : null,
            'is_enabled' => $rule->isEnabled(),
            'priority' => $rule->getPriority(),
            'comment' => $rule->getComment(),
            'targets' => iterator_to_array($this->visitTargets($rule, $subVisitor)),
            'conditions' => iterator_to_array($this->visitConditions($rule, $subVisitor)),
        ];
    }

    /**
     * Visit the given $rule targets into hash representation.
     */
    private function visitTargets(Rule $rule, VisitorInterface $subVisitor): Generator
    {
        foreach ($rule->getTargets() as $target) {
            yield $target->getId() => $subVisitor->visit($target);
        }
    }

    /**
     * Visit the given $rule conditions into hash representation.
     */
    private function visitConditions(Rule $rule, VisitorInterface $subVisitor): Generator
    {
        foreach ($rule->getConditions() as $condition) {
            yield $condition->getId() => $subVisitor->visit($condition);
        }
    }
}
