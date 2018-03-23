<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

final class ConditionParamConverter extends ParamConverter
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
        return array('conditionId');
    }

    public function getDestinationAttributeName()
    {
        return 'condition';
    }

    public function getSupportedClass()
    {
        return Condition::class;
    }

    public function loadValue(array $values)
    {
        if ($values['published']) {
            return $this->layoutResolverService->loadCondition($values['conditionId']);
        }

        return $this->layoutResolverService->loadConditionDraft($values['conditionId']);
    }
}
