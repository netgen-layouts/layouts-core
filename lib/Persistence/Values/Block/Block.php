<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Block;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Block extends Value
{
    use HydratorTrait;

    /**
     * Block ID.
     */
    public int $id;

    /**
     * Block UUID.
     */
    public string $uuid;

    /**
     * ID of the layout where block is located.
     */
    public int $layoutId;

    /**
     * UUID of the layout where block is located.
     */
    public string $layoutUuid;

    /**
     * The depth of the block in the tree.
     */
    public int $depth;

    /**
     * Materialized path of the block.
     */
    public string $path;

    /**
     * ID of the parent block or null if block has no parent.
     */
    public ?int $parentId;

    /**
     * UUID of the parent block or null if block has no parent.
     */
    public ?string $parentUuid;

    /**
     * Placeholder to which this block belongs in the parent block or null if block has no parent.
     */
    public ?string $placeholder;

    /**
     * Position of the block in the parent block or null if block has no parent.
     */
    public ?int $position;

    /**
     * Block definition identifier.
     */
    public string $definitionIdentifier;

    /**
     * Block parameters. Keys are locales, values are parameters in the specific locale.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $parameters;

    /**
     * Block configuration.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $config;

    /**
     * View type which will be used to render this block.
     */
    public string $viewType;

    /**
     * Item view type which will be used to render block items.
     */
    public string $itemViewType;

    /**
     * Human readable name of this block.
     */
    public string $name;

    /**
     * Returns if the block is translatable.
     */
    public bool $isTranslatable;

    /**
     * Returns the main locale of this block.
     */
    public string $mainLocale;

    /**
     * Returns the list of all locales available in this block.
     *
     * @var string[]
     */
    public array $availableLocales;

    /**
     * Returns if main locale of this block will be always available.
     */
    public bool $alwaysAvailable;
}
