<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Collection\QueryType\QueryTypeInterface;
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

    public function isContextual(): bool
    {
        return $this->queryType->isContextual($this);
    }

    public function getQueryType(): QueryTypeInterface
    {
        return $this->queryType;
    }

    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    public function getMainLocale(): string
    {
        return $this->mainLocale;
    }

    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    public function isAlwaysAvailable(): bool
    {
        return $this->alwaysAvailable;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
