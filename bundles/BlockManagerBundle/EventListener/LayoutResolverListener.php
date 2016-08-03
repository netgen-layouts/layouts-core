<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\API\Values\Page\LayoutInfo;
use Netgen\BlockManager\API\Service\LayoutService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;

class LayoutResolverListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface
     */
    protected $layoutResolver;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper
     */
    protected $globalHelper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface $layoutResolver
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper $globalHelper
     */
    public function __construct(
        LayoutResolverInterface $layoutResolver,
        LayoutService $layoutService,
        ViewBuilderInterface $viewBuilder,
        GlobalHelper $globalHelper
    ) {
        $this->layoutResolver = $layoutResolver;
        $this->layoutService = $layoutService;
        $this->viewBuilder = $viewBuilder;
        $this->globalHelper = $globalHelper;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => array('onKernelRequest', -255));
    }

    /**
     * Resolves the layout to be used for the current request.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) === true) {
            return;
        }

        foreach ($this->layoutResolver->resolveRules() as $rule) {
            if (!$rule->getLayout() instanceof LayoutInfo) {
                continue;
            }

            // We need the complete layout, not just the reference
            $layout = $this->layoutService->loadLayoutDraft(
                $rule->getLayout()->getId()
            );

            $this->globalHelper->setLayoutView(
                $this->viewBuilder->buildView($layout)
            );

            return;
        }
    }
}
