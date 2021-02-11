<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Ramsey\Uuid\Uuid;

final class RuleParamConverter extends ParamConverter
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

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
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->layoutResolverService->loadRule(Uuid::fromString($values['ruleId']));
        }

        if ($values['status'] === self::STATUS_ARCHIVED) {
            return $this->layoutResolverService->loadRuleArchive(Uuid::fromString($values['ruleId']));
        }

        return $this->layoutResolverService->loadRuleDraft(Uuid::fromString($values['ruleId']));
    }
}
