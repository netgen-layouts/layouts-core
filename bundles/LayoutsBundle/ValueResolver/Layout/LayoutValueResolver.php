<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Status;
use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Symfony\Component\Uid\Uuid;

final class LayoutValueResolver extends ValueResolver
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    public function getSourceAttributeNames(): array
    {
        return ['layoutId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'layout';
    }

    public function getSupportedClass(): string
    {
        return Layout::class;
    }

    public function loadValue(array $parameters): Layout
    {
        return match ($parameters['status']) {
            Status::Published => $this->layoutService->loadLayout(Uuid::fromString($parameters['layoutId'])),
            Status::Archived => $this->layoutService->loadLayoutArchive(Uuid::fromString($parameters['layoutId'])),
            default => $this->layoutService->loadLayoutDraft(Uuid::fromString($parameters['layoutId'])),
        };
    }
}
