<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\Value;

trait StatusStringTrait
{
    /**
     * Return status string representation for the given $value.
     */
    private function getStatusString(Value $value): string
    {
        return match ($value->status) {
            Status::Draft => 'draft',
            Status::Published => 'published',
            Status::Archived => 'archived',
        };
    }
}
