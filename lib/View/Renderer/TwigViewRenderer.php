<?php

namespace Netgen\BlockManager\View\Renderer;

use Netgen\BlockManager\Registry\ViewTemplateProviderRegistry;
use Netgen\BlockManager\View\ViewInterface;
use Twig_Environment;

class TwigViewRenderer implements ViewRenderer
{
    /**
     * @var \Netgen\BlockManager\Registry\ViewTemplateProviderRegistry
     */
    protected $viewTemplateProviderRegistry;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Registry\ViewTemplateProviderRegistry $viewTemplateProviderRegistry
     * @param \Twig_Environment $twig
     */
    public function __construct(
        ViewTemplateProviderRegistry $viewTemplateProviderRegistry,
        Twig_Environment $twig
    ) {
        $this->viewTemplateProviderRegistry = $viewTemplateProviderRegistry;
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
        if ($view->getTemplate() === null) {
            $viewTemplateProvider = $this->viewTemplateProviderRegistry->getViewTemplateProvider($view);
            $view->setTemplate(
                $viewTemplateProvider->provideTemplate($view)
            );
        }

        return $this->twig->render(
            $view->getTemplate(),
            $view->getParameters()
        );
    }
}
