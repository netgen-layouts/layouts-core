<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\ParameterAwareValueTrait;
use Netgen\BlockManager\ValueObject;

final class Query extends ValueObject implements APIQuery
{
    use ParameterAwareValueTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int|string
     */
    protected $collectionId;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    protected $queryType;

    /**
     * @var string[]
     */
    protected $availableLocales = array();

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

    public function getStatus()
    {
        return $this->status;
    }

    public function getCollectionId()
    {
        return $this->collectionId;
    }

    public function isPublished()
    {
        return $this->published;
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
