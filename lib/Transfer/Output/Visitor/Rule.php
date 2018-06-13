<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Layout\Layout as LayoutValue;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as RuleValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Rule value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\LayoutResolver\Rule
 */
final class Rule extends Visitor
{
    public function accept($value)
    {
        return $value instanceof RuleValue;
    }

    public function visit($rule, VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /** @var \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule */
        $layout = $rule->getLayout();

        return [
            'id' => $rule->getId(),
            'status' => $this->getStatusString($rule),
            'layout_id' => $layout instanceof LayoutValue ? $layout->getId() : null,
            'is_enabled' => $rule->isEnabled(),
            'priority' => $rule->getPriority(),
            'comment' => $rule->getComment(),
            'targets' => $this->visitTargets($rule, $subVisitor),
            'conditions' => $this->visitConditions($rule, $subVisitor),
        ];
    }

    /**
     * Visit the given $rule targets into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\Transfer\Output\VisitorInterface $subVisitor
     *
     * @return array
     */
    private function visitTargets(RuleValue $rule, VisitorInterface $subVisitor)
    {
        $hash = [];

        foreach ($rule->getTargets() as $target) {
            $hash[$target->getId()] = $subVisitor->visit($target);
        }

        return $hash;
    }

    /**
     * Visit the given $rule conditions into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\Transfer\Output\VisitorInterface $subVisitor
     *
     * @return array
     */
    private function visitConditions(RuleValue $rule, VisitorInterface $subVisitor)
    {
        $hash = [];

        foreach ($rule->getConditions() as $condition) {
            $hash[$condition->getId()] = $subVisitor->visit($condition);
        }

        return $hash;
    }
}
