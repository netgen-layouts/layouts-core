<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item\Stubs;

final class Value
{
    /**
     * @var int|string
     */
    private $id;

    /**
     * @var int|string
     */
    private $remoteId;

    /**
     * Value constructor.
     *
     * @param int|string $id
     * @param int|string $remoteId
     */
    public function __construct($id, $remoteId)
    {
        $this->id = $id;
        $this->remoteId = $remoteId;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int|string
     */
    public function getRemoteId()
    {
        return $this->remoteId;
    }

    public function isVisible(): bool
    {
        return $this->id < 100;
    }
}
