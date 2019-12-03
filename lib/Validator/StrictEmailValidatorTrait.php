<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Validator\Constraints\Email;

/**
 * @deprecated Backwards compatibility layer for Symfony 3.4
 */
trait StrictEmailValidatorTrait
{
    /**
     * @return array<string, mixed>
     */
    private function getStrictEmailValidatorOption(): array
    {
        if (Kernel::VERSION_ID < 40100) {
            // On Symfony 3.4, the constraint uses 'strict' option.
            return ['strict' => true];
        }

        return ['mode' => Email::VALIDATION_MODE_STRICT];
    }
}
