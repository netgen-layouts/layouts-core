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
        public private(set) string $entityType,
        public private(set) array $data,
        public private(set) UuidInterface $entityId,
        /**
         * Returns the import error.
         */
        public private(set) Throwable $error,
    ) {}
}
