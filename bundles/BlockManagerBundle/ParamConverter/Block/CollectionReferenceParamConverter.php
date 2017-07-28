<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

class CollectionReferenceParamConverter extends ParamConverter
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
        return array('blockId', 'collectionIdentifier');
    }

    /**
     * Returns destination attribute name.
     *
     * @return string
     */
    public function getDestinationAttributeName()
    {
        return 'collectionReference';
    }

    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return CollectionReference::class;
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
        $locales = isset($values['locale']) ? array($values['locale']) : null;

        return $this->blockService->loadCollectionReference(
            $values['published'] ?
                $this->blockService->loadBlock($values['blockId'], $locales) :
                $this->blockService->loadBlockDraft($values['blockId'], $locales),
            $values['collectionIdentifier']
        );
    }
}
