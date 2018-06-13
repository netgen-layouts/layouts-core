<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\RuleViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RuleCountListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    public static function getSubscribedEvents()
    {
        return [BlockManagerEvents::BUILD_VIEW => 'onBuildView'];
    }

    /**
     * Injects the number of rules mapped to the layout in the rule
     * provided by the event.
     *
     * @param \Netgen\BlockManager\Event\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof RuleViewInterface) {
            return;
        }

        $layout = $layout = $view->getRule()->getLayout();

        $ruleCount = 0;
        if ($layout instanceof Layout && $layout->isPublished()) {
            $ruleCount = $this->layoutResolverService->getRuleCount($layout);
        }

        $event->addParameter('rule_count', $ruleCount);
    }
}
