<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\LayoutsAdminBundle\Event\LayoutsAdminEvents;
use Netgen\Bundle\LayoutsAdminBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SetAdminPageLayoutListener implements EventSubscriberInterface
{
    private GlobalVariable $globalVariable;

    public function __construct(GlobalVariable $globalVariable)
    {
        $this->globalVariable = $globalVariable;
    }

    public static function getSubscribedEvents(): array
    {
        return [LayoutsAdminEvents::ADMIN_MATCH => ['onAdminMatch', -65535]];
    }

    /**
     * Sets the pagelayout template for admin interface.
     */
    public function onAdminMatch(AdminMatchEvent $event): void
    {
        $pageLayoutTemplate = $event->getPageLayoutTemplate();

        if ($pageLayoutTemplate === null) {
            return;
        }

        $this->globalVariable->setPageLayoutTemplate($pageLayoutTemplate);
    }
}
