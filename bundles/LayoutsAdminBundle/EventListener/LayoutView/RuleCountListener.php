<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function sprintf;

final class RuleCountListener implements EventSubscriberInterface
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    public static function getSubscribedEvents(): array
    {
        return [sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'layout') => 'onBuildView'];
    }

    /**
     * Injects the number of rules mapped to the layout in the rule
     * provided by the event.
     */
    public function onBuildView(CollectViewParametersEvent $event): void
    {
        $view = $event->getView();
        if (!$view instanceof LayoutViewInterface) {
            return;
        }

        if ($view->getContext() !== ViewInterface::CONTEXT_ADMIN) {
            return;
        }

        $ruleCount = 0;
        if ($view->getLayout()->isPublished()) {
            $ruleCount = $this->layoutResolverService->getRuleCountForLayout($view->getLayout());
        }

        $event->addParameter('rule_count', $ruleCount);
    }
}
