<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Values\Value;
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
     * @param int $layoutId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function load($layoutId, Request $request)
    {
        $layout = $request->query->get('published') === 'true' ?
            $this->layoutService->loadLayout($layoutId) :
            $this->layoutService->loadLayoutDraft($layoutId);

        return new View($layout, Version::API_V1);
    }

    /**
     * Loads all layout blocks.
     *
     * @param int $layoutId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function viewLayoutBlocks($layoutId, Request $request)
    {
        $layout = $request->query->get('published') === 'true' ?
            $this->layoutService->loadLayout($layoutId) :
            $this->layoutService->loadLayoutDraft($layoutId);

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
     * @param int $layoutId
     * @param string $zoneIdentifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function viewZoneBlocks($layoutId, $zoneIdentifier, Request $request)
    {
        $zone = $request->query->get('published') === 'true' ?
            $this->layoutService->loadZone($layoutId, $zoneIdentifier) :
            $this->layoutService->loadZoneDraft($layoutId, $zoneIdentifier);

        $blocks = array();
        foreach ($zone as $block) {
            $blocks[] = new View($block, Version::API_V1);
        }

        return new Value($blocks);
    }

    /**
     * Links the provided zone to zone from shared layout.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked layout or zone do not exist
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function linkZone($layoutId, $zoneIdentifier, Request $request)
    {
        $zone = $this->layoutService->loadZoneDraft($layoutId, $zoneIdentifier);

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
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unlinkZone($layoutId, $zoneIdentifier)
    {
        $this->layoutService->unlinkZone(
            $this->layoutService->loadZoneDraft($layoutId, $zoneIdentifier)
        );

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
     * @param int|string $layoutId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($layoutId, Request $request)
    {
        $layoutUpdateStruct = $this->layoutService->newLayoutUpdateStruct();
        $layoutUpdateStruct->name = $request->request->get('name');

        $this->layoutService->updateLayout(
            $this->layoutService->loadLayoutDraft($layoutId),
            $layoutUpdateStruct
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Copies the layout.
     *
     * @param int|string $layoutId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function copy($layoutId, Request $request)
    {
        $copiedLayout = $this->layoutService->copyLayout(
            $this->layoutService->loadLayout($layoutId),
            $request->request->get('name')
        );

        return new View($copiedLayout, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Creates a new layout draft.
     *
     * @param int|string $layoutId
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function createDraft($layoutId)
    {
        $layoutDraft = null;
        $layout = $this->layoutService->loadLayout($layoutId);

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
     * Copies the layout draft.
     *
     * @param int|string $layoutId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function copyDraft($layoutId, Request $request)
    {
        $copiedLayout = $this->layoutService->copyLayout(
            $this->layoutService->loadLayoutDraft($layoutId),
            $request->request->get('name')
        );

        return new View($copiedLayout, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Discards a layout draft.
     *
     * @param int|string $layoutId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function discardDraft($layoutId)
    {
        $this->layoutService->discardDraft(
            $this->layoutService->loadLayoutDraft($layoutId)
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Publishes a layout draft.
     *
     * @param int|string $layoutId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function publishDraft($layoutId)
    {
        $this->layoutService->publishLayout(
            $this->layoutService->loadLayoutDraft($layoutId)
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes a layout.
     *
     * @param int|string $layoutId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($layoutId)
    {
        $this->layoutService->deleteLayout(
            $this->layoutService->loadLayout($layoutId)
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
