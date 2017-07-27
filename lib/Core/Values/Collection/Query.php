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

    /**
     * Returns the query ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the status of the value.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the collection ID the query is in.
     *
     * @return int|string
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Returns if the query is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns the limit internal to the query.
     *
     * @return int
     */
    public function getInternalLimit()
    {
        return $this->queryType->getInternalLimit($this);
    }

    /**
     * Returns if the query is dependent on a context, i.e. current request.
     *
     * @return bool
     */
    public function isContextual()
    {
        return $this->queryType->isContextual($this);
    }

    /**
     * Returns the query type.
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * Returns all parameter values.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue[]
     */
    public function getParameters()
    {
        return $this->getTranslation()->getParameters();
    }

    /**
     * Returns the specified parameter value.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Core\ParameterException If the requested parameter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue
     */
    public function getParameter($parameterName)
    {
        return $this->getTranslation()->getParameter($parameterName);
    }

    /**
     * Returns if the object has a specified parameter value.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return $this->getTranslation()->hasParameter($parameterName);
    }

    /**
     * Returns the list of all available locales in the query.
     *
     * @return string[]
     */
    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    /**
     * Returns the main locale for the query.
     *
     * @return string
     */
    public function getMainLocale()
    {
        return $this->mainLocale;
    }

    /**
     * Returns if the query is translatable.
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->isTranslatable;
    }

    /**
     * Returns if the main translation of the query is used
     * in case there are no prioritized translations.
     *
     * @return bool
     */
    public function isAlwaysAvailable()
    {
        return $this->alwaysAvailable;
    }

    /**
     * Returns if the query has a translation in specified locale.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function hasTranslation($locale)
    {
        return array_key_exists($locale, $this->translations);
    }

    /**
     * Returns a query translation in specified locale.
     *
     * If locale is not specified, first locale in the list of available locales is used.
     *
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\Core\TranslationException If the requested translation does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryTranslation
     */
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

    /**
     * Returns all query translations.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}
