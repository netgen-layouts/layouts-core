<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

final class Value
{
    public function __construct(
        private int $id,
        private string $remoteId,
    ) {}

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
