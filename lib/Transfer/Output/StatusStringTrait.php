<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Exception\RuntimeException;

use function sprintf;
use function var_export;

trait StatusStringTrait
{
    /**
     * Return status string representation for the given $value.
     *
     * @throws \Netgen\Layouts\Exception\RuntimeException If status is not recognized
     */
    private function getStatusString(Value $value): string
    {
        switch ($value->getStatus()) {
            case Value::STATUS_DRAFT:
                return 'DRAFT';

            case Value::STATUS_PUBLISHED:
                return 'PUBLISHED';

            case Value::STATUS_ARCHIVED:
                return 'ARCHIVED';
        }

        throw new RuntimeException(sprintf("Unknown status '%s'", var_export($value->getStatus(), true)));
    }
}
