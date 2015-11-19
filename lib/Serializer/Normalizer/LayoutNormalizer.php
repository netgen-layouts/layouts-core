<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutNormalizer implements NormalizerInterface
{
    /**
     * @var array
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
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $layoutIdentifier = $object->getIdentifier();

        return array(
            'id' => $object->getId(),
            'parent_id' => $object->getParentId(),
            'identifier' => $layoutIdentifier,
            'created_at' => $object->getCreated(),
            'updated_at' => $object->getModified(),
            'name' => $this->configuration->getLayoutConfig($layoutIdentifier)['name'],
        );
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Layout;
    }
}
