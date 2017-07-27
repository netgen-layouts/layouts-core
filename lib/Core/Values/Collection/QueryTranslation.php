<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\QueryTranslation as APIQueryTranslation;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\ValueObject;

class QueryTranslation extends ValueObject implements APIQueryTranslation
{
    use ParameterBasedValueTrait;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var bool
     */
    protected $isMainTranslation;

    /**
     * Returns the translation locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Returns if the translation is the main one in the block.
     *
     * @return bool
     */
    public function isMainTranslation()
    {
        return $this->isMainTranslation;
    }
}
