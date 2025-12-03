<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\View\View\RuleViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RuleCountListener implements EventSubscriberInterface
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [BuildViewEvent::getEventName('rule') => 'onBuildView'];
    }

    /**
     * Injects the number of rules mapped to the layout in the rule
     * provided by the event.
     */
    public function onBuildView(BuildViewEvent $event): void
    {
        $view = $event->view;
        if (!$view instanceof RuleViewInterface) {
            return;
        }

        $ruleCount = 0;
        if ($view->rule->layout instanceof Layout && $view->rule->layout->isPublished) {
            $ruleCount = $this->layoutResolverService->getRuleCountForLayout($view->rule->layout);
        }

        $event->view->addParameter('rule_count', $ruleCount);
    }
}
