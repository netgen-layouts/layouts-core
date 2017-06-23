<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener\RuleView;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\RuleViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RuleCountListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     */
    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(BlockManagerEvents::BUILD_VIEW => 'onBuildView');
    }

    /**
     * Injects the rule count to the rule.
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
