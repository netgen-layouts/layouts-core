<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator $validator
     */
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
     *                                                          If zone with specified ID does not exist
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

        try {
            $layout = $this->layoutService->loadLayoutDraft(
                $request->request->get('layout_id')
            );
        } catch (NotFoundException $e) {
            throw new BadStateException('layout_id', 'Layout draft does not exist.', $e);
        }

        $blockCreateStruct = $this->createBlockCreateStruct($blockType);

        $createdBlock = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $layout,
            $request->request->get('zone_identifier'),
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
        $layout = $this->layoutService->loadLayoutDraft(
            $request->request->get('layout_id')
        );

        $copiedBlock = $this->blockService->copyBlockToZone(
            $block,
            $layout,
            $request->request->get('zone_identifier')
        );

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
        $layout = $this->layoutService->loadLayoutDraft(
            $request->request->get('layout_id')
        );

        $this->blockService->moveBlockToZone(
            $block,
            $layout,
            $request->request->get('zone_identifier'),
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

    /**
     * Creates a new block create struct.
     *
     * @param \Netgen\BlockManager\Block\BlockType\BlockType $blockType
     *
     * @return \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    protected function createBlockCreateStruct(BlockType $blockType)
    {
        $blockDefinition = $blockType->getDefinition();
        $blockDefinitionConfig = $blockDefinition->getConfig();

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->name = $blockType->getDefaultName();
        $blockCreateStruct->fillValues($blockDefinition, $blockType->getDefaultParameters());

        if ($blockDefinitionConfig->hasViewType($blockType->getDefaultViewType())) {
            $viewType = $blockDefinitionConfig->getViewType($blockType->getDefaultViewType());

            $blockCreateStruct->viewType = $blockType->getDefaultViewType();
            $blockCreateStruct->itemViewType = $viewType->hasItemViewType($blockType->getDefaultItemViewType()) ?
                $blockType->getDefaultItemViewType() :
                $viewType->getItemViewTypeIdentifiers()[0];
        }

        return $blockCreateStruct;
    }

    /**
     * Performs access checks on the controller.
     */
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');
    }
}
