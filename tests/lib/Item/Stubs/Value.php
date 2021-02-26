<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

final class Value
{
    private int $id;

    private string $remoteId;

    public function __construct(int $id, string $remoteId)
    {
        $this->id = $id;
        $this->remoteId = $remoteId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRemoteId(): string
    {
        return $this->remoteId;
    }

    public function isVisible(): bool
    {
        return $this->id < 100;
    }
}
