<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\BlockValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\API\Exception\BadStateException;
use Netgen\BlockManager\API\Exception\NotFoundException;
use InvalidArgumentException as BaseInvalidArgumentException;

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
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\BlockValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\BlockValidator $validator
     */
    public function __construct(
        BlockService $blockService,
        LayoutService $layoutService,
        ConfigurationInterface $configuration,
        BlockValidator $validator
    ) {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
        $this->configuration = $configuration;
        $this->validator = $validator;
    }

    /**
     * Loads a block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function view(Block $block)
    {
        return new View($block, self::API_VERSION);
    }

    /**
     * Creates the block.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If block type does not exist
     *                                                              If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function create(Request $request)
    {
        $this->validator->validateCreateBlock($request);

        try {
            $blockTypeConfig = $this->configuration->getBlockTypeConfig(
                $request->request->get('block_type')
            );
        } catch (BaseInvalidArgumentException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        try {
            $layout = $this->layoutService->loadLayout(
                $request->request->get('layout_id'),
                Layout::STATUS_DRAFT
            );
        } catch (NotFoundException $e) {
            throw new BadStateException('layout_id', 'Layout does not exist.', $e);
        }

        $defaultValues = $blockTypeConfig['defaults'];
        $blockCreateStruct = $this->blockService->newBlockCreateStruct(
            $defaultValues['definition_identifier'],
            $defaultValues['view_type']
        );

        $blockCreateStruct->name = $defaultValues['name'];
        $blockCreateStruct->setParameters($defaultValues['parameters']);

        $createdBlock = $this->blockService->createBlock(
            $blockCreateStruct,
            $layout,
            $request->request->get('zone_identifier'),
            $request->request->get('position')
        );

        return new View($createdBlock, self::API_VERSION, Response::HTTP_CREATED);
    }

    /**
     * Moves the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function move(Block $block, Request $request)
    {
        $this->blockService->moveBlock(
            $block,
            $request->request->get('position'),
            $request->request->get('zone_identifier')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Displays and processes block edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If form was not submitted
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function edit(Block $block, Request $request)
    {
        $blockDefinition = $this->getBlockDefinition($block->getDefinitionIdentifier());

        $updateStruct = $this->blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->createForm(
            $blockDefinition->getConfiguration()->getForm('edit'),
            $updateStruct,
            array(
                'blockDefinition' => $blockDefinition,
                'method' => 'PATCH',
            )
        );

        $form->handleRequest($request);

        if ($request->getMethod() === 'PATCH') {
            if (!$form->isSubmitted()) {
                throw new InvalidArgumentException('form', 'Form is not submitted.');
            }

            if (!$form->isValid()) {
                return new FormView($form, $block, self::API_VERSION, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $updatedBlock = $this->blockService->updateBlock(
                $block,
                $form->getData()
            );

            return new View($updatedBlock, self::API_VERSION);
        }

        return new FormView($form, $block, self::API_VERSION);
    }

    /**
     * Processes inline block edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If block does not support inline editing
     *                                                                     If form was not submitted
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If unknown error occurred
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function editInline(Block $block, Request $request)
    {
        $blockDefinition = $this->getBlockDefinition($block->getDefinitionIdentifier());

        if (!$blockDefinition->getConfiguration()->hasForm('inline_edit')) {
            throw new InvalidArgumentException('form', 'Block does not support inline editing.');
        }

        $updateStruct = $this->blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->createForm(
            $blockDefinition->getConfiguration()->getForm('inline_edit'),
            $updateStruct,
            array(
                'blockDefinition' => $blockDefinition,
                'method' => 'PATCH',
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            throw new InvalidArgumentException('form', 'Form is not submitted.');
        }

        if (!$form->isValid()) {
            $formErrors = $form->getErrors(true);
            if (!empty($formErrors)) {
                throw new BadStateException(
                    $formErrors[0]->getOrigin()->getName(),
                    $formErrors[0]->getMessage()
                );
            }

            throw new BadStateException('unknown', 'Unknown error');
        }

        $updatedBlock = $this->blockService->updateBlock($block, $form->getData());

        return new View($updatedBlock, self::API_VERSION);
    }

    /**
     * Deletes the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Block $block)
    {
        $this->blockService->deleteBlock($block);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
