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
     * Returns the block definition human readable name.
     *
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * Returns the block definition icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return '';
    }

    /**
     * Returns if the block will be translatable when created.
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return false;
    }

    /**
     * Returns all collections.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection[]
     */
    public function getCollections()
    {
        return array();
    }

    /**
     * Returns if the block definition has a collection with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasCollection($identifier)
    {
        return false;
    }

    /**
     * Returns the collection for provided collection identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If collection does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection
     */
    public function getCollection($identifier)
    {
    }

    /**
     * Returns all forms.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form[]
     */
    public function getForms()
    {
        return array();
    }

    /**
     * Returns if the block definition has a form with provided name.
     *
     * @param string $formName
     *
     * @return bool
     */
    public function hasForm($formName)
    {
        return false;
    }

    /**
     * Returns the form for provided form name.
     *
     * @param string $formName
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If form does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form
     */
    public function getForm($formName)
    {
    }

    /**
     * Returns the block definition view types.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType[]
     */
    public function getViewTypes()
    {
        return $this->viewTypes;
    }

    /**
     * Returns the block definition view type identifiers.
     *
     * @return string[]
     */
    public function getViewTypeIdentifiers()
    {
        return array_keys($this->viewTypes);
    }

    /**
     * Returns if the block definition has a view type with provided identifier.
     *
     * @param string $viewType
     *
     * @return bool
     */
    public function hasViewType($viewType)
    {
        return array_key_exists($viewType, $this->viewTypes);
    }

    /**
     * Returns the view type with provided identifier.
     *
     * @param string $viewType
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockDefinitionException If view type does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType
     */
    public function getViewType($viewType)
    {
        return $this->viewTypes[$viewType];
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
     * Returns the list of parameter definitions in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameterDefinitions()
    {
        return $this->handler->getParameterDefinitions();
    }

    /**
     * Returns the parameter definition with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    public function getParameterDefinition($parameterName)
    {
        if ($this->hasParameterDefinition($parameterName)) {
            return $this->handler->getParameterDefinitions()[$parameterName];
        }

        throw new InvalidArgumentException('parameterName', 'Parameter is missing.');
    }

    /**
     * Returns if the parameter definition with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameterDefinition($parameterName)
    {
        return isset($this->handler->getParameterDefinitions()[$parameterName]);
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
