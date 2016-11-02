<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\View\ViewInterface;
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
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     */
    public function __construct(
        BlockService $blockService,
        CollectionService $collectionService,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Displays block edit interface.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Block $block)
    {
        $collectionReferences = $this->blockService->loadCollectionReferences($block);

        $collections = array();
        foreach ($collectionReferences as $collectionReference) {
            $collections[$collectionReference->getIdentifier()] = $collectionReference->getCollection();
        }

        return $this->render(
            'NetgenBlockManagerAdminBundle:app/block:edit.html.twig',
            array(
                'block' => $block,
                'collections' => $collections,
                'shared_collections' => $this->collectionService->loadSharedCollections(),
                'query_types' => $this->queryTypeRegistry->getQueryTypes(),
            )
        );
    }

    /**
     * Displays and processes block draft edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function editForm(Block $block, $formName, Request $request)
    {
        $blockDefinition = $block->getBlockDefinition();
        $blockDefinitionConfig = $blockDefinition->getConfig();

        $updateStruct = $this->blockService->newBlockUpdateStruct($block);

        $form = $this->createForm(
            $blockDefinitionConfig->getForm($formName)->getType(),
            $updateStruct,
            array(
                'block' => $block,
                'action' => $this->generateUrl(
                    'ngbm_app_block_form_edit',
                    array(
                        'blockId' => $block->getId(),
                        'formName' => $formName,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, array(), ViewInterface::CONTEXT_API);
        }

        if ($form->isValid()) {
            $this->blockService->updateBlock($block, $form->getData());

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_API,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
