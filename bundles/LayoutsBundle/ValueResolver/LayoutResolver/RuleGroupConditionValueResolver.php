<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\API\Values\Status;
use Symfony\Component\Uid\Uuid;

final class RuleGroupConditionValueResolver extends ValueResolver
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    protected function getSourceAttributeNames(): array
    {
        return ['conditionId'];
    }

    protected function getDestinationAttributeName(): string
    {
        return 'condition';
    }

    protected function getSupportedClass(): string
    {
        return RuleGroupCondition::class;
    }

    protected function loadValue(array $parameters): RuleGroupCondition
    {
        return match ($parameters['status']) {
            Status::Published => $this->layoutResolverService->loadRuleGroupCondition(Uuid::fromString($parameters['conditionId'])),
            default => $this->layoutResolverService->loadRuleGroupConditionDraft(Uuid::fromString($parameters['conditionId'])),
        };
    }
}
