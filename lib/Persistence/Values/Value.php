<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values;

/**
 * Classes extending this class represent a versionable entity.
 */
abstract class Value
{
    /**
     * Status of the value.
     */
    final public Status $status;
}
