<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Layouts\Event\RenderViewEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class ViewRenderer implements ViewRendererInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private Environment $twig,
    ) {}

    public function renderView(ViewInterface $view): string
    {
        $event = new RenderViewEvent($view);
        $this->eventDispatcher->dispatch($event);

        $event = new RenderViewEvent($view);
        $this->eventDispatcher->dispatch($event, RenderViewEvent::getEventName($view->identifier));

        if ($view->template === null) {
            return '';
        }

        return $this->twig->render($view->template, $view->parameters);
    }
}
