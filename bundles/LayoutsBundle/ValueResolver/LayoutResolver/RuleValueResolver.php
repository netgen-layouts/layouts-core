<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Status;
use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Symfony\Component\Uid\Uuid;

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

    public function loadValue(array $parameters): Rule
    {
        return match ($parameters['status']) {
            Status::Published => $this->layoutResolverService->loadRule(Uuid::fromString($parameters['ruleId'])),
            Status::Archived => $this->layoutResolverService->loadRuleArchive(Uuid::fromString($parameters['ruleId'])),
            default => $this->layoutResolverService->loadRuleDraft(Uuid::fromString($parameters['ruleId'])),
        };
    }
}
