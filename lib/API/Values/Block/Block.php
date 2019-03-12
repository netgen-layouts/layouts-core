<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Block\Placeholder as APIPlaceholder;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionList;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\ValueStatusTrait;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Exception\API\BlockException;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Block implements Value, ParameterCollectionInterface, ConfigAwareValue
{
    use HydratorTrait;
    use ValueStatusTrait;
    use ConfigAwareValueTrait;
    use ParameterCollectionTrait;

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var int|string
     */
    private $layoutId;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private $definition;

    /**
     * @var string
     */
    private $viewType;

    /**
     * @var string
     */
    private $itemViewType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $position;

    /**
     * @var int|string|null
     */
    private $parentBlockId;

    /**
     * @var string|null
     */
    private $parentPlaceholder;

    /**
     * @var \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    private $placeholders = [];

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $collections;

    /**
     * @var \Netgen\BlockManager\Block\DynamicParameters
     */
    private $dynamicParameters;

    /**
     * @var string[]
     */
    private $availableLocales = [];

    /**
     * @var string
     */
    private $mainLocale;

    /**
     * @var bool
     */
    private $isTranslatable;

    /**
     * @var bool
     */
    private $alwaysAvailable;

    /**
     * @var string
     */
    private $locale;

    public function __construct()
    {
        $this->collections = $this->collections ?? new ArrayCollection();
    }

    /**
     * Returns the block ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the ID of the layout where the block is located.
     *
     * @return int|string
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Returns the block definition.
     */
    public function getDefinition(): BlockDefinitionInterface
    {
        return $this->definition;
    }

    /**
     * Returns view type which will be used to render this block.
     */
    public function getViewType(): string
    {
        return $this->viewType;
    }

    /**
     * Returns item view type which will be used to render block items.
     */
    public function getItemViewType(): string
    {
        return $this->itemViewType;
    }

    /**
     * Returns the human readable name of the block.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the position of the block in the parent block or zone.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Returns the ID of the parent block where this block is located.
     *
     * If block does not have a parent block, null is returned.
     *
     * @return int|string|null
     */
    public function getParentBlockId()
    {
        return $this->parentBlockId;
    }

    /**
     * Returns the placeholder identifier in the parent block where this block is located.
     *
     * If block does not have a parent block, null is returned.
     */
    public function getParentPlaceholder(): ?string
    {
        return $this->parentPlaceholder;
    }

    /**
     * Returns all placeholders from this block.
     */
    public function getPlaceholders(): PlaceholderList
    {
        return new PlaceholderList($this->placeholders);
    }

    /**
     * Returns the specified placeholder.
     *
     * @throws \Netgen\BlockManager\Exception\API\BlockException If the placeholder does not exist
     */
    public function getPlaceholder(string $identifier): APIPlaceholder
    {
        if ($this->hasPlaceholder($identifier)) {
            return $this->placeholders[$identifier];
        }

        throw BlockException::noPlaceholder($identifier);
    }

    /**
     * Returns if block has a specified placeholder.
     */
    public function hasPlaceholder(string $identifier): bool
    {
        return isset($this->placeholders[$identifier]);
    }

    /**
     * Returns all collections from this block.
     */
    public function getCollections(): CollectionList
    {
        return new CollectionList($this->collections->toArray());
    }

    /**
     * Returns the specified block collection.
     *
     * @throws \Netgen\BlockManager\Exception\API\BlockException If the block collection does not exist
     */
    public function getCollection(string $identifier): Collection
    {
        if (!$this->hasCollection($identifier)) {
            throw BlockException::noCollection($identifier);
        }

        return $this->collections->get($identifier);
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
     *
     * @return mixed
     */
    public function getDynamicParameter(string $parameter)
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
     * Returns if the block is dependent on a context, i.e. currently displayed page.
     */
    public function isContextual(): bool
    {
        return $this->definition->isContextual($this);
    }

    /**
     * Returns the list of all available locales in the block.
     *
     * @return string[]
     */
    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    /**
     * Returns the main locale for the block.
     */
    public function getMainLocale(): string
    {
        return $this->mainLocale;
    }

    /**
     * Returns if the block is translatable.
     */
    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    /**
     * Returns if the block is always available.
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

    /**
     * Builds the dynamic parameters of the block from the block definition.
     */
    private function buildDynamicParameters(): void
    {
        $this->dynamicParameters = $this->dynamicParameters ?? $this->definition->getDynamicParameters($this);
    }
}
