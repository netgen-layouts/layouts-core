<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

final class RuleParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    public function getSourceAttributeNames()
    {
        return ['ruleId'];
    }

    public function getDestinationAttributeName()
    {
        return 'rule';
    }

    public function getSupportedClass()
    {
        return Rule::class;
    }

    public function loadValue(array $values)
    {
        if ($values['status'] === self::$statusPublished) {
            return $this->layoutResolverService->loadRule($values['ruleId']);
        }

        if ($values['status'] === self::$statusArchived) {
            return $this->layoutResolverService->loadRuleArchive($values['ruleId']);
        }

        return $this->layoutResolverService->loadRuleDraft($values['ruleId']);
    }
}
