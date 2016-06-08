<?php

namespace Netgen\BlockManager\View;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use RuntimeException;

class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\View\TemplateResolverInterface
     */
    protected $templateResolver;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface[]
     */
    protected $viewProviders = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\TemplateResolverInterface $templateResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Netgen\BlockManager\View\Provider\ViewProviderInterface[] $viewProviders
     */
    public function __construct(TemplateResolverInterface $templateResolver, EventDispatcherInterface $eventDispatcher, array $viewProviders = array())
    {
        foreach ($viewProviders as $viewProvider) {
            if (!$viewProvider instanceof ViewProviderInterface) {
                throw new RuntimeException(
                    sprintf(
                        'View provider "%s" needs to implement ViewProviderInterface.',
                        get_class($viewProvider)
                    )
                );
            }
        }

        $this->templateResolver = $templateResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->viewProviders = $viewProviders;
    }

    /**
     * Builds the view.
     *
     * @param mixed $valueObject
     * @param string $context
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView($valueObject, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array())
    {
        foreach ($this->viewProviders as $viewProvider) {
            if (!$viewProvider->supports($valueObject)) {
                continue;
            }

            $view = $viewProvider->provideView($valueObject, $parameters);
            $view->setContext($context);
            $view->addParameters($parameters);
            $view->addParameters(array('view_context' => $context));

            $event = new CollectViewParametersEvent($view);
            $this->eventDispatcher->dispatch(ViewEvents::BUILD_VIEW, $event);
            $view->addParameters($event->getViewParameters());

            break;
        }

        if (!isset($view)) {
            throw new RuntimeException(
                sprintf(
                    'No view providers found for "%s" value object.',
                    get_class($valueObject)
                )
            );
        }

        $view->setTemplate(
            $this->templateResolver->resolveTemplate($view)
        );

        return $view;
    }
}
