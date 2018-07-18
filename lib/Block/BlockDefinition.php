<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Config\ConfigDefinitionAwareTrait;
use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

class BlockDefinition implements BlockDefinitionInterface
{
    use HydratorTrait;
    use ParameterDefinitionCollectionTrait;
    use ConfigDefinitionAwareTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var bool
     */
    protected $isTranslatable = false;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection[]
     */
    protected $collections = [];

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form[]
     */
    protected $forms = [];

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType[]
     */
    protected $viewTypes = [];

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface[]
     */
    protected $handlerPlugins = [];

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    public function getCollections(): array
    {
        return $this->collections;
    }

    public function hasCollection(string $identifier): bool
    {
        return array_key_exists($identifier, $this->collections);
    }

    public function getCollection(string $identifier): Collection
    {
        if (!$this->hasCollection($identifier)) {
            throw BlockDefinitionException::noCollection($this->identifier, $identifier);
        }

        return $this->collections[$identifier];
    }

    public function getForms(): array
    {
        return $this->forms;
    }

    public function hasForm(string $formName): bool
    {
        return array_key_exists($formName, $this->forms);
    }

    public function getForm(string $formName): Form
    {
        if (!$this->hasForm($formName)) {
            throw BlockDefinitionException::noForm($this->identifier, $formName);
        }

        return $this->forms[$formName];
    }

    public function getViewTypes(): array
    {
        return $this->viewTypes;
    }

    public function getViewTypeIdentifiers(): array
    {
        return array_keys($this->viewTypes);
    }

    public function hasViewType(string $viewType): bool
    {
        return array_key_exists($viewType, $this->viewTypes);
    }

    public function getViewType(string $viewType): ViewType
    {
        if (!$this->hasViewType($viewType)) {
            throw BlockDefinitionException::noViewType($this->identifier, $viewType);
        }

        return $this->viewTypes[$viewType];
    }

    public function getDynamicParameters(Block $block): DynamicParameters
    {
        $dynamicParams = new DynamicParameters();

        $this->handler->getDynamicParameters($dynamicParams, $block);

        foreach ($this->handlerPlugins as $handlerPlugin) {
            $handlerPlugin->getDynamicParameters($dynamicParams, $block);
        }

        return $dynamicParams;
    }

    public function isContextual(Block $block): bool
    {
        return $this->handler->isContextual($block);
    }

    public function hasPlugin(string $className): bool
    {
        foreach ($this->handlerPlugins as $handlerPlugin) {
            if (is_a($handlerPlugin, $className, true)) {
                return true;
            }
        }

        return false;
    }
}
