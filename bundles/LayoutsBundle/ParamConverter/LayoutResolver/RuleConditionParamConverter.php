<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Ramsey\Uuid\Uuid;

final class RuleConditionParamConverter extends ParamConverter
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
