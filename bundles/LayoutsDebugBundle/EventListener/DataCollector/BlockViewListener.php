<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\EventListener\DataCollector;

use Netgen\Bundle\LayoutsDebugBundle\DataCollector\LayoutsDataCollector;
use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function in_array;

final class BlockViewListener implements EventSubscriberInterface
{
    /**
     * @param string[] $enabledContexts
     */
    public function __construct(
        private LayoutsDataCollector $dataCollector,
        private array $enabledContexts,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [BuildViewEvent::getEventName('block') => ['onBuildView', -65535]];
    }

    /**
     * Includes results built from all block collections, if specified so.
     */
    public function onBuildView(BuildViewEvent $event): void
    {
        $view = $event->view;

        if (!$view instanceof BlockViewInterface) {
            return;
        }

        if (!in_array($view->context, $this->enabledContexts, true)) {
            return;
        }

        $this->dataCollector->collectBlockView($view);
    }
}
