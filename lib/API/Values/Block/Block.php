<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\API\Values\ParameterBasedValue;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\BlockDefinitionInterface;

interface Block extends Value, ParameterBasedValue, ConfigAwareValue
{
    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the ID of the layout where the block is located.
     *
     * @return int|string
     */
    public function getLayoutId();

    /**
     * Returns the block definition.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public function getDefinition(): BlockDefinitionInterface;

    /**
     * Returns view type which will be used to render this block.
     *
     * @return string
     */
    public function getViewType(): string;

    /**
     * Returns item view type which will be used to render block items.
     *
     * @return string
     */
    public function getItemViewType(): string;

    /**
     * Returns the human readable name of the block.
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Returns the position of the block in the parent block or zone.
     *
     * @return int
     */
    public function getParentPosition(): int;

    /**
     * Returns all placeholders from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    public function getPlaceholders(): array;

    /**
     * Returns the specified placeholder.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the placeholder does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    public function getPlaceholder(string $identifier): Placeholder;

    /**
     * Returns if block has a specified placeholder.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPlaceholder(string $identifier): bool;

    /**
     * Returns all collections from this block.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    public function getCollections(): array;

    /**
     * Returns the specified block collection.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the block collection does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection
     */
    public function getCollection(string $identifier): Collection;

    /**
     * Returns if block has a specified collection.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasCollection(string $identifier): bool;

    /**
     * Returns the specified dynamic parameter value or null if parameter does not exist.
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getDynamicParameter(string $parameter);

    /**
     * Returns if the object has a specified dynamic parameter.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasDynamicParameter(string $parameter): bool;

    /**
     * Returns if the block is dependent on a context, i.e. currently displayed page.
     *
     * @return bool
     */
    public function isContextual(): bool;

    /**
     * Returns if the block is is cacheable or not.
     *
     * @return bool
     */
    public function isCacheable(): bool;

    /**
     * Returns the list of all available locales in the block.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array;

    /**
     * Returns the main locale for the block.
     *
     * @return string
     */
    public function getMainLocale(): string;

    /**
     * Returns if the block is translatable.
     *
     * @return bool
     */
    public function isTranslatable(): bool;

    /**
     * Returns if the block is always available.
     *
     * @return bool
     */
    public function isAlwaysAvailable(): bool;

    /**
     * Returns the locale of the currently loaded translation.
     *
     * @return string
     */
    public function getLocale(): string;
}
