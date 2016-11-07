<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator;
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
     * Loads all shared layouts.
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function loadSharedLayouts()
    {
        $layouts = array();
        foreach ($this->layoutService->loadSharedLayouts() as $layout) {
            $layouts[] = new VersionedValue($layout, Version::API_V1);
        }

        return new Value($layouts);
    }

    /**
     * Loads either the draft status or published status of specified layout.
     *
     * If a query param "published" with value of "true" is provided, published
     * state will be loaded directly, without first loading the draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function load(Layout $layout)
    {
        return new View($layout, Version::API_V1);
    }

    /**
     * Loads all layout blocks.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function viewLayoutBlocks(Layout $layout)
    {
        $blocks = array();
        foreach ($layout as $zone) {
            foreach ($zone as $block) {
                $blocks[] = new View($block, Version::API_V1);
            }
        }

        return new Value($blocks);
    }

    /**
     * Loads all zone blocks.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function viewZoneBlocks(Zone $zone)
    {
        $blocks = array();
        foreach ($zone as $block) {
            $blocks[] = new View($block, Version::API_V1);
        }

        return new Value($blocks);
    }

    /**
     * Links the provided zone to zone from shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked layout or zone do not exist
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function linkZone(Zone $zone, Request $request)
    {
        try {
            $linkedZone = $this->layoutService->loadZone(
                $request->request->get('linked_layout_id'),
                $request->request->get('linked_zone_identifier')
            );
        } catch (NotFoundException $e) {
            throw new BadStateException(
                'linked_zone_identifier',
                'Specified linked layout or zone do not exist'
            );
        }

        $this->layoutService->linkZone($zone, $linkedZone);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes the zone link, if any exists.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unlinkZone(Zone $zone)
    {
        $this->layoutService->unlinkZone($zone);

        return new Response(null, Response::HTTP_NO_CONTENT);
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

        try {
            $layoutType = $this->getLayoutType($request->request->get('layout_type'));
        } catch (InvalidArgumentException $e) {
            throw new BadStateException('layout_type', 'Layout type does not exist.', $e);
        }

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
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Layout $layout, Request $request)
    {
        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = $request->request->get('name');

        $this->layoutService->updateLayout($layout, $layoutUpdateStruct);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Copies the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function copy(Layout $layout, Request $request)
    {
        $copiedLayout = $this->layoutService->copyLayout(
            $layout,
            $request->request->get('name')
        );

        return new View($copiedLayout, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Creates a new layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
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
            if ($layoutDraft instanceof Layout) {
                $this->layoutService->discardDraft($layoutDraft);
            }

            $createdDraft = $this->layoutService->createDraft($layout);

            $this->repository->commitTransaction();

            return new View($createdDraft, Version::API_V1, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->repository->rollbackTransaction();

            throw $e;
        }
    }

    /**
     * Discards a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function discardDraft(Layout $layout)
    {
        $this->layoutService->discardDraft($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Publishes a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function publishDraft(Layout $layout)
    {
        $this->layoutService->publishLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Layout $layout)
    {
        $this->layoutService->deleteLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
