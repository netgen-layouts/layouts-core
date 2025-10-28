<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Utils\BackwardsCompatibility\EventDispatcherProxy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

use function sprintf;

final class ViewRenderer implements ViewRendererInterface
{
    private EventDispatcherProxy $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        private Environment $twig,
    ) {
        $this->eventDispatcher = new EventDispatcherProxy($eventDispatcher);
    }

    public function renderView(ViewInterface $view): string
    {
        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch($event, LayoutsEvents::RENDER_VIEW);
        $view->addParameters($event->getParameters());

        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch($event, sprintf('%s.%s', LayoutsEvents::RENDER_VIEW, $view::getIdentifier()));
        $view->addParameters($event->getParameters());

        $viewTemplate = $view->getTemplate();
        if ($viewTemplate === null) {
            return '';
        }

        return $this->twig->render($viewTemplate, $view->getParameters());
    }
}
