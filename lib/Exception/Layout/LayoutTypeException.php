<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Layout;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class LayoutTypeException extends InvalidArgumentException implements Exception
{
    public static function noLayoutType(string $layoutType): self
    {
        return new self(
            sprintf(
                'Layout type with "%s" identifier does not exist.',
                $layoutType
            )
        );
    }

    public static function noZone(string $layoutType, string $zoneIdentifier): self
    {
        return new self(
            sprintf(
                'Zone "%s" does not exist in "%s" layout type.',
                $zoneIdentifier,
                $layoutType
            )
        );
    }
}
