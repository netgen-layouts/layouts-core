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
     *
     * @param mixed $property
     *
     * @return mixed
     */
    private function getLazyProperty(&$property)
    {
        if (is_callable($property)) {
            $property = $property();
        }

        return $property;
    }
}
