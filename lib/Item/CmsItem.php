<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

use Netgen\Layouts\Utils\HydratorTrait;

final class CmsItem implements CmsItemInterface
{
    use HydratorTrait;

    public private(set) int|string $value;

    public private(set) int|string $remoteId;

    public private(set) string $valueType;

    public private(set) string $name;

    public private(set) bool $isVisible;

    public private(set) ?object $object;
}
