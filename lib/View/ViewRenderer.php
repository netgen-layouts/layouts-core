<?php

namespace Netgen\BlockManager\View;

use Twig_Environment;

class ViewRenderer implements ViewRendererInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
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
        return $this->twig->render(
            $view->getTemplate(),
            $view->getParameters()
        );
    }
}
