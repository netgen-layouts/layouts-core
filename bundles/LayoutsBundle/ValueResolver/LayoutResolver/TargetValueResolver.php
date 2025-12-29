<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\Status;
use Symfony\Component\Uid\Uuid;

final class TargetValueResolver extends ValueResolver
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    protected function getSourceAttributeNames(): array
    {
        return ['targetId'];
    }

    protected function getDestinationAttributeName(): string
    {
        return 'target';
    }

    protected function getSupportedClass(): string
    {
        return Target::class;
    }

    protected function loadValue(array $parameters): Target
    {
        return match ($parameters['status']) {
            Status::Published => $this->layoutResolverService->loadTarget(Uuid::fromString($parameters['targetId'])),
            default => $this->layoutResolverService->loadTargetDraft(Uuid::fromString($parameters['targetId'])),
        };
    }
}
