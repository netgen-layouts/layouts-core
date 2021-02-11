<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Doctrine\Common\Collections\Collection as DoctrineCollection;
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

    private UuidInterface $id;

    private UuidInterface $layoutId;

    private BlockDefinitionInterface $definition;

    private string $viewType;

    private string $itemViewType;

    private string $name;

    private int $position;

    private ?UuidInterface $parentBlockId;

    private ?string $parentPlaceholder;

    /**
     * @var \Netgen\Layouts\API\Values\Block\Placeholder[]
     */
    private array $placeholders;

    /**
     * @var \Doctrine\Common\Collections\Collection<string, \Netgen\Layouts\API\Values\Collection\Collection>
     */
    private DoctrineCollection $collections;

    private DynamicParameters $dynamicParameters;

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
     * Returns the UUID of the layout where the block is located.
     */
    public function getLayoutId(): UuidInterface
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
     * Returns the UUID of the parent block where this block is located.
     *
     * If block does not have a parent block, null is returned.
     */
    public function getParentBlockId(): ?UuidInterface
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
     * @throws \Netgen\Layouts\Exception\API\BlockException If the placeholder does not exist
     */
    public function getPlaceholder(string $identifier): Placeholder
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
     * @throws \Netgen\Layouts\Exception\API\BlockException If the block collection does not exist
     */
    public function getCollection(string $identifier): Collection
    {
        $collection = $this->collections->get($identifier);
        if ($collection === null) {
            throw BlockException::noCollection($identifier);
        }

        return $collection;
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
        $this->dynamicParameters ??= $this->definition->getDynamicParameters($this);
    }
}
