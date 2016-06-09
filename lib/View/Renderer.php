<?php

namespace Netgen\BlockManager\View;

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
     * Renders the value object.
     *
     * @param mixed $valueObject
     * @param array $parameters
     * @param string $context
     *
     * @return string
     */
    public function renderValueObject($valueObject, array $parameters = array(), $context = ViewInterface::CONTEXT_VIEW)
    {
        return $this->renderView(
            $this->viewBuilder->buildView($valueObject, $parameters, $context)
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
