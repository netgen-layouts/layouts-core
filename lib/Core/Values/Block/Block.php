<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\API\Values\Block\Placeholder as APIPlaceholder;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Core\Values\ValueStatusTrait;
use Netgen\BlockManager\Exception\Core\BlockException;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\Value;

final class Block extends Value implements APIBlock
{
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
    private $parentPosition;

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

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->collections = $this->collections ?? new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLayoutId()
    {
        return $this->layoutId;
    }

    public function getDefinition(): BlockDefinitionInterface
    {
        return $this->definition;
    }

    public function getViewType(): string
    {
        return $this->viewType;
    }

    public function getItemViewType(): string
    {
        return $this->itemViewType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentPosition(): int
    {
        return $this->parentPosition;
    }

    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }

    public function getPlaceholder(string $identifier): APIPlaceholder
    {
        if ($this->hasPlaceholder($identifier)) {
            return $this->placeholders[$identifier];
        }

        throw BlockException::noPlaceholder($identifier);
    }

    public function hasPlaceholder(string $identifier): bool
    {
        return isset($this->placeholders[$identifier]);
    }

    public function getCollections(): array
    {
        return $this->collections->toArray();
    }

    public function getCollection(string $identifier): Collection
    {
        if (!$this->hasCollection($identifier)) {
            throw BlockException::noCollection($identifier);
        }

        return $this->collections->get($identifier);
    }

    public function hasCollection(string $identifier): bool
    {
        return $this->collections->containsKey($identifier);
    }

    public function getDynamicParameter(string $parameter)
    {
        $this->buildDynamicParameters();

        return $this->dynamicParameters->offsetGet($parameter);
    }

    public function hasDynamicParameter(string $parameter): bool
    {
        $this->buildDynamicParameters();

        return $this->dynamicParameters->offsetExists($parameter);
    }

    public function isContextual(): bool
    {
        return $this->definition->isContextual($this);
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

    /**
     * Builds the dynamic parameters of the block from the block definition.
     */
    private function buildDynamicParameters(): void
    {
        $this->dynamicParameters = $this->dynamicParameters ?? $this->definition->getDynamicParameters($this);
    }
}
