<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\ParameterBasedValue;

interface BlockTranslation extends ParameterBasedValue
{
    /**
     * Returns the translation locale.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Returns if the translation is the main one in the block.
     *
     * @return bool
     */
    public function isMainTranslation();
}
