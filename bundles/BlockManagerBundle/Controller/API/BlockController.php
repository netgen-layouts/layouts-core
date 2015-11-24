<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\Form\Data\UpdateBlockData;
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
     * Creates the block.
     *
     * @param string $definitionIdentifier
     * @param string $viewType
     * @param int|string $zoneId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create($definitionIdentifier, $viewType, $zoneId)
    {
        $blockService = $this->get('netgen_block_manager.api.service.block');
        $layoutService = $this->get('netgen_block_manager.api.service.layout');
        $blockDefinition = $this->getBlockDefinition($definitionIdentifier);

        $blockCreateStruct = $blockService->newBlockCreateStruct($definitionIdentifier, $viewType);
        foreach ($blockDefinition->getParameters() as $parameterIdentifier => $parameter) {
            $blockCreateStruct->setParameter($parameterIdentifier, $parameter->getDefaultValue());
        }

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

        $formName = 'ngbm_update_block';
        if ($this->get('form.registry')->hasType('ngbm_update_block_' . $block->getDefinitionIdentifier())) {
            $formName = 'ngbm_update_block_' . $block->getDefinitionIdentifier();
        }

        $form = $this->createForm(
            $formName,
            new UpdateBlockData($block, $updateStruct)
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
        $updatedBlock = $blockService->updateBlock($block, $form->getData()->updateStruct);

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
