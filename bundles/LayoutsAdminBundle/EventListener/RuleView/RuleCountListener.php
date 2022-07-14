<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener\RuleView;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\View\View\RuleViewInterface;
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
        return [sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'rule') => 'onBuildView'];
    }

    /**
     * Injects the number of rules mapped to the layout in the rule
     * provided by the event.
     */
    public function onBuildView(CollectViewParametersEvent $event): void
    {
        $view = $event->getView();
        if (!$view instanceof RuleViewInterface) {
            return;
        }

        $layout = $view->getRule()->getLayout();

        $ruleCount = 0;
        if ($layout instanceof Layout && $layout->isPublished()) {
            $ruleCount = $this->layoutResolverService->getRuleCountForLayout($layout);
        }

        $event->addParameter('rule_count', $ruleCount);
    }
}
