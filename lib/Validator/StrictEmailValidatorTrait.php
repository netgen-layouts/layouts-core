<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator;

use Egulias\EmailValidator\Validation\EmailValidation;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Validator\Constraints\Email;

/**
 * @deprecated Backwards compatibility layer for Symfony 2.8 and 3.4/4.0
 */
trait StrictEmailValidatorTrait
{
    private function getStrictEmailValidatorOption(): array
    {
        if (Kernel::VERSION_ID < 30000) {
            // On Symfony 2.8, strict validation is supported only with
            // Email Validator 1.2 (where EmailValidation interface does not exist)
            return ['strict' => !interface_exists(EmailValidation::class)];
        }

        if (Kernel::VERSION_ID < 40100) {
            // On Symfony 3.4 and 4.0, strict validation is always supported
            return ['strict' => true];
        }

        return ['mode' => Email::VALIDATION_MODE_STRICT];
    }
}
