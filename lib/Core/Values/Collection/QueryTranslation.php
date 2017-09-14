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

    public function getLocale()
    {
        return $this->locale;
    }

    public function isMainTranslation()
    {
        return $this->isMainTranslation;
    }
}
