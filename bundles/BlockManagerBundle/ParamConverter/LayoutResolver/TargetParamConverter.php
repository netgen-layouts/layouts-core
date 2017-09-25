<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

final class TargetParamConverter extends ParamConverter
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
        return array('targetId');
    }

    public function getDestinationAttributeName()
    {
        return 'target';
    }

    public function getSupportedClass()
    {
        return Target::class;
    }

    public function loadValueObject(array $values)
    {
        if ($values['published']) {
            return $this->layoutResolverService->loadTarget($values['targetId']);
        }

        return $this->layoutResolverService->loadTargetDraft($values['targetId']);
    }
}
