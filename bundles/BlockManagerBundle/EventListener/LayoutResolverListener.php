<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Layout\Resolver\Rule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface $layoutResolver
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper $globalHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LayoutResolverInterface $layoutResolver,
        LayoutService $layoutService,
        ViewBuilderInterface $viewBuilder,
        GlobalHelper $globalHelper,
        LoggerInterface $logger = null
    ) {
        $this->layoutResolver = $layoutResolver;
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

        $layout = null;
        foreach ($this->layoutResolver->resolveRules() as $rule) {
            try {
                $layout = $this->layoutService->loadLayout($rule->getLayoutId());
            } catch (NotFoundException $e) {
                // If layout was not found, we still want to display the page
                $this->logger->notice(
                    sprintf(
                        'Layout resolver rule with ID %d matched a layout with ID %d, but the layout was not found',
                        $rule->getId(),
                        $rule->getLayoutId()
                    ),
                    array('ngbm')
                );

                return;
            }
        }

        if (!$layout instanceof Layout) {
            return;
        }

        $this->globalHelper->setLayoutView(
            $this->viewBuilder->buildView($layout)
        );
    }
}
