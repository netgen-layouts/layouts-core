<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\Core\Values\Value;

final class Query extends Value implements APIQuery
{
    use ParameterBasedValueTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $collectionId;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface
     */
    protected $queryType;

    /**
     * @var string[]
     */
    protected $availableLocales = [];

    /**
     * @var string
     */
    protected $mainLocale;

    /**
     * @var bool
     */
    protected $isTranslatable;

    /**
     * @var bool
     */
    protected $alwaysAvailable;

    /**
     * @var string
     */
    protected $locale;

    public function getId()
    {
        return $this->id;
    }

    public function getCollectionId()
    {
        return $this->collectionId;
    }

    public function isContextual()
    {
        return $this->queryType->isContextual($this);
    }

    public function getQueryType()
    {
        return $this->queryType;
    }

    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    public function getMainLocale()
    {
        return $this->mainLocale;
    }

    public function isTranslatable()
    {
        return $this->isTranslatable;
    }

    public function isAlwaysAvailable()
    {
        return $this->alwaysAvailable;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
