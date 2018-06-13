<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

/**
 * @final
 */
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

    public function renderValue($value, $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = [])
    {
        return $this->viewRenderer->renderView(
            $this->viewBuilder->buildView($value, $context, $parameters)
        );
    }
}
