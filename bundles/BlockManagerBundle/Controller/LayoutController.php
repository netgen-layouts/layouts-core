<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutController extends Controller
{
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
        $layoutService = $this->get('netgen_block_manager.api.service.layout');
        $configuration = $this->get('netgen_block_manager.configuration');

        $layoutConfig = $configuration->getLayoutConfig($type);

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(
            $type,
            $name,
            array_keys($layoutConfig['zones'])
        );

        $layoutCreateStruct->status = Layout::STATUS_DRAFT;

        $layout = $layoutService->createLayout($layoutCreateStruct);
        $layoutView = $this->buildViewObject($layout);

        return $layoutView;
    }
}
