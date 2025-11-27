<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

use Netgen\Layouts\API\Values\Value;
use Ramsey\Uuid\UuidInterface;

final class SuccessResult implements ResultInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private(set) string $entityType,
        private(set) array $data,
        private(set) UuidInterface $entityId,
        /**
         * Returns the imported entity.
         */
        private(set) Value $entity,
    ) {}
}
