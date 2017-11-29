<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as RuleValue;
use Netgen\BlockManager\Transfer\Output\Visitor;
use RuntimeException;

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

    public function visit($rule, Visitor $subVisitor = null, array $context = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule */

        $layout = $rule->getLayout();

        return array(
            'id' => $rule->getId(),
            'status' => $this->getStatusString($rule),
            'layout_id' => $layout instanceof Layout ? $layout->getId() : null,
            'is_enabled' => $rule->isEnabled(),
            'priority' => $rule->getPriority(),
            'comment' => $rule->getComment(),
            'targets' => $this->visitTargets($rule, $subVisitor),
            'conditions' => $this->visitConditions($rule, $subVisitor),
        );
    }

    /**
     * Visit the given $rule targets into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\Transfer\Output\Visitor $subVisitor
     *
     * @return mixed
     */
    private function visitTargets(RuleValue $rule, Visitor $subVisitor)
    {
        $hash = array();
        $targets = $rule->getTargets();

        foreach ($targets as $target) {
            $hash[$target->getId()] = $subVisitor->visit($target);
        }

        return $hash;
    }

    /**
     * Visit the given $rule conditions into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\Transfer\Output\Visitor $subVisitor
     *
     * @return mixed
     */
    private function visitConditions(RuleValue $rule, Visitor $subVisitor)
    {
        $hash = array();
        $conditions = $rule->getConditions();

        foreach ($conditions as $condition) {
            $hash[$condition->getId()] = $subVisitor->visit($condition);
        }

        return $hash;
    }
}
