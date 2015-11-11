<?php

namespace Netgen\BlockManager\Normalizer;

use Netgen\BlockManager\View\BlockViewInterface;
use Netgen\BlockManager\View\Renderer\ViewRenderer;

class BlockViewNormalizer extends BlockNormalizer
{
    /**
     * @var \Netgen\BlockManager\View\Renderer\ViewRenderer
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Renderer\ViewRenderer $viewRenderer
     * @param array $blockConfig
     */
    public function __construct(array $blockConfig, ViewRenderer $viewRenderer)
    {
        parent::__construct($blockConfig);

        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\View\BlockViewInterface $object
     * @param string $format
     * @param array $context
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
