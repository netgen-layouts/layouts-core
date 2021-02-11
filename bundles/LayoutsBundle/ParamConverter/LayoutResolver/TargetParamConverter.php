<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Ramsey\Uuid\Uuid;

final class TargetParamConverter extends ParamConverter
{
    private LayoutResolverService $layoutResolverService;

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

    public function loadValue(array $values): Target
    {
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->layoutResolverService->loadTarget(Uuid::fromString($values['targetId']));
        }

        return $this->layoutResolverService->loadTargetDraft(Uuid::fromString($values['targetId']));
    }
}
