<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Value;
use Twig_Environment;

class Renderer implements RendererInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Twig_Environment $twig
     */
    public function __construct(ViewBuilderInterface $viewBuilder, Twig_Environment $twig)
    {
        $this->viewBuilder = $viewBuilder;
        $this->twig = $twig;
    }

    /**
     * Renders the value.
     *
     * @param mixed $value
     * @param string $context
     * @param array $parameters
     *
     * @return string
     */
    public function renderValue($value, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array())
    {
        return $this->renderView(
            $this->viewBuilder->buildView($value, $context, $parameters)
        );
    }

    /**
     * Renders the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return string
     */
    public function renderView(ViewInterface $view)
    {
        return $this->twig->render($view->getTemplate(), $view->getParameters());
    }
}
