<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;

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
     * @param int|string $blockId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function view($blockId, Request $request)
    {
        $block = $request->query->get('published') === 'true' ?
            $this->blockService->loadBlock($blockId) :
            $this->blockService->loadBlockDraft($blockId);

        return new View($block, Version::API_V1);
    }

    /**
     * Creates the block.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block type does not exist
     *                                                          If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function create(Request $request)
    {
        $this->validator->validateCreateBlock($request);

        try {
            $blockType = $this->getBlockType($request->request->get('block_type'));
        } catch (InvalidArgumentException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        try {
            $layout = $this->layoutService->loadLayoutDraft($request->request->get('layout_id'));
        } catch (NotFoundException $e) {
            throw new BadStateException('layout_id', 'Layout draft does not exist.', $e);
        }

        $createdBlock = $this->blockService->createBlock(
            $this->blockService->newBlockCreateStruct($blockType),
            $layout,
            $request->request->get('zone_identifier'),
            $request->request->get('position')
        );

        return new View($createdBlock, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Updates the block.
     *
     * @param int|string $blockId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If some of the parameters do not exist in the block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function update($blockId, Request $request)
    {
        $block = $this->blockService->loadBlockDraft($blockId);

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct();
        $blockUpdateStruct->name = $request->request->get('name');
        $blockUpdateStruct->viewType = $request->request->get('view_type');
        $blockUpdateStruct->itemViewType = $request->request->get('item_view_type');

        $parameters = $request->request->get('parameters');
        if (is_array($request->request->get('parameters'))) {
            foreach ($parameters as $parameterName => $parameterValue) {
                if (!$block->hasParameter($parameterName)) {
                    throw new BadStateException(
                        'parameters[' . $parameterName . ']',
                        'Parameter does not exist in block.'
                    );
                }

                $parameterType = $block->getParameter($parameterName)->getParameterType();
                $blockUpdateStruct->setParameter(
                    $parameterName,
                    $parameterType->toValue($parameterValue)
                );
            }
        }

        $updatedBlock = $this->blockService->updateBlock($block, $blockUpdateStruct);

        return new View($updatedBlock, Version::API_V1);
    }

    /**
     * Copies the block draft.
     *
     * @param int|string $blockId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function copy($blockId, Request $request)
    {
        $copiedBlock = $this->blockService->copyBlock(
            $this->blockService->loadBlockDraft($blockId),
            $request->request->get('zone_identifier')
        );

        return new View($copiedBlock, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Moves the block draft.
     *
     * @param int|string $blockId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function move($blockId, Request $request)
    {
        $this->blockService->moveBlock(
            $this->blockService->loadBlockDraft($blockId),
            $request->request->get('position'),
            $request->request->get('zone_identifier')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Restores the block draft to the published state.
     *
     * @param int|string $blockId
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function restore($blockId)
    {
        $restoredBlock = $this->blockService->restoreBlock(
            $this->blockService->loadBlockDraft($blockId)
        );

        return new View($restoredBlock, Version::API_V1);
    }

    /**
     * Deletes the block draft.
     *
     * @param int|string $blockId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($blockId)
    {
        $this->blockService->deleteBlock(
            $this->blockService->loadBlockDraft($blockId)
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
