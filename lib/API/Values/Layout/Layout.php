<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use DateTimeImmutable;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Exception\API\LayoutException;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Symfony\Component\Uid\Uuid;

use function in_array;

final class Layout implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) Uuid $id;

    /**
     * Returns the layout type.
     */
    public private(set) LayoutTypeInterface $layoutType;

    /**
     * Returns human readable name of the layout.
     */
    public private(set) string $name;

    /**
     * Return human readable description of the layout.
     */
    public private(set) string $description;

    /**
     * Returns when was the layout first created.
     */
    public private(set) DateTimeImmutable $created;

    /**
     * Returns when was the layout last updated.
     */
    public private(set) DateTimeImmutable $modified;

    /**
     * Returns if the layout is shared.
     */
    public private(set) bool $isShared;

    /**
     * Returns the main locale of the layout.
     */
    public private(set) string $mainLocale;

    /**
     * Returns the list of all available locales in the layout.
     *
     * @var string[]
     */
    public private(set) array $availableLocales;

    /**
     * Returns all zones from the layout.
     */
    public private(set) ZoneList $zones {
        get => ZoneList::fromArray($this->zones->toArray());
    }

    /**
     * Returns if the layout has the provided locale.
     */
    public function hasLocale(string $locale): bool
    {
        return in_array($locale, $this->availableLocales, true);
    }

    /**
     * Returns the specified zone.
     *
     * @throws \Netgen\Layouts\Exception\API\LayoutException If the zone does not exist
     */
    public function getZone(string $zoneIdentifier): Zone
    {
        return $this->zones->get($zoneIdentifier) ??
            throw LayoutException::noZone($zoneIdentifier);
    }

    /**
     * Returns if layout has a specified zone.
     */
    public function hasZone(string $zoneIdentifier): bool
    {
        return $this->zones->containsKey($zoneIdentifier);
    }
}
