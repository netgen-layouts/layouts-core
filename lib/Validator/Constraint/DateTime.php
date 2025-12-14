<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class DateTime extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        /**
         * If true, the validator will validate an array with datetime and timezone
         * keys as a datetime value.
         */
        public bool $allowArray = false,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_datetime';
    }
}
