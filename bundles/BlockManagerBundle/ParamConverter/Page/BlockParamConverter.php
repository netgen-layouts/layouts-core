<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Page;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Page\Block;

class BlockParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     */
    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Returns source attribute name.
     *
     * @return array
     */
    public function getSourceAttributeNames()
    {
        return array('blockId');
    }

    /**
     * Returns destination attribute name.
     *
     * @return string
     */
    public function getDestinationAttributeName()
    {
        return 'block';
    }

    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return Block::class;
    }

    /**
     * Returns the value object.
     *
     * @param array $values
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function loadValueObject(array $values)
    {
        return $this->blockService->loadBlock($values['blockId']);
    }
}
