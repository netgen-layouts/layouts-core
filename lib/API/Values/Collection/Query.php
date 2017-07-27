<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterBasedValue;
use Netgen\BlockManager\API\Values\Value;

interface Query extends Value, ParameterBasedValue
{
    /**
     * Returns the query ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the collection ID the query is in.
     *
     * @return int|string
     */
    public function getCollectionId();

    /**
     * Returns if the query is published.
     *
     * @return bool
     */
    public function isPublished();

    /**
     * Returns the limit internal to the query.
     *
     * @return int
     */
    public function getInternalLimit();

    /**
     * Returns if the query is dependent on a context, i.e. current request.
     *
     * @return bool
     */
    public function isContextual();

    /**
     * Returns the query type.
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function getQueryType();

    /**
     * Returns the list of all available locales in the query.
     *
     * @return string[]
     */
    public function getAvailableLocales();

    /**
     * Returns the main locale for the query.
     *
     * @return string
     */
    public function getMainLocale();

    /**
     * Returns if the query is translatable.
     *
     * @return bool
     */
    public function isTranslatable();

    /**
     * Returns if the main translation of the query is used
     * in case there are no prioritized translations.
     *
     * @return bool
     */
    public function isAlwaysAvailable();

    /**
     * Returns if the query has a translation in specified locale.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function hasTranslation($locale);

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
    public function getTranslation($locale = null);

    /**
     * Returns all query translations.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\QueryTranslation[]
     */
    public function getTranslations();
}
