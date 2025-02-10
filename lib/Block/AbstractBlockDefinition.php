<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Collection;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ConfigProviderInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface;
use Netgen\Layouts\Config\ConfigDefinitionAwareTrait;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;

use function array_key_exists;
use function array_keys;
use function is_a;

abstract class AbstractBlockDefinition implements BlockDefinitionInterface
{
    use ConfigDefinitionAwareTrait;
    use HydratorTrait;
    use ParameterDefinitionCollectionTrait;

    protected string $identifier;

    protected string $name;

    protected ?string $icon;

    protected bool $isTranslatable;

    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\Configuration\Collection[]
     */
    protected array $collections = [];

    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\Configuration\Form[]
     */
    protected array $forms = [];

    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\Handler\PluginInterface[]
     */
    protected array $handlerPlugins = [];

    protected ConfigProviderInterface $configProvider;

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

    public function getViewTypes(?Block $block = null): array
    {
        return $this->configProvider->provideViewTypes($block);
    }

    public function getViewTypeIdentifiers(?Block $block = null): array
    {
        return array_keys($this->configProvider->provideViewTypes($block));
    }

    public function hasViewType(string $viewType, ?Block $block = null): bool
    {
        return array_key_exists($viewType, $this->configProvider->provideViewTypes($block));
    }

    public function getViewType(string $viewType, ?Block $block = null): ViewType
    {
        $viewTypes = $this->configProvider->provideViewTypes($block);

        if (!array_key_exists($viewType, $viewTypes)) {
            throw BlockDefinitionException::noViewType($this->identifier, $viewType);
        }

        return $viewTypes[$viewType];
    }

    public function getDynamicParameters(Block $block): DynamicParameters
    {
        $dynamicParams = new DynamicParameters();

        $this->getHandler()->getDynamicParameters($dynamicParams, $block);

        foreach ($this->handlerPlugins as $handlerPlugin) {
            $handlerPlugin->getDynamicParameters($dynamicParams, $block);
        }

        return $dynamicParams;
    }

    public function isContextual(Block $block): bool
    {
        return $this->getHandler()->isContextual($block);
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

    /**
     * Returns the block definition plugin with provided FQCN.
     *
     * @param class-string $className
     */
    public function getPlugin(string $className): PluginInterface
    {
        foreach ($this->handlerPlugins as $handlerPlugin) {
            if (is_a($handlerPlugin, $className, true)) {
                return $handlerPlugin;
            }
        }

        throw BlockDefinitionException::noPlugin($this->identifier, $className);
    }

    public function getPlugins(): array
    {
        return $this->handlerPlugins;
    }
}
