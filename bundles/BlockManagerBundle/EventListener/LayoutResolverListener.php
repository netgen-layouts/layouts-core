<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\API\Values\Page\Layout;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;

class LayoutResolverListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface
     */
    protected $layoutResolver;

    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface $layoutResolver
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable $globalVariable
     */
    public function __construct(
        LayoutResolverInterface $layoutResolver,
        ViewBuilderInterface $viewBuilder,
        GlobalVariable $globalVariable
    ) {
        $this->layoutResolver = $layoutResolver;
        $this->viewBuilder = $viewBuilder;
        $this->globalVariable = $globalVariable;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::CONTROLLER => array('onKernelController', -255));
    }

    /**
     * Resolves the layout to be used for the current request.
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) === true) {
            return;
        }

        foreach ($this->layoutResolver->resolveRules() as $rule) {
            if (!$rule->getLayout() instanceof Layout) {
                continue;
            }

            $this->globalVariable->setLayoutView(
                $this->viewBuilder->buildView($rule->getLayout())
            );

            return;
        }
    }
}
