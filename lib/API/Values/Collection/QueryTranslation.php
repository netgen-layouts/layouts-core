<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterBasedValue;

interface QueryTranslation extends ParameterBasedValue
{
    /**
     * Returns the translation locale.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Returns if the translation is the main one in the query.
     *
     * @return bool
     */
    public function isMainTranslation();
}
