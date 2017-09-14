<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator
     */
    protected $validator;

    public function __construct(LayoutService $layoutService, BlockService $blockService, LayoutValidator $validator)
    {
        $this->layoutService = $layoutService;
        $this->blockService = $blockService;
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
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
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
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout does not exist in provided locale
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function viewLayoutBlocks(Layout $layout, $locale)
    {
        if (!$layout->hasLocale($locale)) {
            throw new NotFoundException('layout', $layout->getId());
        }

        $blocks = array();
        foreach ($layout as $zone) {
            foreach ($this->blockService->loadZoneBlocks($zone, array($locale)) as $block) {
                $blocks[] = new View($block, Version::API_V1);
            }
        }

        return new Value($blocks);
    }

    /**
     * Loads all zone blocks.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout does not exist in provided locale
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function viewZoneBlocks(Zone $zone, $locale)
    {
        $layout = $zone->isPublished() ?
            $this->layoutService->loadLayout($zone->getLayoutId()) :
            $this->layoutService->loadLayoutDraft($zone->getLayoutId());

        if (!$layout->hasLocale($locale)) {
            throw new NotFoundException('layout', $layout->getId());
        }

        $blocks = array();
        foreach ($this->blockService->loadZoneBlocks($zone, array($locale)) as $block) {
            $blocks[] = new View($block, Version::API_V1);
        }

        return new Value($blocks);
    }

    /**
     * Links the provided zone to zone from shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function linkZone(Zone $zone, Request $request)
    {
        $linkedZone = $this->layoutService->loadZone(
            $request->request->get('linked_layout_id'),
            $request->request->get('linked_zone_identifier')
        );

        $this->layoutService->linkZone($zone, $linkedZone);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes the zone link, if any exists.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
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
        } catch (LayoutTypeException $e) {
            throw new BadStateException('layout_type', 'Layout type does not exist.', $e);
        }

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $layoutType,
            $request->request->get('name'),
            $request->request->get('locale')
        );

        $layoutCreateStruct->description = $request->request->get('description');

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        return new View($createdLayout, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Copies the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function copy(Layout $layout, Request $request)
    {
        $copyStruct = $this->layoutService->newLayoutCopyStruct();
        $copyStruct->name = $request->request->get('name');
        $copyStruct->description = $request->request->get('description');

        $copiedLayout = $this->layoutService->copyLayout($layout, $copyStruct);

        return new View($copiedLayout, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Changes the type of the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function changeType(Layout $layout, Request $request)
    {
        $layoutType = $this->getLayoutType($request->request->get('new_type'));
        $zoneMappings = $request->request->get('zone_mappings');

        $updatedLayout = $this->layoutService->changeLayoutType(
            $layout,
            $layoutType,
            is_array($zoneMappings) ? $zoneMappings : array()
        );

        return new View($updatedLayout, Version::API_V1);
    }

    /**
     * Creates a new layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function createDraft(Layout $layout)
    {
        $createdDraft = $this->layoutService->createDraft($layout, true);

        return new View($createdDraft, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Discards a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
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
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
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
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Layout $layout)
    {
        $this->layoutService->deleteLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');
    }
}
