<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
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
     * Serializes the block object.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function view(Block $block)
    {
        $data = $this->handleValueObject($block);

        return $this->buildResponse($data);
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

        $zoneIdentifier = $request->request->get('zone_identifier');
        if (!isset($layout->getZones()[$zoneIdentifier])) {
            throw new BadStateException('zone_identifier', 'Zone does not exist in the layout.');
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

        $data = $this->handleValueObject($createdBlock);

        return $this->buildResponse($data);
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
        $this->validator->validateMoveBlock($request);

        $this->blockService->moveBlock(
            $block,
            $request->request->get('position'),
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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If form was not submitted
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Block $block)
    {
        $blockConfig = $this->configuration->getBlockConfig(
            $block->getDefinitionIdentifier()
        );

        $updateStruct = $this->blockService->newBlockUpdateStruct();
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

        if ($request->getMethod() === 'PATCH') {
            if (!$form->isSubmitted()) {
                throw new InvalidArgumentException('form', 'Form is not submitted.');
            }

            if (!$form->isValid()) {
                $data = $this->handleValueObjectForm($block, $form);

                return $this->buildResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $updatedBlock = $this->blockService->updateBlock(
                $block,
                $form->getData()
            );

            $data = $this->handleValueObject($updatedBlock);

            return $this->buildResponse($data);
        }

        $data = $this->handleValueObjectForm($block, $form);

        return $this->buildResponse($data);
    }

    /**
     * Processes inline block edit form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If block does not support inline editing
     *                                                                     If form was not submitted
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editInline(Request $request, Block $block)
    {
        $blockConfig = $this->configuration->getBlockConfig(
            $block->getDefinitionIdentifier()
        );

        if (!isset($blockConfig['forms']['inline'])) {
            throw new InvalidArgumentException('form', 'Block does not support inline editing.');
        }

        $updateStruct = $this->blockService->newBlockUpdateStruct();
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
            throw new InvalidArgumentException('form', 'Form is not submitted.');
        }

        if (!$form->isValid()) {
            $data = $this->handleValueObjectForm($block, $form);

            return $this->buildResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $updatedBlock = $this->blockService->updateBlock($block, $form->getData());

        $data = $this->handleValueObject($updatedBlock);

        return $this->buildResponse($data);
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
        $this->blockService->deleteBlock($block);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
