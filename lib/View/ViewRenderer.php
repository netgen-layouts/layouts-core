<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

use function sprintf;

final class ViewRenderer implements ViewRendererInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private Environment $twig,
    ) {}

    public function renderView(ViewInterface $view): string
    {
        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch($event);
        $view->addParameters($event->parameters);

        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch($event, sprintf('%s.%s', LayoutsEvents::RENDER_VIEW, $view->identifier));
        $view->addParameters($event->parameters);

        $viewTemplate = $view->template;
        if ($viewTemplate === null) {
            return '';
        }

        return $this->twig->render($viewTemplate, $view->parameters);
    }
}
