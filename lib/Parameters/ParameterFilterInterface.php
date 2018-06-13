<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

/**
 * Parameter filters are used before validating the parameter values to perform various
 * transformations. For example, one filter would rewrite the HTML markup parameter value
 * to markup without any <script> tags or other insecure "features".
 */
interface ParameterFilterInterface
{
    /**
     * Filters the parameter value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($value);
}
