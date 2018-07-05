<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator;

use Symfony\Component\HttpKernel\Kernel;

/**
 * @deprecated Backwards compatibility layer for Symfony 2.8 and 3.4/4.0
 */
trait StrictChoiceValidatorTrait
{
    private function getStrictChoiceValidatorOption(): array
    {
        if (Kernel::VERSION_ID < 30200) {
            // On Symfony 2.8, setting the strict option to "true" is needed
            return ['strict' => true];
        }

        return [];
    }
}
