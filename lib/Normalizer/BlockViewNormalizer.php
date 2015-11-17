<?php

namespace Netgen\BlockManager\Normalizer;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewRendererInterface;

class BlockViewNormalizer extends BlockNormalizer
{
    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     * @param \Netgen\BlockManager\View\ViewRendererInterface $viewRenderer
     */
    public function __construct(ConfigurationInterface $configuration, ViewRendererInterface $viewRenderer)
    {
        parent::__construct($configuration);

        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\View\BlockViewInterface $object
     * @param string $format
     * @param array $context
     *
     * @throws \RuntimeException If configuration for block does not exist
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data = parent::normalize($object->getBlock());
        $data['html'] = $this->viewRenderer->renderView($object);

        return $data;
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
        return $data instanceof BlockViewInterface;
    }
}
