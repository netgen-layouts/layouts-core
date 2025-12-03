<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RuleCountListener implements EventSubscriberInterface
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [BuildViewEvent::getEventName('layout') => 'onBuildView'];
    }

    /**
     * Injects the number of rules mapped to the layout in the rule
     * provided by the event.
     */
    public function onBuildView(BuildViewEvent $event): void
    {
        $view = $event->view;
        if (!$view instanceof LayoutViewInterface) {
            return;
        }

        if ($view->context !== ViewInterface::CONTEXT_ADMIN) {
            return;
        }

        $ruleCount = 0;
        if ($view->layout->isPublished) {
            $ruleCount = $this->layoutResolverService->getRuleCountForLayout($view->layout);
        }

        $event->view->addParameter('rule_count', $ruleCount);
    }
}
