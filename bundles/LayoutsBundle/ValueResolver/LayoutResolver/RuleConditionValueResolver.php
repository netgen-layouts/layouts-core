<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Ramsey\Uuid\Uuid;

final class RuleConditionValueResolver extends ValueResolver
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

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
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->layoutResolverService->loadRuleCondition(Uuid::fromString($values['conditionId']));
        }

        return $this->layoutResolverService->loadRuleConditionDraft(Uuid::fromString($values['conditionId']));
    }
}
