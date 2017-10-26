<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class ContainerDefinition implements ContainerDefinitionInterface
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
    private $viewTypes;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $viewTypes
     * @param \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface $handler
     */
    public function __construct($identifier, array $viewTypes = array(), ContainerDefinitionHandlerInterface $handler = null)
    {
        $this->identifier = $identifier;
        $this->viewTypes = $viewTypes;

        $this->handler = $handler ?: new ContainerDefinitionHandler();
    }

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns placeholder identifiers.
     *
     * @return string[]
     */
    public function getPlaceholders()
    {
        return $this->handler->getPlaceholderIdentifiers();
    }

    /**
     * Returns the list of parameters in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->handler->getParameters();
    }

    /**
     * Returns the parameter with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter($parameterName)
    {
        if ($this->hasParameter($parameterName)) {
            return $this->handler->getParameters()[$parameterName];
        }

        throw new InvalidArgumentException('parameterName', 'Parameter is missing.');
    }

    /**
     * Returns if the parameter with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return isset($this->handler->getParameters()[$parameterName]);
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Block\DynamicParameters
     */
    public function getDynamicParameters(Block $block)
    {
        $dynamicParams = new DynamicParameters();

        $this->handler->getDynamicParameters($dynamicParams, $block);

        return $dynamicParams;
    }

    /**
     * Returns if the provided block is dependent on a context, i.e. current request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function isContextual(Block $block)
    {
        return $this->handler->isContextual($block);
    }

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public function getConfig()
    {
        $viewTypes = array();
        foreach ($this->viewTypes as $viewType => $itemTypes) {
            $itemViewTypes = array();
            foreach ($itemTypes as $itemType) {
                $itemViewTypes[$itemType] = new ItemViewType(
                    array(
                        'identifier' => $itemType,
                        'name' => $itemType,
                    )
                );
            }

            $viewTypes[$viewType] = new ViewType(
                array(
                    'identifier' => $viewType,
                    'name' => $viewType,
                    'itemViewTypes' => $itemViewTypes,
                )
            );
        }

        return new Configuration(
            array(
                'identifier' => $this->identifier,
                'viewTypes' => $viewTypes,
            )
        );
    }

    /**
     * Returns if this block definition is a container.
     *
     * @return bool
     */
    public function isContainer()
    {
        return !empty($this->handler->getPlaceholderIdentifiers());
    }

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return false;
    }

    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions()
    {
        return array();
    }

    /**
     * Returns if the block definition has a plugin with provided FQCN.
     *
     * @param string $className
     *
     * @return bool
     */
    public function hasPlugin($className)
    {
        return false;
    }
}
