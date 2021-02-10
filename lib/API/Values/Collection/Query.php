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

    private UuidInterface $id;

    private UuidInterface $collectionId;

    private QueryTypeInterface $queryType;

    /**
     * @var string[]
     */
    private array $availableLocales;

    private string $mainLocale;

    private bool $isTranslatable;

    private bool $alwaysAvailable;

    private string $locale;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Returns the UUID of the collection to which this query belongs.
     */
    public function getCollectionId(): UuidInterface
    {
        return $this->collectionId;
    }

    /**
     * Returns if the query is dependent on a context, i.e. currently displayed page.
     */
    public function isContextual(): bool
    {
        return $this->queryType->isContextual($this);
    }

    /**
     * Returns the query type.
     */
    public function getQueryType(): QueryTypeInterface
    {
        return $this->queryType;
    }

    /**
     * Returns the list of all available locales in the query.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    /**
     * Returns the main locale for the query.
     */
    public function getMainLocale(): string
    {
        return $this->mainLocale;
    }

    /**
     * Returns if the query is translatable.
     */
    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    /**
     * Returns if the main translation of the query will be used
     * in case there are no prioritized translations.
     */
    public function isAlwaysAvailable(): bool
    {
        return $this->alwaysAvailable;
    }

    /**
     * Returns the locale of the currently loaded translation.
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
