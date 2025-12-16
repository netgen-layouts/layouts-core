<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Type;

use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;

final class NullLayoutType implements LayoutTypeInterface
{
    public true $isEnabled {
        get => true;
    }

    public string $name {
        get => 'Invalid layout type';
    }

    public string $icon {
        get => '';
    }

    public array $zones {
        get => [];
    }

    public array $zoneIdentifiers {
        get => [];
    }

    public function __construct(
        public private(set) string $identifier,
    ) {}

    public function hasZone(string $zoneIdentifier): false
    {
        return false;
    }

    public function getZone(string $zoneIdentifier): never
    {
        throw LayoutTypeException::noZone($this->identifier, $zoneIdentifier);
    }

    public function isBlockAllowedInZone(BlockDefinitionInterface $definition, string $zoneIdentifier): true
    {
        return true;
    }
}
