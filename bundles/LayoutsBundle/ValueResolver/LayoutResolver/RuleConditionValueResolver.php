<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\Status;
use Symfony\Component\Uid\Uuid;

final class RuleConditionValueResolver extends ValueResolver
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
        return RuleCondition::class;
    }

    protected function loadValue(array $parameters): RuleCondition
    {
        return match ($parameters['status']) {
            Status::Published => $this->layoutResolverService->loadRuleCondition(Uuid::fromString($parameters['conditionId'])),
            default => $this->layoutResolverService->loadRuleConditionDraft(Uuid::fromString($parameters['conditionId'])),
        };
    }
}
