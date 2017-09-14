<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

class BlockParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    public function getSourceAttributeNames()
    {
        return array('blockId');
    }

    public function getDestinationAttributeName()
    {
        return 'block';
    }

    public function getSupportedClass()
    {
        return Block::class;
    }

    public function loadValueObject(array $values)
    {
        $locales = isset($values['locale']) ? array($values['locale']) : null;

        if ($values['published']) {
            return $this->blockService->loadBlock($values['blockId'], $locales);
        }

        return $this->blockService->loadBlockDraft($values['blockId'], $locales);
    }
}
