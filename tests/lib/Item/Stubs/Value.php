<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

final class Value
{
    public bool $isVisible {
        get => $this->id < 100;
    }

    public function __construct(
        private(set) int $id,
        private(set) string $remoteId,
    ) {}
}
