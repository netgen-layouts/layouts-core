<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Ramsey\Uuid\Uuid;

final class RuleGroupParamConverter extends ParamConverter
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

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
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->layoutResolverService->loadRuleGroup(Uuid::fromString($values['ruleGroupId']));
        }

        if ($values['status'] === self::STATUS_ARCHIVED) {
            return $this->layoutResolverService->loadRuleGroupArchive(Uuid::fromString($values['ruleGroupId']));
        }

        return $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString($values['ruleGroupId']));
    }
}
