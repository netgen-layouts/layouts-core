<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Configuration\ConfigurationInterface;

class LayoutController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(LayoutService $layoutService, ConfigurationInterface $configuration)
    {
        $this->layoutService = $layoutService;
        $this->configuration = $configuration;
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
        $layoutConfig = $this->configuration->getLayoutConfig($type);

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $type,
            $name,
            array_keys($layoutConfig['zones'])
        );

        $layoutCreateStruct->status = Layout::STATUS_DRAFT;

        $layout = $this->layoutService->createLayout($layoutCreateStruct);
        $layoutView = $this->buildViewObject($layout);

        return $layoutView;
    }
}
