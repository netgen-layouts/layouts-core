<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Values\Page\Block;

class BlockController extends Controller
{
    /**
     * Returns the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function view(Block $block)
    {
        $blockView = $this->buildViewObject($block, array(), 'api');

        return $this->serializeObject($blockView, self::API_VERSION);
    }

    /**
     * Creates the block from specified block type.
     *
     * @param string $identifier
     * @param int|string $zoneId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create($identifier, $zoneId)
    {
        $blockService = $this->get('netgen_block_manager.api.service.block');
        $layoutService = $this->get('netgen_block_manager.api.service.layout');
        $configuration = $this->get('netgen_block_manager.configuration');

        $defaultValues = $configuration->getBlockTypeConfig($identifier)['defaults'];
        $blockDefinition = $this->getBlockDefinition($defaultValues['definition_identifier']);

        $blockCreateStruct = $blockService->newBlockCreateStruct(
            $defaultValues['definition_identifier'],
            $defaultValues['view_type']
        );

        $blockCreateStruct->name = $defaultValues['name'];
        $blockCreateStruct->setParameters(
            $defaultValues['parameters'] + $blockDefinition->getDefaultParameterValues()
        );

        $createdBlock = $blockService->createBlock(
            $blockCreateStruct,
            $layoutService->loadZone($zoneId)
        );

        return $this->redirectToRoute(
            'netgen_block_manager_api_v1_load_block',
            array('blockId' => $createdBlock->getId())
        );
    }

    /**
     * Displays and processes block update form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Block $block)
    {
        $blockService = $this->get('netgen_block_manager.api.service.block');
        $updateStruct = $blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->createForm(
            'Netgen\BlockManager\Form\Type\UpdateBlockType',
            $updateStruct,
            array('block' => $block)
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $blockView = $this->buildViewObject(
                $block,
                array(
                    'form' => $form->createView(),
                ),
                'edit'
            );

            return $this->renderViewObject($blockView);
        }

        $blockService = $this->get('netgen_block_manager.api.service.block');
        $updatedBlock = $blockService->updateBlock($block, $form->getData());

        return $this->redirectToRoute(
            'netgen_block_manager_api_v1_load_block',
            array('blockId' => $updatedBlock->getId())
        );
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
