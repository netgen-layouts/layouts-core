<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;

class PageLayoutListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface
     */
    protected $pageLayoutResolver;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface $pageLayoutResolver
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable $globalVariable
     */
    public function __construct(
        PageLayoutResolverInterface $pageLayoutResolver,
        GlobalVariable $globalVariable
    ) {
        $this->pageLayoutResolver = $pageLayoutResolver;
        $this->globalVariable = $globalVariable;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => 'onKernelRequest');
    }

    /**
     * Resolves the main page layout to be used for the current request.
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

        $this->globalVariable->setPageLayoutTemplate(
            $this->pageLayoutResolver->resolvePageLayout()
        );
    }
}
