<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Status;
use Symfony\Component\Uid\Uuid;

final class LayoutValueResolver extends ValueResolver
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    protected function getSourceAttributeNames(): array
    {
        return ['layoutId'];
    }

    protected function getDestinationAttributeName(): string
    {
        return 'layout';
    }

    protected function getSupportedClass(): string
    {
        return Layout::class;
    }

    protected function loadValue(array $parameters): Layout
    {
        return match ($parameters['status']) {
            Status::Published => $this->layoutService->loadLayout(Uuid::fromString($parameters['layoutId'])),
            Status::Archived => $this->layoutService->loadLayoutArchive(Uuid::fromString($parameters['layoutId'])),
            default => $this->layoutService->loadLayoutDraft(Uuid::fromString($parameters['layoutId'])),
        };
    }
}
