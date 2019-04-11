<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\RuntimeException;
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

    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /** @var \Netgen\Layouts\API\Values\LayoutResolver\Rule $value */
        $layout = $value->getLayout();

        return [
            'id' => $value->getId(),
            'status' => $this->getStatusString($value),
            'layout_id' => $layout instanceof Layout ? $layout->getId() : null,
            'is_enabled' => $value->isEnabled(),
            'priority' => $value->getPriority(),
            'comment' => $value->getComment(),
            'targets' => iterator_to_array($this->visitTargets($value, $subVisitor)),
            'conditions' => iterator_to_array($this->visitConditions($value, $subVisitor)),
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
