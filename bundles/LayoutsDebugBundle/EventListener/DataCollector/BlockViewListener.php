<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\EventListener\DataCollector;

use Netgen\Bundle\LayoutsDebugBundle\DataCollector\LayoutsDataCollector;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function in_array;
use function sprintf;

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
        return [sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'block') => ['onBuildView', -65535]];
    }

    /**
     * Includes results built from all block collections, if specified so.
     */
    public function onBuildView(CollectViewParametersEvent $event): void
    {
        $view = $event->getView();

        if (!$view instanceof BlockViewInterface) {
            return;
        }

        if (!in_array($view->getContext(), $this->enabledContexts, true)) {
            return;
        }

        $this->dataCollector->collectBlockView($view);
    }
}
