<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener;

use Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\BlockManagerAdminBundle\Event\BlockManagerAdminEvents;
use Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SetAdminPageLayoutListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable
     */
    private $globalVariable;

    public function __construct(GlobalVariable $globalVariable)
    {
        $this->globalVariable = $globalVariable;
    }

    public static function getSubscribedEvents(): array
    {
        return [BlockManagerAdminEvents::ADMIN_MATCH => ['onAdminMatch', -65535]];
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
