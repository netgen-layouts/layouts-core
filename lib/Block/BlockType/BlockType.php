<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockType;

use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Utils\HydratorTrait;

/**
 * Block type represents the starting configuration of the block. E.g. while
 * block definition specifies what view types the block can have, block type
 * specifies the exact view type the new block will have once created. The
 * same goes for item view types and all of the block parameters.
 *
 * @final
 */
class BlockType
{
    use HydratorTrait;

    private string $identifier;

    private bool $isEnabled;

    private string $name;

    private ?string $icon;

    private BlockDefinitionInterface $definition;

    /**
     * @var array<string, mixed>
     */
    private array $defaults = [];

    /**
     * Returns the block type identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns if the block type is enabled or not.
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Returns the block type name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the block type icon.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Returns the block definition.
     */
    public function getDefinition(): BlockDefinitionInterface
    {
        return $this->definition;
    }

    /**
     * Returns the default block values.
     *
     * @return array<string, mixed>
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Returns the default block name.
     */
    public function getDefaultName(): string
    {
        return $this->defaults['name'] ?? '';
    }

    /**
     * Returns the default block view type.
     */
    public function getDefaultViewType(): string
    {
        return $this->defaults['view_type'] ?? '';
    }

    /**
     * Returns the default block item view type.
     */
    public function getDefaultItemViewType(): string
    {
        return $this->defaults['item_view_type'] ?? '';
    }

    /**
     * Returns the default block parameters.
     *
     * @return array<string, mixed>
     */
    public function getDefaultParameters(): array
    {
        return $this->defaults['parameters'] ?? [];
    }
}
