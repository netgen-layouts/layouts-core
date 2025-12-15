<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\Status;
use Symfony\Component\Uid\Uuid;

final class RuleGroupValueResolver extends ValueResolver
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    public function getSourceAttributeNames(): array
    {
        return ['ruleGroupId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'ruleGroup';
    }

    public function getSupportedClass(): string
    {
        return RuleGroup::class;
    }

    public function loadValue(array $parameters): RuleGroup
    {
        return match ($parameters['status']) {
            Status::Published => $this->layoutResolverService->loadRuleGroup(Uuid::fromString($parameters['ruleGroupId'])),
            Status::Archived => $this->layoutResolverService->loadRuleGroupArchive(Uuid::fromString($parameters['ruleGroupId'])),
            default => $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString($parameters['ruleGroupId'])),
        };
    }
}
