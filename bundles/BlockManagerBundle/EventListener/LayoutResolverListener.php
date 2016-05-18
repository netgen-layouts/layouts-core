<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\LayoutResolver\Rule;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface
     */
    protected $pageLayoutResolver;

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
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\LayoutResolver\LayoutResolverInterface $layoutResolver
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface $pageLayoutResolver
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper $globalHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LayoutResolverInterface $layoutResolver,
        PageLayoutResolverInterface $pageLayoutResolver,
        LayoutService $layoutService,
        ViewBuilderInterface $viewBuilder,
        GlobalHelper $globalHelper,
        LoggerInterface $logger = null
    ) {
        $this->layoutResolver = $layoutResolver;
        $this->pageLayoutResolver = $pageLayoutResolver;
        $this->layoutService = $layoutService;
        $this->viewBuilder = $viewBuilder;
        $this->globalHelper = $globalHelper;
        $this->logger = $logger ?: new NullLogger();
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

        $this->globalHelper->setPageLayout(
            $this->pageLayoutResolver->resolvePageLayout()
        );

        $rule = $this->layoutResolver->resolveLayout();
        if (!$rule instanceof Rule) {
            return;
        }

        try {
            $layout = $this->layoutService->loadLayout($rule->layoutId);
        } catch (NotFoundException $e) {
            // If layout was not found, we still want to display the page
            $this->logger->notice(
                sprintf(
                    'Layout resolver rule matched a layout with ID %d, but it was not found',
                    $rule->layoutId
                ),
                'ngbm'
            );

            return;
        }

        $this->globalHelper->setLayoutView(
            $this->viewBuilder->buildView($layout)
        );
    }
}
