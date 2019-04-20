<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Utils\CreateStructBuilder;
use Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Utils\CreateStructValidator;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Block\Registry\BlockTypeRegistryInterface;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\Block\BlockTypeException;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateInZone extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Utils\CreateStructBuilder
     */
    private $createStructBuilder;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block\Utils\CreateStructValidator
     */
    private $createStructValidator;

    /**
     * @var \Netgen\Layouts\Block\Registry\BlockTypeRegistryInterface
     */
    private $blockTypeRegistry;

    public function __construct(
        BlockService $blockService,
        LayoutService $layoutService,
        CreateStructBuilder $createStructBuilder,
        CreateStructValidator $createStructValidator,
        BlockTypeRegistryInterface $blockTypeRegistry
    ) {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
        $this->createStructBuilder = $createStructBuilder;
        $this->createStructValidator = $createStructValidator;
        $this->blockTypeRegistry = $blockTypeRegistry;
    }

    /**
     * Creates the block in specified zone.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If block type does not exist
     */
    public function __invoke(Request $request): View
    {
        $requestData = $request->attributes->get('data');

        $this->createStructValidator->validateCreateBlock($requestData);

        try {
            $blockType = $this->blockTypeRegistry->getBlockType($requestData->get('block_type'));
        } catch (BlockTypeException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        $layout = $this->layoutService->loadLayoutDraft($requestData->get('layout_id'));

        $this->denyAccessUnlessGranted(
            'nglayouts:block:add',
            [
                'block_definition' => $blockType->getDefinition(),
                'layout' => $layout,
            ]
        );

        $blockCreateStruct = $this->createStructBuilder->buildCreateStruct($blockType);

        $createdBlock = $this->blockService->createBlockInZone(
            $blockCreateStruct,
            $layout->getZone($requestData->get('zone_identifier')),
            $requestData->get('parent_position')
        );

        return new View($createdBlock, Version::API_V1, Response::HTTP_CREATED);
    }
}
