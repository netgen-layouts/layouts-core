<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

class LayoutController extends Controller
{
    /**
     * Creates the layout from specified layout identifier.
     *
     * @param string $identifier
     * @param string $name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create($identifier, $name)
    {
        $layoutService = $this->get('netgen_block_manager.api.service.layout');
        $configuration = $this->get('netgen_block_manager.configuration');

        $layoutConfig = $configuration->getLayoutConfig($identifier);

        $layoutCreateStruct = $layoutService->newLayoutCreateStruct(
            $identifier,
            $name,
            array_keys($layoutConfig['zones'])
        );

        $layout = $layoutService->createLayout($layoutCreateStruct);

        $layoutView = $this->buildViewObject($layout);

        return $layoutView;
    }
}
