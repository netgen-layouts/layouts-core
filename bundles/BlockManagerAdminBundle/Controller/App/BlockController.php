<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Page\BlockDraft;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;
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
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(BlockDraft $block)
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
                'block_definition' => $this->getBlockDefinition($block->getDefinitionIdentifier()),
                'collections' => $collections,
                'named_collections' => $this->collectionService->loadNamedCollections(),
                'query_types' => $this->queryTypeRegistry->getQueryTypes(),
            )
        );
    }

    /**
     * Displays and processes block draft edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If block does not support the specified form
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function editForm(BlockDraft $block, $formName, Request $request)
    {
        $blockDefinition = $this->getBlockDefinition($block->getDefinitionIdentifier());

        if (!$blockDefinition->getConfig()->hasForm($formName)) {
            throw new InvalidArgumentException('form', 'Block does not support specified form.');
        }

        $blockForm = $blockDefinition->getConfig()->getForm($formName);

        $updateStruct = $this->blockService->newBlockUpdateStruct($block);

        $form = $this->createForm(
            $blockForm->getType(),
            $updateStruct,
            array(
                'blockDefinition' => $blockDefinition,
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

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->buildView($form);
        }

        if ($form->isValid()) {
            $this->blockService->updateBlock($block, $form->getData());

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_DEFAULT,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
