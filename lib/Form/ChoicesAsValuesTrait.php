<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Form;

use Symfony\Component\HttpKernel\Kernel;

/**
 * @deprecated Backwards compatibility layer for Symfony 2.8
 */
trait ChoicesAsValuesTrait
{
    /**
     * Returns the array filled with choices_as_values form option for ChoiceType Symfony form
     * on versions of Symfony lower than 3.1.
     */
    private function getChoicesAsValuesOption(): array
    {
        if (Kernel::VERSION_ID < 30100) {
            return [
                'choices_as_values' => true,
            ];
        }

        return [];
    }
}
