<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Form\Data\UpdateBlockData;

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

        return $this->serializeObject($blockView);
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

        $form = $this->createForm(
            'ngbm_update_block',
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
}
