<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\Value;
use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;

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

    public function getSourceAttributeNames(): array
    {
        return ['targetId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'target';
    }

    public function getSupportedClass(): string
    {
        return Target::class;
    }

    public function loadValue(array $values): Value
    {
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->layoutResolverService->loadTarget($values['targetId']);
        }

        return $this->layoutResolverService->loadTargetDraft($values['targetId']);
    }
}
