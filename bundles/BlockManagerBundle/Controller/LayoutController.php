<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\View\ViewBuilderInterface;

class LayoutController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry
     */
    protected $layoutTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry $layoutTypeRegistry
     */
    public function __construct(
        LayoutService $layoutService,
        ViewBuilderInterface $viewBuilder,
        LayoutTypeRegistry $layoutTypeRegistry
    ) {
        $this->layoutService = $layoutService;
        $this->viewBuilder = $viewBuilder;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Creates the layout from specified layout type.
     *
     * @param string $type
     * @param string $name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create($type, $name)
    {
        $layoutType = $this->layoutTypeRegistry->getLayoutType($type);

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $type,
            $name,
            array_keys($layoutType->getZones())
        );

        $layoutCreateStruct->status = Layout::STATUS_DRAFT;

        $layout = $this->layoutService->createLayout($layoutCreateStruct);
        $layoutView = $this->viewBuilder->buildView($layout);

        return $layoutView;
    }
}
