<?php

namespace Netgen\BlockManager\Core\Values\Layout;

use Netgen\BlockManager\API\Values\Layout\Zone as APIZone;
use Netgen\BlockManager\Core\Service\Mapper\Proxy\LazyLoadingProxyTrait;
use Netgen\BlockManager\Core\Values\Value;

final class Zone extends Value implements APIZone
{
    use LazyLoadingProxyTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var int|string
     */
    protected $layoutId;

    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Zone
     */
    protected $linkedZone;

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getLayoutId()
    {
        return $this->layoutId;
    }

    public function hasLinkedZone()
    {
        return $this->getLinkedZone() instanceof APIZone;
    }

    public function getLinkedZone()
    {
        return $this->getLazyLoadedProperty($this->linkedZone);
    }
}
