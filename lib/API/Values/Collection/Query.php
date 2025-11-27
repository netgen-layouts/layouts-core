<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Collection;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Collection\QueryType\QueryTypeInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Query implements Value, ParameterCollectionInterface
{
    use HydratorTrait;
    use ParameterCollectionTrait;
    use ValueStatusTrait;

    public private(set) UuidInterface $id;

    /**
     * Returns the UUID of the collection to which this query belongs.
     */
    public private(set) UuidInterface $collectionId;

    /**
     * Returns the query type.
     */
    public private(set) QueryTypeInterface $queryType;

    /**
     * Returns if the query is dependent on a context, i.e. currently displayed page.
     */
    public bool $isContextual {
        get => $this->queryType->isContextual($this);
    }

    /**
     * Returns the list of all available locales in the query.
     *
     * @var string[]
     */
    public private(set) array $availableLocales;

    /**
     * Returns the main locale for the query.
     */
    public private(set) string $mainLocale;

    /**
     * Returns if the query is translatable.
     */
    public private(set) bool $isTranslatable;

    /**
     * Returns if the main translation of the query will be used
     * in case there are no prioritized translations.
     */
    public private(set) bool $isAlwaysAvailable;

    /**
     * Returns the locale of the currently loaded translation.
     */
    public private(set) string $locale;
}
