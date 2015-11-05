<?php

namespace Netgen\BlockManager\Core\Service;

use Netgen\BlockManager\API\Service\BlockService as BlockServiceInterface;
use Netgen\BlockManager\Persistence\Handler\Block as BlockHandler;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\BlockCreateStruct as APIBlockCreateStruct;
use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Page\Block as PersistenceBlock;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Values\Page\Zone as APIZone;
use Netgen\BlockManager\Exceptions\InvalidArgumentException;

class BlockService implements BlockServiceInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Block
     */
    protected $blockHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\Persistence\Handler\Block $blockHandler
     */
    public function __construct(LayoutService $layoutService, BlockHandler $blockHandler)
    {
        $this->layoutService = $layoutService;
        $this->blockHandler = $blockHandler;
    }

    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If block ID has an invalid or empty value
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function loadBlock($blockId)
    {
        if (!is_int($blockId) && !is_string($blockId)) {
            throw new InvalidArgumentException('blockId', $blockId, 'Value must be an integer or a string.');
        }

        if (empty($blockId)) {
            throw new InvalidArgumentException('blockId', $blockId, 'Value must not be empty.');
        }

        return $this->buildDomainBlockObject(
            $this->blockHandler->loadBlock($blockId)
        );
    }

    /**
     * Loads blocks belonging to specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function loadZoneBlocks(APIZone $zone)
    {
        $persistenceBlocks = $this->blockHandler->loadZoneBlocks($zone->getId());

        $blocks = array();
        foreach ($persistenceBlocks as $persistenceBlock) {
            $blocks[] = $this->buildDomainBlockObject($persistenceBlock);
        }

        return $blocks;
    }

    /**
     * Loads blocks belonging to specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function loadLayoutBlocks(APILayout $layout)
    {
        $blocks = array();

        foreach ($layout->getZones() as $zone) {
            $zoneBlocks = $this->loadZoneBlocks($zone);
            $blocks[$zone->getIdentifier()] = $zoneBlocks;
        }

        return $blocks;
    }

    /**
     * Creates a block in specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If create struct properties have an invalid or empty value
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function createBlock(APIBlockCreateStruct $blockCreateStruct, APIZone $zone)
    {
        if (!is_string($blockCreateStruct->definitionIdentifier)) {
            throw new InvalidArgumentException(
                'blockCreateStruct->definitionIdentifier',
                $blockCreateStruct->definitionIdentifier,
                'Value must be a string.'
            );
        }

        if (empty($blockCreateStruct->definitionIdentifier)) {
            throw new InvalidArgumentException(
                'blockCreateStruct->definitionIdentifier',
                $blockCreateStruct->definitionIdentifier,
                'Value must not be empty.'
            );
        }

        if (!is_string($blockCreateStruct->viewType)) {
            throw new InvalidArgumentException(
                'blockCreateStruct->viewType',
                $blockCreateStruct->viewType,
                'Value must be a string.'
            );
        }

        if (empty($blockCreateStruct->viewType)) {
            throw new InvalidArgumentException(
                'blockCreateStruct->viewType',
                $blockCreateStruct->viewType,
                'Value must not be empty.'
            );
        }

        $createdBlock = $this->blockHandler->createBlock($blockCreateStruct, $zone->getId());

        return $this->buildDomainBlockObject($createdBlock);
    }

    /**
     * Copies a specified block. If zone is specified, copied block will be
     * placed in it, otherwise, it will be placed in the same zone where source block is.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If specified zone is in a different layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function copyBlock(APIBlock $block, APIZone $zone = null)
    {
        if ($zone instanceof APIZone) {
            $originalZone = $this->layoutService->loadZone($block->getZoneId());
            if ($zone->getLayoutId() !== $originalZone->getLayoutId()) {
                throw new InvalidArgumentException(
                    'zone->layoutId',
                    $zone->getLayoutId(),
                    'Block cannot be copied to a different layout.'
                );
            }
        }

        $copiedBlock = $this->blockHandler->copyBlock(
            $block->getId(),
            $zone instanceof APIZone ? $zone->getId() : $block->getZoneId()
        );

        return $this->buildDomainBlockObject($copiedBlock);
    }

    /**
     * Moves a block to specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If specified zone is in a different layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function moveBlock(APIBlock $block, APIZone $zone)
    {
        $originalZone = $this->layoutService->loadZone($block->getZoneId());
        if ($zone->getLayoutId() !== $originalZone->getLayoutId()) {
            throw new InvalidArgumentException(
                'zone->layoutId',
                $zone->getLayoutId(),
                'Block cannot be moved to a different layout.'
            );
        }

        $movedBlock = $this->blockHandler->moveBlock($block->getId(), $zone->getId());

        return $this->buildDomainBlockObject($movedBlock);
    }

    /**
     * Deletes a specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function deleteBlock(APIBlock $block)
    {
        $this->blockHandler->deleteBlock($block->getId());
    }

    /**
     * Creates a new block create struct.
     *
     * @param string $definitionIdentifier
     * @param string $viewType
     *
     * @return \Netgen\BlockManager\API\Values\BlockCreateStruct
     */
    public function newBlockCreateStruct($definitionIdentifier, $viewType)
    {
        return new BlockCreateStruct(
            array(
                'definitionIdentifier' => $definitionIdentifier,
                'viewType' => $viewType,
            )
        );
    }

    /**
     * Builds the API block value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $persistenceBlock
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    protected function buildDomainBlockObject(PersistenceBlock $persistenceBlock)
    {
        $block = new Block(
            array(
                'id' => $persistenceBlock->id,
                'zoneId' => $persistenceBlock->zoneId,
                'definitionIdentifier' => $persistenceBlock->definitionIdentifier,
                'parameters' => $persistenceBlock->parameters,
                'viewType' => $persistenceBlock->viewType,
            )
        );

        return $block;
    }
}
