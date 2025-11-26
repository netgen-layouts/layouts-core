<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\Value;

trait StatusStringTrait
{
    /**
     * Return status string representation for the given $value.
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException If status is not recognized
     */
    private function getStatusString(Value $value): string
    {
        return match ($value->status) {
            Status::Draft => 'DRAFT',
            Status::Published => 'PUBLISHED',
            Status::Archived => 'ARCHIVED',
        };
    }
}
