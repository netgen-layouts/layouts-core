<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Creates the layout from specified layout type.
     *
     * @param string $type
     * @param string $name
     *
     * @return \Netgen\BlockManager\View\LayoutViewInterface
     */
    public function create($type, $name)
    {
        $layoutType = $this->getLayoutType($type);

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $layoutType->getIdentifier(),
            $name
        );

        $layoutCreateStruct->status = Layout::STATUS_DRAFT;

        $layout = $this->layoutService->createLayout($layoutCreateStruct);
        $layoutView = $this->buildView($layout);

        return $layoutView;
    }
}
