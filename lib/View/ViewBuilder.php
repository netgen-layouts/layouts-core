<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\View\ViewProviderException;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\View\TemplateResolverInterface
     */
    private $templateResolver;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface[]
     */
    private $viewProviders = array();

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
                throw new InvalidInterfaceException(
                    'View provider',
                    get_class($viewProvider),
                    ViewProviderInterface::class
                );
            }
        }

        $this->templateResolver = $templateResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->viewProviders = $viewProviders;
    }

    public function buildView($valueObject, $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = array())
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
    private function getViewProvider($valueObject)
    {
        foreach ($this->viewProviders as $viewProvider) {
            if ($viewProvider->supports($valueObject)) {
                return $viewProvider;
            }
        }

        throw ViewProviderException::noViewProvider(get_class($valueObject));
    }
}
