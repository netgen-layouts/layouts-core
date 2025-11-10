<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Ramsey\Uuid\Uuid;

final class LayoutValueResolver extends ValueResolver
{
    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

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
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->layoutService->loadLayout(Uuid::fromString($values['layoutId']));
        }

        if ($values['status'] === self::STATUS_ARCHIVED) {
            return $this->layoutService->loadLayoutArchive(Uuid::fromString($values['layoutId']));
        }

        return $this->layoutService->loadLayoutDraft(Uuid::fromString($values['layoutId']));
    }
}
