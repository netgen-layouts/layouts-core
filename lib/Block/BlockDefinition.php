<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\ValueObject;

class BlockDefinition extends ValueObject implements BlockDefinitionInterface
{
    use ParameterCollectionTrait;

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
    protected $collections = array();

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form[]
     */
    protected $forms = array();

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType[]
     */
    protected $viewTypes = array();

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\PluginInterface[]
     */
    protected $handlerPlugins = array();

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    protected $configDefinitions = array();

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function isTranslatable()
    {
        return $this->isTranslatable;
    }

    public function getCollections()
    {
        return $this->collections;
    }

    public function hasCollection($identifier)
    {
        return isset($this->collections[$identifier]);
    }

    public function getCollection($identifier)
    {
        if (!$this->hasCollection($identifier)) {
            throw BlockDefinitionException::noCollection($this->identifier, $identifier);
        }

        return $this->collections[$identifier];
    }

    public function getForms()
    {
        return $this->forms;
    }

    public function hasForm($formName)
    {
        return isset($this->forms[$formName]);
    }

    public function getForm($formName)
    {
        if (!$this->hasForm($formName)) {
            throw BlockDefinitionException::noForm($this->identifier, $formName);
        }

        return $this->forms[$formName];
    }

    public function getViewTypes()
    {
        return $this->viewTypes;
    }

    public function getViewTypeIdentifiers()
    {
        return array_keys($this->viewTypes);
    }

    public function hasViewType($viewType)
    {
        return isset($this->viewTypes[$viewType]);
    }

    public function getViewType($viewType)
    {
        if (!$this->hasViewType($viewType)) {
            throw BlockDefinitionException::noViewType($this->identifier, $viewType);
        }

        return $this->viewTypes[$viewType];
    }

    public function getDynamicParameters(Block $block)
    {
        $dynamicParams = new DynamicParameters();

        $this->handler->getDynamicParameters($dynamicParams, $block);

        foreach ($this->handlerPlugins as $handlerPlugin) {
            $handlerPlugin->getDynamicParameters($dynamicParams, $block);
        }

        return $dynamicParams;
    }

    public function isContextual(Block $block)
    {
        return $this->handler->isContextual($block);
    }

    public function getConfigDefinitions()
    {
        return $this->configDefinitions;
    }

    public function hasPlugin($className)
    {
        foreach ($this->handlerPlugins as $handlerPlugin) {
            if (is_a($handlerPlugin, $className, true)) {
                return true;
            }
        }

        return false;
    }
}
