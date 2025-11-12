<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Ramsey\Uuid\Uuid;

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

    public function loadValue(array $values): RuleGroup
    {
        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->layoutResolverService->loadRuleGroup(Uuid::fromString($values['ruleGroupId'])),
            self::STATUS_ARCHIVED => $this->layoutResolverService->loadRuleGroupArchive(Uuid::fromString($values['ruleGroupId'])),
            default => $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString($values['ruleGroupId'])),
        };
    }
}
