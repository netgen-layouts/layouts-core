<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Ramsey\Uuid\Uuid;

final class RuleGroupConditionValueResolver extends ValueResolver
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    public function getSourceAttributeNames(): array
    {
        return ['conditionId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'condition';
    }

    public function getSupportedClass(): string
    {
        return RuleGroupCondition::class;
    }

    public function loadValue(array $values): RuleGroupCondition
    {
        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->layoutResolverService->loadRuleGroupCondition(Uuid::fromString($values['conditionId'])),
            default => $this->layoutResolverService->loadRuleGroupConditionDraft(Uuid::fromString($values['conditionId'])),
        };
    }
}
