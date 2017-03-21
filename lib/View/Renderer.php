<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig_Environment;

class Renderer implements RendererInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Twig_Environment $twig
     */
    public function __construct(
        ViewBuilderInterface $viewBuilder,
        EventDispatcherInterface $eventDispatcher,
        Twig_Environment $twig
    ) {
        $this->viewBuilder = $viewBuilder;
        $this->eventDispatcher = $eventDispatcher;
        $this->twig = $twig;
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
        return $this->renderView(
            $this->viewBuilder->buildView($valueObject, $context, $parameters)
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
        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch(BlockManagerEvents::RENDER_VIEW, $event);
        $view->addParameters($event->getParameters());

        return $this->twig->render($view->getTemplate(), $view->getParameters());
    }
}
