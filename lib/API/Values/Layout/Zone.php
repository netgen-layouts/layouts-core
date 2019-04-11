<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\Zone as APIZone;
use Netgen\Layouts\API\Values\LazyPropertyTrait;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;

final class Zone implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;
    use LazyPropertyTrait;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var int|string
     */
    private $layoutId;

    /**
     * @var \Netgen\Layouts\API\Values\Layout\Zone|null
     */
    private $linkedZone;

    /**
     * Returns the zone identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the ID of the layout to which this zone belongs.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns if the zone has a linked zone.
     */
    public function hasLinkedZone(): bool
    {
        return $this->getLinkedZone() instanceof APIZone;
    }

    /**
     * Returns the linked zone or null if no linked zone exists.
     */
    public function getLinkedZone(): ?APIZone
    {
        return $this->getLazyProperty($this->linkedZone);
    }
}
