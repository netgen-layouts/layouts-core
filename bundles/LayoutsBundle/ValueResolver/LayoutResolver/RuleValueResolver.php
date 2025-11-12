<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Ramsey\Uuid\Uuid;

final class RuleValueResolver extends ValueResolver
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    public function getSourceAttributeNames(): array
    {
        return ['ruleId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'rule';
    }

    public function getSupportedClass(): string
    {
        return Rule::class;
    }

    public function loadValue(array $values): Rule
    {
        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->layoutResolverService->loadRule(Uuid::fromString($values['ruleId'])),
            self::STATUS_ARCHIVED => $this->layoutResolverService->loadRuleArchive(Uuid::fromString($values['ruleId'])),
            default => $this->layoutResolverService->loadRuleDraft(Uuid::fromString($values['ruleId'])),
        };
    }
}
