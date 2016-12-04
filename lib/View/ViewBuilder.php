<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @param array $parameters
     * @param string $context
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView($valueObject, array $parameters = array(), $context = ViewInterface::CONTEXT_DEFAULT)
    {
        $viewProvider = $this->getViewProvider($valueObject);

        $view = $viewProvider->provideView($valueObject, $parameters);
        $view->setContext($context);
        $view->addParameters($parameters);
        $view->addParameter('view_context', $context);

        $this->templateResolver->resolveTemplate($view);

        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch(BlockManagerEvents::BUILD_VIEW, $event);
        $view->addParameters($event->getParameters());

        return $view;
    }

    /**
     * Returns the view provider that supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected function getViewProvider($valueObject)
    {
        foreach ($this->viewProviders as $viewProvider) {
            if ($viewProvider->supports($valueObject)) {
                return $viewProvider;
            }
        }

        throw new RuntimeException(
            sprintf(
                'No view providers found for "%s" value object.',
                get_class($valueObject)
            )
        );
    }
}
