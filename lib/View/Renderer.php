<?php

namespace Netgen\BlockManager\View;

class Renderer implements RendererInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    private $viewBuilder;

    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    private $viewRenderer;

    public function __construct(
        ViewBuilderInterface $viewBuilder,
        ViewRendererInterface $viewRenderer
    ) {
        $this->viewBuilder = $viewBuilder;
        $this->viewRenderer = $viewRenderer;
    }

    public function renderValueObject($valueObject, $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = array())
    {
        return $this->viewRenderer->renderView(
            $this->viewBuilder->buildView($valueObject, $context, $parameters)
        );
    }
}
