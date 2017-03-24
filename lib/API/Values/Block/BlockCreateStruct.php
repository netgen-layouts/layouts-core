<?php

namespace Netgen\BlockManager\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class BlockCreateStruct extends ParameterStruct implements ConfigAwareStruct
{
    use ConfigAwareStructTrait;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    public $definition;

    /**
     * @var string
     */
    public $viewType;

    /**
     * @var string
     */
    public $itemViewType;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \Netgen\BlockManager\API\Values\Block\PlaceholderCreateStruct[]
     */
    protected $placeholderStructs = array();

    /**
     * Sets the placeholder create struct to block create struct.
     *
     * @param $identifier
     * @param \Netgen\BlockManager\API\Values\Block\PlaceholderCreateStruct $placeholderStruct
     */
    public function setPlaceholderStruct($identifier, PlaceholderCreateStruct $placeholderStruct)
    {
        $this->placeholderStructs[$identifier] = $placeholderStruct;
    }

    /**
     * Returns if the struct has a placeholder struct with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPlaceholderStruct($identifier)
    {
        return array_key_exists($identifier, $this->placeholderStructs);
    }

    /**
     * Gets the placeholder create struct with provided identifier.
     *
     * @param $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If placeholder struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Block\PlaceholderCreateStruct
     */
    public function getPlaceholderStruct($identifier)
    {
        if (!$this->hasPlaceholderStruct($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'Placeholder create struct with identifier "%s" does not exist in the struct.',
                    $identifier
                )
            );
        }

        return $this->placeholderStructs[$identifier];
    }

    /**
     * Returns all placeholder create structs from the struct.
     *
     * @return \Netgen\BlockManager\API\Values\Block\PlaceholderCreateStruct[]
     */
    public function getPlaceholderStructs()
    {
        return $this->placeholderStructs;
    }
}
