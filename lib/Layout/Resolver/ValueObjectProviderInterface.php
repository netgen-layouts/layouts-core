<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

interface ValueObjectProviderInterface
{
    /**
     * Returns the value object associated with the provided value of a target
     * or null if the value object does not exist (e.g. if the value is null or invalid).
     *
     * @param mixed $value
     */
    public function getValueObject($value): ?object;
}
