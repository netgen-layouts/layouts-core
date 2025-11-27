<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

use Ramsey\Uuid\UuidInterface;
use Throwable;

final class ErrorResult implements ResultInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private(set) string $entityType,
        private(set) array $data,
        private(set) UuidInterface $entityId,
        /**
         * Returns the import error.
         */
        private(set) Throwable $error,
    ) {}
}
