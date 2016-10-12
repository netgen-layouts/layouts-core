<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\LayoutDraft;
use Netgen\BlockManager\API\Values\Page\ZoneDraft;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
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
     * Loads all shared layouts.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function loadSharedLayouts()
    {
        $layouts = array();
        foreach ($this->layoutService->loadSharedLayouts() as $layout) {
            $layouts[] = new VersionedValue($layout, Version::API_V1);
        }

        return new ValueList($layouts);
    }

    /**
     * Loads either the draft status or published status of specified layout.
     *
     * If a query param "published" with value of "true" is provided, published
     * state will be loaded directly, without first loading the draft.
     *
     * @param int $layoutId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function load($layoutId, Request $request)
    {
        $layout = $this->loadLayout(
            $layoutId,
            $request->query->get('published') !== 'true'
        );

        return new View($layout, Version::API_V1);
    }

    /**
     * Loads all layout blocks.
     *
     * @param int $layoutId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function viewLayoutBlocks($layoutId, Request $request)
    {
        $layout = $this->loadLayout(
            $layoutId,
            $request->query->get('published') !== 'true'
        );

        $blocks = array();
        foreach ($layout as $zone) {
            foreach ($zone as $block) {
                $blocks[] = new View($block, Version::API_V1);
            }
        }

        return new ValueList($blocks);
    }

    /**
     * Loads all zone blocks.
     *
     * @param int $layoutId
     * @param string $zoneIdentifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueList
     */
    public function viewZoneBlocks($layoutId, $zoneIdentifier, Request $request)
    {
        $layout = $this->loadLayout(
            $layoutId,
            $request->query->get('published') !== 'true'
        );

        if (!$layout->hasZone($zoneIdentifier)) {
            throw new NotFoundException('zone', $zoneIdentifier);
        }

        $blocks = array();
        foreach ($layout->getZone($zoneIdentifier, false) as $block) {
            $blocks[] = new View($block, Version::API_V1);
        }

        return new ValueList($blocks);
    }

    /**
     * Links the provided zone to zone from shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\ZoneDraft $zone
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked layout or zone do not exist
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function linkZone(ZoneDraft $zone, Request $request)
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
     * @param \Netgen\BlockManager\API\Values\Page\ZoneDraft $zone
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unlinkZone(ZoneDraft $zone)
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
    public function discardDraft(LayoutDraft $layout)
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
    public function publishDraft(LayoutDraft $layout)
    {
        $this->layoutService->publishLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads either published or draft state of the layout.
     *
     * @param int|string $layoutId
     * @param bool $loadDraft
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout|\Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    protected function loadLayout($layoutId, $loadDraft = true)
    {
        if ($loadDraft) {
            return $this->layoutService->loadLayoutDraft(
                $layoutId
            );
        }

        return $this->layoutService->loadLayout(
            $layoutId
        );
    }
}
