<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionList;
use Netgen\Layouts\API\Values\Config\ConfigAwareValue;
use Netgen\Layouts\API\Values\Config\ConfigAwareValueTrait;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Exception\API\BlockException;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Block implements Value, ParameterCollectionInterface, ConfigAwareValue
{
    use ConfigAwareValueTrait;
    use HydratorTrait;
    use ParameterCollectionTrait;
    use ValueStatusTrait;

    public private(set) UuidInterface $id;

    /**
     * Returns the UUID of the layout where the block is located.
     */
    public private(set) UuidInterface $layoutId;

    /**
     * Returns the block definition.
     */
    public private(set) BlockDefinitionInterface $definition;

    /**
     * Returns view type which will be used to render this block.
     */
    public private(set) string $viewType;

    /**
     * Returns item view type which will be used to render block items.
     */
    public private(set) string $itemViewType;

    /**
     * Returns the human readable name of the block.
     */
    public private(set) string $name;

    /**
     * Returns the position of the block in the parent block or zone.
     */
    public private(set) int $position;

    /**
     * Returns the UUID of the parent block where this block is located.
     *
     * If block does not have a parent block, null is returned.
     */
    public private(set) ?UuidInterface $parentBlockId;

    /**
     * Returns the placeholder identifier in the parent block where this block is located.
     *
     * If block does not have a parent block, null is returned.
     */
    public private(set) ?string $parentPlaceholder;

    /**
     * Returns all placeholders from this block.
     */
    public private(set) PlaceholderList $placeholders {
        get => new PlaceholderList($this->placeholders->toArray());
    }

    /**
     * Returns all collections from this block.
     */
    public private(set) CollectionList $collections {
        get => CollectionList::fromArray($this->collections->toArray());
    }

    /**
     * Returns the list of all available locales in the block.
     *
     * @var string[]
     */
    public private(set) array $availableLocales;

    /**
     * Returns the main locale for the block.
     */
    public private(set) string $mainLocale;

    /**
     * Returns if the block is translatable.
     */
    public private(set) bool $isTranslatable;

    /**
     * Returns if the block is always available.
     */
    public private(set) bool $isAlwaysAvailable;

    /**
     * Returns the locale of the currently loaded translation.
     */
    public private(set) string $locale;

    /**
     * Returns if the block is dependent on a context, i.e. currently displayed page.
     */
    public bool $isContextual {
        get => $this->definition->isContextual($this);
    }

    private DynamicParameters $dynamicParameters;

    /**
     * Returns the specified placeholder.
     *
     * @throws \Netgen\Layouts\Exception\API\BlockException If the placeholder does not exist
     */
    public function getPlaceholder(string $identifier): Placeholder
    {
        return $this->placeholders->get($identifier) ??
            throw BlockException::noPlaceholder($identifier);
    }

    /**
     * Returns if block has a specified placeholder.
     */
    public function hasPlaceholder(string $identifier): bool
    {
        return $this->placeholders->containsKey($identifier);
    }

    /**
     * Returns the specified block collection.
     *
     * @throws \Netgen\Layouts\Exception\API\BlockException If the block collection does not exist
     */
    public function getCollection(string $identifier): Collection
    {
        return $this->collections->get($identifier) ??
            throw BlockException::noCollection($identifier);
    }

    /**
     * Returns if block has a specified collection.
     */
    public function hasCollection(string $identifier): bool
    {
        return $this->collections->containsKey($identifier);
    }

    /**
     * Returns the specified dynamic parameter value or null if parameter does not exist.
     */
    public function getDynamicParameter(string $parameter): mixed
    {
        $this->buildDynamicParameters();

        return $this->dynamicParameters->offsetGet($parameter);
    }

    /**
     * Returns if the object has a specified dynamic parameter.
     */
    public function hasDynamicParameter(string $parameter): bool
    {
        $this->buildDynamicParameters();

        return $this->dynamicParameters->offsetExists($parameter);
    }

    /**
     * Builds the dynamic parameters of the block from the block definition.
     */
    private function buildDynamicParameters(): void
    {
        $this->dynamicParameters ??= $this->definition->getDynamicParameters($this);
    }
}
