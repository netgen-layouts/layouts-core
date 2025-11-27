<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\LayoutsAdminBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SetAdminPageLayoutListener implements EventSubscriberInterface
{
    public function __construct(
        private GlobalVariable $globalVariable,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [AdminMatchEvent::class => ['onAdminMatch', -65535]];
    }

    /**
     * Sets the pagelayout template for admin interface.
     */
    public function onAdminMatch(AdminMatchEvent $event): void
    {
        if ($event->pageLayoutTemplate === null) {
            return;
        }

        $this->globalVariable->setPageLayoutTemplate($event->pageLayoutTemplate);
    }
}
