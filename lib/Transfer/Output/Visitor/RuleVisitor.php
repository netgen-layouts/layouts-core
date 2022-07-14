<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function iterator_to_array;

/**
 * Rule value visitor.
 *
 * @see \Netgen\Layouts\API\Values\LayoutResolver\Rule
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\LayoutResolver\Rule>
 */
final class RuleVisitor implements VisitorInterface
{
    use StatusStringTrait;

    public const ENTITY_TYPE = 'rule';

    public function accept(object $value): bool
    {
        return $value instanceof Rule;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        $layout = $value->getLayout();

        return [
            '__type' => self::ENTITY_TYPE,
            'id' => $value->getId()->toString(),
            'status' => $this->getStatusString($value),
            'rule_group_id' => $value->getRuleGroupId()->toString(),
            'layout_id' => $layout instanceof Layout ? $layout->getId()->toString() : null,
            'is_enabled' => $value->isEnabled(),
            'priority' => $value->getPriority(),
            'description' => $value->getDescription(),
            'comment' => $value->getDescription(),
            'targets' => iterator_to_array($this->visitTargets($value, $outputVisitor)),
            'conditions' => iterator_to_array($this->visitConditions($value, $outputVisitor)),
        ];
    }

    /**
     * Visit the given $rule targets into hash representation.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function visitTargets(Rule $rule, OutputVisitor $outputVisitor): Generator
    {
        foreach ($rule->getTargets() as $target) {
            yield $outputVisitor->visit($target);
        }
    }

    /**
     * Visit the given $rule conditions into hash representation.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function visitConditions(Rule $rule, OutputVisitor $outputVisitor): Generator
    {
        foreach ($rule->getConditions() as $condition) {
            yield $outputVisitor->visit($condition);
        }
    }
}
