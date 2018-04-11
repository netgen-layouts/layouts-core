<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Exception\InvalidArgumentException;

final class ContainerDefinition implements ContainerDefinitionInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var \Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler
     */
    private $handler;

    /**
     * @var array
     */
    private $viewTypes = array();

    public function __construct($identifier, array $viewTypes = array(), ContainerDefinitionHandlerInterface $handler = null)
    {
        $this->identifier = $identifier;

        $this->handler = $handler ?: new ContainerDefinitionHandler();

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
        return false;
    }

    public function getCollections()
    {
        return array();
    }

    public function hasCollection($identifier)
    {
        return false;
    }

    public function getCollection($identifier)
    {
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

    public function getPlaceholders()
    {
        return $this->handler->getPlaceholderIdentifiers();
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

    public function isContainer()
    {
        return !empty($this->handler->getPlaceholderIdentifiers());
    }

    public function isDynamicContainer()
    {
        return false;
    }

    public function getConfigDefinitions()
    {
        return array();
    }

    public function hasPlugin($className)
    {
        return false;
    }
}
