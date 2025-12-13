<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Symfony\Component\Uid\Uuid;

final class RuleConditionValueResolver extends ValueResolver
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
        return RuleCondition::class;
    }

    public function loadValue(array $values): RuleCondition
    {
        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->layoutResolverService->loadRuleCondition(Uuid::fromString($values['conditionId'])),
            default => $this->layoutResolverService->loadRuleConditionDraft(Uuid::fromString($values['conditionId'])),
        };
    }
}
