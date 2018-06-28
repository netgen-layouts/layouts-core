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
     */
    public function getDefinition(): BlockDefinitionInterface;

    /**
     * Returns view type which will be used to render this block.
     */
    public function getViewType(): string;

    /**
     * Returns item view type which will be used to render block items.
     */
    public function getItemViewType(): string;

    /**
     * Returns the human readable name of the block.
     */
    public function getName(): ?string;

    /**
     * Returns the position of the block in the parent block or zone.
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
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the placeholder does not exist
     */
    public function getPlaceholder(string $identifier): Placeholder;

    /**
     * Returns if block has a specified placeholder.
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
     * @throws \Netgen\BlockManager\Exception\Core\BlockException If the block collection does not exist
     */
    public function getCollection(string $identifier): Collection;

    /**
     * Returns if block has a specified collection.
     */
    public function hasCollection(string $identifier): bool;

    /**
     * Returns the specified dynamic parameter value or null if parameter does not exist.
     *
     * @return mixed
     */
    public function getDynamicParameter(string $parameter);

    /**
     * Returns if the object has a specified dynamic parameter.
     */
    public function hasDynamicParameter(string $parameter): bool;

    /**
     * Returns if the block is dependent on a context, i.e. currently displayed page.
     */
    public function isContextual(): bool;

    /**
     * Returns the list of all available locales in the block.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array;

    /**
     * Returns the main locale for the block.
     */
    public function getMainLocale(): string;

    /**
     * Returns if the block is translatable.
     */
    public function isTranslatable(): bool;

    /**
     * Returns if the block is always available.
     */
    public function isAlwaysAvailable(): bool;

    /**
     * Returns the locale of the currently loaded translation.
     */
    public function getLocale(): string;
}
