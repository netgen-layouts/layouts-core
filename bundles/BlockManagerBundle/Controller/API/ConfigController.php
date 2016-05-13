<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Serializer\Values\ValueArray;

class ConfigController extends Controller
{
    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Serializes the block types.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function getBlockTypes()
    {
        $configBlockTypeGroups = $this->configuration->getParameter('block_type_groups');
        $configBlockTypes = $this->configuration->getParameter('block_types');

        $blockTypeGroups = array();
        foreach ($configBlockTypeGroups as $identifier => $blockTypeGroup) {
            $blockTypeGroups[] = array(
                'identifier' => $identifier,
            ) + $blockTypeGroup;
        }

        $blockTypes = array();
        foreach ($configBlockTypes as $identifier => $blockType) {
            $blockTypes[] = array(
                'identifier' => $identifier,
            ) + $blockType;
        }

        $data = array(
            'block_type_groups' => $blockTypeGroups,
            'block_types' => $blockTypes,
        );

        return new ValueArray($data);
    }

    /**
     * Serializes the collection sources.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function getSources()
    {
        $sources = array();
        $configSources = $this->configuration->getParameter('sources');

        foreach ($configSources as $identifier => $source) {
            $sources[] = array(
                'identifier' => $identifier
            ) + $source;
        }

        return new ValueArray($sources);
    }
}
