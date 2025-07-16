<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Type;

use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;

final class NullLayoutType implements LayoutTypeInterface
{
    private string $layoutType;

    public function __construct(string $layoutType)
    {
        $this->layoutType = $layoutType;
    }

    public function getIdentifier(): string
    {
        return $this->layoutType;
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'Invalid layout type';
    }

    public function getIcon(): string
    {
        return '';
    }

    public function getZones(): array
    {
        return [];
    }

    public function getZoneIdentifiers(): array
    {
        return [];
    }

    public function hasZone(string $zoneIdentifier): bool
    {
        return false;
    }

    public function getZone(string $zoneIdentifier): Zone
    {
        throw LayoutTypeException::noZone($this->layoutType, $zoneIdentifier);
    }

    public function isBlockAllowedInZone(BlockDefinitionInterface $definition, string $zoneIdentifier): bool
    {
        return true;
    }
}
