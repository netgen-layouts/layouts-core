<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

interface ValueObjectProviderInterface
{
    /**
     * Returns the value object associated with the provided value of a parameter
     * or null if the value object does not exist (e.g. if the value is null).
     *
     * @param mixed $value
     */
    public function getValueObject($value): ?object;
}
