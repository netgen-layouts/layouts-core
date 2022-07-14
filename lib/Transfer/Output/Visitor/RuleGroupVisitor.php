<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\StatusStringTrait;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function iterator_to_array;

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

    public const ENTITY_TYPE = 'rule_group';

    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    public function accept(object $value): bool
    {
        return $value instanceof RuleGroup;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            '__type' => self::ENTITY_TYPE,
            'id' => $value->getId()->toString(),
            'status' => $this->getStatusString($value),
            'parent_id' => $value->getParentId() !== null ? $value->getParentId()->toString() : null,
            'name' => $value->getName(),
            'description' => $value->getDescription(),
            'is_enabled' => $value->isEnabled(),
            'priority' => $value->getPriority(),
            'groups' => iterator_to_array($this->visitSubGroups($value, $outputVisitor)),
            'rules' => iterator_to_array($this->visitRules($value, $outputVisitor)),
            'conditions' => iterator_to_array($this->visitConditions($value, $outputVisitor)),
        ];
    }

    /**
     * Visit the given $ruleGroup subgroups into hash representation.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function visitSubGroups(RuleGroup $ruleGroup, OutputVisitor $outputVisitor): Generator
    {
        foreach ($this->layoutResolverService->loadRuleGroups($ruleGroup) as $subGroup) {
            yield $outputVisitor->visit($subGroup);
        }
    }

    /**
     * Visit the given $ruleGroup rules into hash representation.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function visitRules(RuleGroup $ruleGroup, OutputVisitor $outputVisitor): Generator
    {
        foreach ($ruleGroup->getRules() as $rule) {
            yield $outputVisitor->visit($rule);
        }
    }

    /**
     * Visit the given $ruleGroup conditions into hash representation.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function visitConditions(RuleGroup $ruleGroup, OutputVisitor $outputVisitor): Generator
    {
        foreach ($ruleGroup->getConditions() as $condition) {
            yield $outputVisitor->visit($condition);
        }
    }
}
