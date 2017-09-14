<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\Exception\Core\TranslationException;
use Netgen\BlockManager\ValueObject;

class Query extends ValueObject implements APIQuery
{
    use ParameterBasedValueTrait;

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
     * @var \Netgen\BlockManager\API\Values\Collection\QueryTranslation[]
     */
    protected $translations = array();

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

    public function getInternalLimit()
    {
        return $this->queryType->getInternalLimit($this);
    }

    public function isContextual()
    {
        return $this->queryType->isContextual($this);
    }

    public function getQueryType()
    {
        return $this->queryType;
    }

    public function getParameters()
    {
        return $this->getTranslation()->getParameters();
    }

    public function getParameter($parameterName)
    {
        return $this->getTranslation()->getParameter($parameterName);
    }

    public function hasParameter($parameterName)
    {
        return $this->getTranslation()->hasParameter($parameterName);
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

    public function hasTranslation($locale)
    {
        return array_key_exists($locale, $this->translations);
    }

    public function getTranslation($locale = null)
    {
        if ($locale === null) {
            return $this->translations[$this->availableLocales[0]];
        }

        if (!$this->hasTranslation($locale)) {
            throw TranslationException::noTranslation($locale);
        }

        return $this->translations[$locale];
    }

    public function getTranslations()
    {
        return $this->translations;
    }
}
