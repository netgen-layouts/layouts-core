<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

/**
 * @final
 */
class Renderer implements RendererInterface
{
    public function __construct(
        private ViewBuilderInterface $viewBuilder,
        private ViewRendererInterface $viewRenderer,
    ) {}

    public function renderValue(mixed $value, string $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = []): string
    {
        return $this->viewRenderer->renderView(
            $this->viewBuilder->buildView($value, $context, $parameters),
        );
    }
}
