<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Values\EditView;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Values\Page\BlockDraft;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use RuntimeException;

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
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator $validator
     */
    public function __construct(
        BlockService $blockService,
        LayoutService $layoutService,
        CollectionService $collectionService,
        BlockValidator $validator
    ) {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
        $this->collectionService = $collectionService;
        $this->validator = $validator;
    }

    /**
     * Loads a block draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function view(BlockDraft $block)
    {
        return new View($block, Version::API_V1);
    }

    /**
     * Loads all block draft collections.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollections(BlockDraft $block)
    {
        $collections = array_map(
            function (CollectionReference $collection) {
                return new VersionedValue($collection, Version::API_V1);
            },
            $this->blockService->loadCollectionReferences($block)
        );

        return new ValueArray($collections);
    }

    /**
     * Creates the block.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block type or block definition does not exist
     *                                                          If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function create(Request $request)
    {
        $this->validator->validateCreateBlock($request);

        try {
            $blockType = $this->getBlockType($request->request->get('block_type'));
        } catch (RuntimeException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        try {
            $blockDefinition = $this->getBlockDefinition($blockType->getDefinitionIdentifier());
        } catch (RuntimeException $e) {
            throw new BadStateException('block_type', 'Block definition specified in block type does not exist.', $e);
        }

        try {
            $layout = $this->layoutService->loadLayoutDraft($request->request->get('layout_id'));
        } catch (NotFoundException $e) {
            throw new BadStateException('layout_id', 'Layout draft does not exist.', $e);
        }

        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $blockType->getDefinitionIdentifier(),
            $blockType->getDefaultBlockViewType()
        );

        $blockParameters = array();
        foreach ($blockDefinition->getParameters() as $parameterName => $parameter) {
            $blockParameters[$parameterName] = null;
        }

        $blockCreateStruct->name = $blockType->getDefaultBlockName();
        $blockCreateStruct->setParameters($blockType->getDefaultBlockParameters() + $blockParameters);

        $createdBlock = $this->blockService->createBlock(
            $blockCreateStruct,
            $layout,
            $request->request->get('zone_identifier'),
            $request->request->get('position')
        );

        return new View($createdBlock, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Displays block draft edit interface.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\EditView
     */
    public function edit(BlockDraft $block)
    {
        $defaultCollection = null;
        $collectionReferences = $this->blockService->loadCollectionReferences($block);
        foreach ($collectionReferences as $collectionReference) {
            if ($collectionReference->getIdentifier() === 'default') {
                if ($collectionReference->getCollectionStatus() === Collection::STATUS_PUBLISHED) {
                    $defaultCollection = $this->collectionService->loadCollection(
                        $collectionReference->getCollectionId()
                    );
                } else {
                    $defaultCollection = $this->collectionService->loadCollectionDraft(
                        $collectionReference->getCollectionId()
                    );
                }
            }
        }

        $editView = new EditView($block, Version::API_V1);
        $editView->setViewParameters(
            array(
                'block_definition' => $this->getBlockDefinition($block->getDefinitionIdentifier()),
                'collection' => $defaultCollection,
                'named_collections' => $this->collectionService->loadNamedCollections(),
            )
        );

        return $editView;
    }

    /**
     * Moves the block draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function move(BlockDraft $block, Request $request)
    {
        $this->blockService->moveBlock(
            $block,
            $request->request->get('position'),
            $request->request->get('zone_identifier')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
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
    public function form(BlockDraft $block, $formName, Request $request)
    {
        $blockDefinition = $this->getBlockDefinition($block->getDefinitionIdentifier());

        if (!$blockDefinition->getConfig()->hasForm($formName)) {
            throw new InvalidArgumentException('form', 'Block does not support specified form.');
        }

        $updateStruct = $this->blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->createForm(
            $blockDefinition->getConfig()->getForm($formName)->getType(),
            $updateStruct,
            array(
                'blockDefinition' => $blockDefinition,
                'action' => $this->generateUrl(
                    'netgen_block_manager_api_v1_block_form',
                    array(
                        'blockId' => $block->getId(),
                        'formName' => $formName,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        $responseCode = Response::HTTP_OK;
        if ($request->getMethod() === Request::METHOD_POST) {
            if ($form->isValid()) {
                $updatedBlock = $this->blockService->updateBlock($block, $form->getData());

                return new View($updatedBlock, Version::API_V1);
            } else {
                $responseCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            }
        }

        return new FormView($form, $block, Version::API_V1, $responseCode);
    }

    /**
     * Restores the block draft to the published state.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function restore(BlockDraft $block)
    {
        $restoredBlock = $this->blockService->restoreBlock($block);

        return new View($restoredBlock, Version::API_V1);
    }

    /**
     * Deletes the block draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(BlockDraft $block)
    {
        $this->blockService->deleteBlock($block);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
