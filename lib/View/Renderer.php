<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

/**
 * @final
 */
class Renderer implements RendererInterface
{
    private ViewBuilderInterface $viewBuilder;

    private ViewRendererInterface $viewRenderer;

    public function __construct(
        ViewBuilderInterface $viewBuilder,
        ViewRendererInterface $viewRenderer
    ) {
        $this->viewBuilder = $viewBuilder;
        $this->viewRenderer = $viewRenderer;
    }

    public function renderValue($value, string $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = []): string
    {
        return $this->viewRenderer->renderView(
            $this->viewBuilder->buildView($value, $context, $parameters),
        );
    }
}
