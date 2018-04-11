<?php

namespace Netgen\BlockManager\Tests\Item\Stubs;

final class Value
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $remoteId;

    public function __construct($id, $remoteId)
    {
        $this->id = $id;
        $this->remoteId = $remoteId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRemoteId()
    {
        return $this->remoteId;
    }

    public function isVisible()
    {
        return $this->id < 100;
    }
}
