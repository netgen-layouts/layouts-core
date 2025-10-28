<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use function is_callable;

trait LazyPropertyTrait
{
    /**
     * Lazily loads the provided variable, meaning, if it is callable,
     * it executes it and stores the result of execution into itself,
     * making it lazily loaded for the next run.
     */
    private function getLazyProperty(mixed &$property): mixed
    {
        if (is_callable($property)) {
            $property = $property();
        }

        return $property;
    }
}
