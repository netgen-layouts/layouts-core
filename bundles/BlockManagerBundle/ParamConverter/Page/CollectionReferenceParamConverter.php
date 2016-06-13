<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Page;

use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;
use Netgen\BlockManager\API\Service\BlockService;

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
        return $this->blockService->loadCollectionReference(
            $this->blockService->loadBlockDraft($values['blockId']),
            $values['collectionIdentifier']
        );
    }
}
