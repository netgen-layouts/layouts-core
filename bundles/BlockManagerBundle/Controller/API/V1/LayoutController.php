<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\LayoutDraft;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Values\ValueList;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class LayoutController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Repository
     */
    protected $repository;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Repository $repository
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator $validator
     */
    public function __construct(Repository $repository, LayoutService $layoutService, LayoutValidator $validator)
    {
        $this->repository = $repository;
        $this->layoutService = $layoutService;
        $this->validator = $validator;
    }

    /**
     * Loads a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function view(LayoutDraft $layout)
    {
        return new View($layout, Version::API_V1);
    }

    /**
     * Loads all layout draft blocks.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function viewLayoutBlocks(LayoutDraft $layout)
    {
        $blocks = array();
        foreach ($layout->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                $blocks[] = new View($block, Version::API_V1);
            }
        }

        return new ValueList($blocks);
    }

    /**
     * Creates the layout.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout type does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function create(Request $request)
    {
        $this->validator->validateCreateLayout($request);

        $layoutType = $this->getLayoutType($request->request->get('layout_type'));

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $layoutType->getIdentifier(),
            $request->request->get('name')
        );

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        return new View($createdLayout, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Updates the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(LayoutDraft $layout, Request $request)
    {
        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = $request->request->get('name');

        $this->layoutService->updateLayout($layout, $layoutUpdateStruct);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Creates a new layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function createDraft(Layout $layout)
    {
        $layoutDraft = null;

        try {
            $layoutDraft = $this->layoutService->loadLayoutDraft($layout->getId());
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $this->repository->beginTransaction();

        try {
            if ($layoutDraft instanceof LayoutDraft) {
                $this->layoutService->discardDraft($layoutDraft);
            }

            $createdDraft = $this->layoutService->createDraft($layout);

            $this->repository->commitTransaction();

            return new View($createdDraft, Version::API_V1, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->repository->rollbackTransaction();

            throw new BadStateException('layout', $e->getMessage());
        }
    }

    /**
     * Discards a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function discard(LayoutDraft $layout)
    {
        $this->layoutService->discardDraft($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Publishes a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function publish(LayoutDraft $layout)
    {
        $this->layoutService->publishLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
