<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Ramsey\Uuid\Uuid;

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

    public function loadValue(array $values): Layout
    {
        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->layoutService->loadLayout(Uuid::fromString($values['layoutId'])),
            self::STATUS_ARCHIVED => $this->layoutService->loadLayoutArchive(Uuid::fromString($values['layoutId'])),
            default => $this->layoutService->loadLayoutDraft(Uuid::fromString($values['layoutId'])),
        };
    }
}
