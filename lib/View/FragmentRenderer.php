<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\View\Fragment\ViewRendererInterface as FragmentViewRendererInterface;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class FragmentRenderer implements RendererInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    protected $viewRenderer;

    /**
     * @var \Symfony\Component\HttpKernel\Fragment\FragmentHandler
     */
    protected $fragmentHandler;

    /**
     * @var \Netgen\BlockManager\View\Fragment\ViewRendererInterface[]
     */
    protected $fragmentViewRenderers;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\BlockManager\View\ViewRendererInterface $viewRenderer
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $fragmentHandler
     * @param \Netgen\BlockManager\View\Fragment\ViewRendererInterface[] $fragmentViewRenderers
     */
    public function __construct(
        ViewBuilderInterface $viewBuilder,
        ViewRendererInterface $viewRenderer,
        FragmentHandler $fragmentHandler,
        array $fragmentViewRenderers = array()
    ) {
        $this->viewBuilder = $viewBuilder;
        $this->viewRenderer = $viewRenderer;
        $this->fragmentHandler = $fragmentHandler;
        $this->fragmentViewRenderers = $fragmentViewRenderers;
    }

    /**
     * Renders the value object.
     *
     * @param mixed $valueObject
     * @param string $context
     * @param array $parameters
     *
     * @return string
     */
    public function renderValueObject($valueObject, $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = array())
    {
        $view = $this->viewBuilder->buildView($valueObject, $context, $parameters);
        if (!$view instanceof CacheableViewInterface || !$view->isCacheable()) {
            return $this->viewRenderer->renderView($view);
        }

        $fragmentViewRenderer = $this->getFragmentViewRenderer($view);
        if (!$fragmentViewRenderer instanceof FragmentViewRendererInterface) {
            return $this->viewRenderer->renderView($view);
        }

        return $this->fragmentHandler->render(
            $fragmentViewRenderer->getController($view),
            'esi'
        );
    }

    /**
     * Returns the fragment view renderer for the provided view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return \Netgen\BlockManager\View\Fragment\ViewRendererInterface|null
     */
    protected function getFragmentViewRenderer(ViewInterface $view)
    {
        foreach ($this->fragmentViewRenderers as $fragmentViewRenderer) {
            if (!$fragmentViewRenderer->supportsView($view)) {
                continue;
            }

            return $fragmentViewRenderer;
        }
    }
}