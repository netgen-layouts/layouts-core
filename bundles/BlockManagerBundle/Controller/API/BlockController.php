<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Symfony\Component\HttpFoundation\Request;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Form\FormData;

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
        $blockView = $this->buildViewObject($block, array(), 'api');

        return $this->serializeObject($blockView);
    }

    public function edit(Request $request, Block $block)
    {
        $definitionIdentifier = $block->getDefinitionIdentifier();
        $blockDefinition = $this->getBlockDefinition($definitionIdentifier);

        $blockConfig = $this
            ->get('netgen_block_manager.configuration')
            ->getBlockConfig($definitionIdentifier);

        $form = $this->createForm(
            'ngbm_block',
            new FormData($blockDefinition, $block)
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $blockView = $this->buildViewObject(
                $block,
                array(
                    'form' => $form->createView(),
                    'config' => $blockConfig,
                ),
                'edit'
            );

            return $this->renderViewObject($blockView);
        }

        $blockService = $this->get('netgen_block_manager.api.service.block');
        $updatedBlock = $blockService->updateBlock($block, $form->getData()->payload);

        return $this->serializeObject($this->buildViewObject($updatedBlock));
    }
}
