<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Layout;

use Netgen\BlockManager\API\Values\Layout\Zone as APIZone;
use Netgen\BlockManager\Core\Values\LazyPropertyTrait;
use Netgen\BlockManager\Core\Values\ValueStatusTrait;
use Netgen\BlockManager\Value;

final class Zone extends Value implements APIZone
{
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
     * @var \Netgen\BlockManager\API\Values\Layout\Zone|null
     */
    private $linkedZone;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getLayoutId()
    {
        return $this->layoutId;
    }

    public function hasLinkedZone(): bool
    {
        return $this->getLinkedZone() instanceof APIZone;
    }

    public function getLinkedZone(): ?APIZone
    {
        return $this->getLazyProperty($this->linkedZone);
    }
}
