<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

interface Query extends Value, ParameterCollectionInterface
{
    /**
     * Returns the query ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the ID of the collection to which this query belongs.
     *
     * @return int|string
     */
    public function getCollectionId();

    /**
     * Returns if the query is dependent on a context, i.e. currently displayed page.
     */
    public function isContextual(): bool;

    /**
     * Returns the query type.
     */
    public function getQueryType(): QueryTypeInterface;

    /**
     * Returns the list of all available locales in the query.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array;

    /**
     * Returns the main locale for the query.
     */
    public function getMainLocale(): string;

    /**
     * Returns if the query is translatable.
     */
    public function isTranslatable(): bool;

    /**
     * Returns if the main translation of the query will be used
     * in case there are no prioritized translations.
     */
    public function isAlwaysAvailable(): bool;

    /**
     * Returns the locale of the currently loaded translation.
     */
    public function getLocale(): string;
}
