<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator
     */
    private $validator;

    public function __construct(
        BlockService $blockService,
        LayoutService $layoutService,
        BlockValidator $validator
    ) {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
        $this->validator = $validator;
    }

    /**
     * Loads a block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function view(Block $block)
    {
        return new View($block, Version::API_V1);
    }

    /**
     * Creates the block in specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block type does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function create(Block $block, Request $request)
    {
        $this->validator->validateCreateBlock($request);

        try {
            $blockType = $this->getBlockType($request->request->get('block_type'));
        } catch (BlockTypeException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        $blockCreateStruct = $this->createBlockCreateStruct($blockType);

        $createdBlock = $this->blockService->createBlock(
            $blockCreateStruct,
            $block,
            $request->request->get('placeholder'),
            $request->request->get('position')
        );

        return new View($createdBlock, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Creates the block in specified zone.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block type does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function createInZone(Request $request)
    {
        $this->validator->validateCreateBlock($request);

        try {
            $blockType = $this->getBlockType($request->request->get('block_type'));
        } catch (BlockTypeException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        $zone = $this->layoutService->loadZoneDraft(
            $request->request->get('layout_id'),
            $request->request->get('zone_identifier')
        );

        $blockCreateStruct = $this->createBlockCreateStruct($blockType);

        $createdBlock = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $zone,
            $request->request->get('position')
        );

        return new View($createdBlock, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Copies the block draft to specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function copy(Block $block, Request $request)
    {
        $targetBlock = $this->blockService->loadBlockDraft(
            $request->request->get('block_id')
        );

        $copiedBlock = $this->blockService->copyBlock(
            $block,
            $targetBlock,
            $request->request->get('placeholder')
        );

        return new View($copiedBlock, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Copies the block draft to specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function copyToZone(Block $block, Request $request)
    {
        $zone = $this->layoutService->loadZoneDraft(
            $request->request->get('layout_id'),
            $request->request->get('zone_identifier')
        );

        $copiedBlock = $this->blockService->copyBlockToZone($block, $zone);

        return new View($copiedBlock, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Moves the block draft to specified block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function move(Block $block, Request $request)
    {
        $targetBlock = $this->blockService->loadBlockDraft(
            $request->request->get('block_id')
        );

        $this->blockService->moveBlock(
            $block,
            $targetBlock,
            $request->request->get('placeholder'),
            $request->request->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Moves the block draft to specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveToZone(Block $block, Request $request)
    {
        $zone = $this->layoutService->loadZoneDraft(
            $request->request->get('layout_id'),
            $request->request->get('zone_identifier')
        );

        $this->blockService->moveBlockToZone(
            $block,
            $zone,
            $request->request->get('position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Restores the block draft to the published state.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function restore(Block $block)
    {
        $restoredBlock = $this->blockService->restoreBlock($block);

        return new View($restoredBlock, Version::API_V1);
    }

    /**
     * Deletes the block draft.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Block $block)
    {
        $this->blockService->deleteBlock($block);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');
    }

    /**
     * Creates a new block create struct.
     *
     * @param \Netgen\BlockManager\Block\BlockType\BlockType $blockType
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    private function createBlockCreateStruct(BlockType $blockType)
    {
        $blockDefinition = $blockType->getDefinition();
        $blockDefinitionConfig = $blockDefinition->getConfig();

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->name = $blockType->getDefaultName();
        $blockCreateStruct->fillParametersFromHash($blockDefinition, $blockType->getDefaultParameters());

        if ($blockDefinitionConfig->hasViewType($blockType->getDefaultViewType())) {
            $viewType = $blockDefinitionConfig->getViewType($blockType->getDefaultViewType());

            $blockCreateStruct->viewType = $blockType->getDefaultViewType();
            $blockCreateStruct->itemViewType = $viewType->hasItemViewType($blockType->getDefaultItemViewType()) ?
                $blockType->getDefaultItemViewType() :
                $viewType->getItemViewTypeIdentifiers()[0];
        }

        $blockConfig = $blockDefinition->getConfig();
        foreach ($blockConfig->getCollections() as $collectionConfig) {
            $blockCreateStruct->addCollectionCreateStruct(
                $collectionConfig->getIdentifier(),
                new CollectionCreateStruct()
            );
        }

        return $blockCreateStruct;
    }
}
