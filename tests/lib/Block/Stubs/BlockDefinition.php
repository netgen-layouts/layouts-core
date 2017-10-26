<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
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
    protected $viewTypes;

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
     * Constructor.
     *
     * @param string $identifier
     * @param array $viewTypes
     * @param \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface $handler
     * @param bool $hasCollection
     * @param bool $isTranslatable
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     */
    public function __construct(
        $identifier,
        array $viewTypes = array(),
        BlockDefinitionHandlerInterface $handler = null,
        $hasCollection = false,
        $isTranslatable = false,
        array $configDefinitions = array()
    ) {
        $this->identifier = $identifier;
        $this->viewTypes = $viewTypes;
        $this->hasCollection = $hasCollection;
        $this->isTranslatable = $isTranslatable;

        $this->handler = $handler ?: new BlockDefinitionHandler();
        $this->configDefinitions = $configDefinitions;
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

        $collections = array();
        if ($this->hasCollection) {
            $collections['default'] = new Collection(
                array(
                    'identifier' => 'default',
                )
            );
        }

        return new Configuration(
            array(
                'identifier' => $this->identifier,
                'isTranslatable' => $this->isTranslatable,
                'collections' => $collections,
                'viewTypes' => $viewTypes,
            )
        );
    }

    /**
     * Returns the available config definitions.
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface[]
     */
    public function getConfigDefinitions()
    {
        return $this->configDefinitions;
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
