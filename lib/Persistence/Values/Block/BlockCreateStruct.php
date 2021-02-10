<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Block;

use Netgen\Layouts\Utils\HydratorTrait;

final class BlockCreateStruct
{
    use HydratorTrait;

    /**
     * Status of the new block.
     */
    public int $status;

    /**
     * Position of the new block.
     */
    public ?int $position;

    /**
     * Identifier of the block definition of the new block.
     */
    public string $definitionIdentifier;

    /**
     * View type of the new block.
     */
    public string $viewType;

    /**
     * Item view type of the new block.
     */
    public string $itemViewType;

    /**
     * Human readable name of the new block.
     */
    public string $name;

    /**
     * Flag indicating if the block is always available.
     */
    public bool $alwaysAvailable;

    /**
     * Flag indicating if the block is translatable.
     */
    public bool $isTranslatable;

    /**
     * The block parameters.
     *
     * @var array<string, mixed>
     */
    public array $parameters;

    /**
     * The block configuration.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $config;
}
