<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class DateTime extends Constraint
{
    /**
     * If true, the validator will validate an array with datetime and timezone
     * keys as a datetime value.
     *
     * @var bool
     */
    public $allowArray = false;

    public $invalidTimeZoneMessage = 'netgen_block_manager.datetime.invalid_timezone';

    public function validatedBy()
    {
        return 'ngbm_datetime';
    }
}
