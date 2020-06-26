<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

abstract class ConditionType implements ConditionTypeInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function export($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function import($value)
    {
        return $value;
    }
}
