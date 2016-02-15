<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Exception\BadStateException;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use InvalidArgumentException as BaseInvalidArgumentException;

class BlockController extends Controller
{
    /**
     * Serializes the block object.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function view(Block $block)
    {
        $response = new JsonResponse();
        $response->setContent(
            $this->handleValueObject($block)
        );

        return $response;
    }

    /**
     * Creates the block.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If some of the required request parameters are empty, missing or have an invalid format
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If block type does not exist
     *                                                              If provided position is out of range
     *                                                              If layout with specified ID does not exist or layout does not have a specified zone
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $blockType = $request->request->get('block_type');
        if (!is_string($blockType) || empty($blockType)) {
            throw new InvalidArgumentException('block_type', 'The value needs to be a non empty string.');
        }

        $position = $request->request->get('position');
        if ($position !== null && !ctype_digit($position)) {
            throw new InvalidArgumentException('position', 'The value needs to be a non negative integer.');
        }

        $layoutId = $request->request->get('layout_id');
        if (!ctype_digit($layoutId)) {
            throw new InvalidArgumentException('layout_id', 'The value needs to be a non negative integer.');
        }

        if (!$request->request->has('zone_identifier')) {
            throw new InvalidArgumentException('zone_identifier', 'The value is missing.');
        }

        $blockService = $this->get('netgen_block_manager.api.service.block');
        $layoutService = $this->get('netgen_block_manager.api.service.layout');
        $configuration = $this->get('netgen_block_manager.configuration');

        try {
            $blockTypeConfig = $configuration->getBlockTypeConfig($blockType);
        } catch (BaseInvalidArgumentException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        try {
            $layout = $layoutService->loadLayout($layoutId, Layout::STATUS_DRAFT);
        } catch (NotFoundException $e) {
            throw new BadStateException('layout_id', 'Layout does not exist.', $e);
        }

        $defaultValues = $blockTypeConfig['defaults'];
        $blockCreateStruct = $blockService->newBlockCreateStruct(
            $defaultValues['definition_identifier'],
            $defaultValues['view_type']
        );

        $blockCreateStruct->name = $defaultValues['name'];
        $blockCreateStruct->setParameters($defaultValues['parameters']);

        $createdBlock = $blockService->createBlock(
            $blockCreateStruct,
            $layout,
            $request->request->get('zone_identifier'),
            $position !== null ? (int)$position : null
        );

        $response = new JsonResponse();
        $response->setContent(
            $this->handleValueObject($createdBlock)
        );

        return $response;
    }

    /**
     * Moves the block.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If some of the required request parameters are empty, missing or have an invalid format
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout the block is in does not have the specified zone
     *                                                              If provided position is out of range
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function move(Request $request, Block $block)
    {
        $position = $request->request->get('position');
        if (!ctype_digit($position)) {
            throw new InvalidArgumentException('position', 'The value needs to be a non negative integer.');
        }

        $blockService = $this->get('netgen_block_manager.api.service.block');

        $blockService->moveBlock(
            $block,
            (int)$position,
            $request->request->get('zone_identifier')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Displays and processes full block edit form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Block $block)
    {
        $blockService = $this->get('netgen_block_manager.api.service.block');
        $configuration = $this->get('netgen_block_manager.configuration');

        $blockConfig = $configuration->getBlockConfig($block->getDefinitionIdentifier());

        $updateStruct = $blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->createForm(
            $blockConfig['forms']['full'],
            $updateStruct,
            array(
                'block' => $block,
                'method' => 'PATCH',
            )
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $response = new JsonResponse(
                null,
                !$form->isSubmitted() ?
                    Response::HTTP_OK :
                    Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $response->setContent(
                $this->handleValueObjectForm($block, $form)
            );

            return $response;
        }

        $updatedBlock = $blockService->updateBlock($block, $form->getData());

        $response = new JsonResponse();
        $response->setContent(
            $this->handleValueObject($updatedBlock)
        );

        return $response;
    }

    /**
     * Processes inline block edit form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If block does not support inline editing
     *                                                              If request parameters required by the form are missing
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editInline(Request $request, Block $block)
    {
        $blockService = $this->get('netgen_block_manager.api.service.block');
        $configuration = $this->get('netgen_block_manager.configuration');

        $blockConfig = $configuration->getBlockConfig($block->getDefinitionIdentifier());

        if (!isset($blockConfig['forms']['inline'])) {
            throw new BadStateException('form', 'Block does not support inline editing.');
        }

        $updateStruct = $blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->createForm(
            $blockConfig['forms']['inline'],
            $updateStruct,
            array(
                'block' => $block,
                'method' => 'PATCH',
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            throw new BadStateException('form', 'Form data is missing.');
        }

        if (!$form->isValid()) {
            $response = new JsonResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY);
            $response->setContent(42);

            return $response;
        }

        $updatedBlock = $blockService->updateBlock($block, $form->getData());

        $response = new JsonResponse();
        $response->setContent(
            $this->handleValueObject($updatedBlock)
        );

        return $response;
    }

    /**
     * Deletes the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete(Block $block)
    {
        $blockService = $this->get('netgen_block_manager.api.service.block');
        $blockService->deleteBlock($block);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
