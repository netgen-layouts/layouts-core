<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class BlockDefinition implements BlockDefinitionInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler
     */
    protected $handler;

    /**
     * @var array
     */
    protected $viewTypes = array();

    /**
     * @var array
     */
    protected $collections = array();

    /**
     * @var bool
     */
    protected $hasCollection;

    /**
     * @var bool
     */
    protected $isTranslatable;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    protected $configDefinitions;

    /**
     * @var bool
     */
    protected $hasPlugin;

    public function __construct(
        $identifier,
        array $viewTypes = array(),
        BlockDefinitionHandlerInterface $handler = null,
        $hasCollection = false,
        $isTranslatable = false,
        array $configDefinitions = array(),
        $hasPlugin = false
    ) {
        $this->identifier = $identifier;
        $this->hasCollection = $hasCollection;
        $this->isTranslatable = $isTranslatable;

        $this->handler = $handler ?: new BlockDefinitionHandler();
        $this->configDefinitions = $configDefinitions;
        $this->hasPlugin = $hasPlugin;

        foreach ($viewTypes as $viewType => $itemTypes) {
            $itemViewTypes = array();
            foreach ($itemTypes as $itemType) {
                $itemViewTypes[$itemType] = new ItemViewType(
                    array(
                        'identifier' => $itemType,
                        'name' => $itemType,
                    )
                );
            }

            $this->viewTypes[$viewType] = new ViewType(
                array(
                    'identifier' => $viewType,
                    'name' => $viewType,
                    'itemViewTypes' => $itemViewTypes,
                )
            );
        }

        if ($this->hasCollection) {
            $this->collections['default'] = new Collection(
                array(
                    'identifier' => 'default',
                )
            );
        }
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getName()
    {
        return '';
    }

    public function getIcon()
    {
        return '';
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
        return array_key_exists($identifier, $this->collections);
    }

    public function getCollection($identifier)
    {
        return $this->collections[$identifier];
    }

    public function getForms()
    {
        return array();
    }

    public function hasForm($formName)
    {
        return false;
    }

    public function getForm($formName)
    {
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
        return array_key_exists($viewType, $this->viewTypes);
    }

    public function getViewType($viewType)
    {
        return $this->viewTypes[$viewType];
    }

    public function getParameterDefinitions()
    {
        return $this->handler->getParameterDefinitions();
    }

    public function getParameterDefinition($parameterName)
    {
        if ($this->hasParameterDefinition($parameterName)) {
            return $this->handler->getParameterDefinitions()[$parameterName];
        }

        throw new InvalidArgumentException('parameterName', 'Parameter is missing.');
    }

    public function hasParameterDefinition($parameterName)
    {
        return isset($this->handler->getParameterDefinitions()[$parameterName]);
    }

    public function getDynamicParameters(Block $block)
    {
        $dynamicParams = new DynamicParameters();

        $this->handler->getDynamicParameters($dynamicParams, $block);

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
        return $this->hasPlugin;
    }
}
