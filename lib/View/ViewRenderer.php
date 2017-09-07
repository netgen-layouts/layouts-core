<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

class ViewRenderer implements ViewRendererInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Twig\Environment $twig
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, Environment $twig)
    {
        $this->eventDispatcher = $eventDispatcher;
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
        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch(BlockManagerEvents::RENDER_VIEW, $event);
        $view->addParameters($event->getParameters());

        return $this->twig->render($view->getTemplate(), $view->getParameters());
    }
}
