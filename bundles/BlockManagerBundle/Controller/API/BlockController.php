<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Values\Page\Block;

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
        $serializer = $this->get('serializer');

        $normalizedBlock = $this->normalizeValueObject($block);

        $response = new JsonResponse();
        $response->setContent($serializer->encode($normalizedBlock, 'json'));

        return $response;
    }

    /**
     * Serializes the blocks from provided layout object.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function viewLayoutBlocks(Layout $layout)
    {
        $serializer = $this->get('serializer');

        $blocks = array();
        foreach ($layout->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                $blocks[] = $this->normalizeValueObject($block);
            }
        }

        $response = new JsonResponse();
        $response->setContent($serializer->encode($blocks, 'json'));

        return $response;
    }

    /**
     * Serializes the block types.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function viewBlockTypes()
    {
        $serializer = $this->get('serializer');
        $configuration = $this->get('netgen_block_manager.configuration');

        $configBlockTypeGroups = $configuration->getParameter('block_type_groups');
        $configBlockTypes = $configuration->getParameter('block_types');

        $blockTypeGroups = array();
        foreach ($configBlockTypeGroups as $identifier => $blockTypeGroup) {
            $blockTypeGroups[] = array(
                'identifier' => $identifier
            ) + $blockTypeGroup;
        }

        $blockTypes = array();
        foreach ($configBlockTypes as $identifier => $blockType) {
            $blockTypes[] = array(
                'identifier' => $identifier
            ) + $blockType;
        }

        $data = array(
            'block_type_groups' => $blockTypeGroups,
            'block_types' => $blockTypes
        );

        $response = new JsonResponse();
        $response->setContent($serializer->encode($data, 'json'));

        return $response;
    }

    /**
     * Creates the block from specified block type.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param string $zoneIdentifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create($identifier, Layout $layout, $zoneIdentifier)
    {
        $blockService = $this->get('netgen_block_manager.api.service.block');
        $configuration = $this->get('netgen_block_manager.configuration');

        $defaultValues = $configuration->getBlockTypeConfig($identifier)['defaults'];

        $blockCreateStruct = $blockService->newBlockCreateStruct(
            $defaultValues['definition_identifier'],
            $defaultValues['view_type']
        );

        $blockCreateStruct->name = $defaultValues['name'];
        $blockCreateStruct->setParameters($defaultValues['parameters']);

        $createdBlock = $blockService->createBlock(
            $blockCreateStruct,
            $layout,
            $zoneIdentifier
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
        $configuration = $this->get('netgen_block_manager.configuration');

        $blockConfig = $configuration->getBlockConfig($block->getDefinitionIdentifier());

        $updateStruct = $blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->createForm(
            $blockConfig['forms']['edit'],
            $updateStruct,
            array('block' => $block)
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $blockView = $this->buildViewObject(
                $block,
                ViewInterface::CONTEXT_API_EDIT,
                array('form' => $form->createView(), 'api_version' => self::API_VERSION)
            );

            return $blockView;
        }

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
