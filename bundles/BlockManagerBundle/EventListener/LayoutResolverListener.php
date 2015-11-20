<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\API\Values\Page\Layout;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Netgen\BlockManager\LayoutResolver\LayoutResolverInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;

class LayoutResolverListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\LayoutResolver\LayoutResolverInterface
     */
    protected $layoutResolver;

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
     * @param \Netgen\BlockManager\LayoutResolver\LayoutResolverInterface $layoutResolver
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper $globalHelper
     */
    public function __construct(
        LayoutResolverInterface $layoutResolver,
        ViewBuilderInterface $viewBuilder,
        GlobalHelper $globalHelper
    ) {
        $this->layoutResolver = $layoutResolver;
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

        $layout = $this->layoutResolver->resolveLayout();
        if (!$layout instanceof Layout) {
            return;
        }

        $layoutView = $this->viewBuilder->buildView($layout);
        $this->globalHelper->setLayoutView($layoutView);
    }
}
