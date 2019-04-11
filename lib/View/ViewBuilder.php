<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Utils\BackwardsCompatibility\EventDispatcherProxy;
use Netgen\Layouts\View\Provider\ViewProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\Layouts\View\TemplateResolverInterface
     */
    private $templateResolver;

    /**
     * @var \Netgen\Layouts\Utils\BackwardsCompatibility\EventDispatcherProxy
     */
    private $eventDispatcher;

    /**
     * @var \Netgen\Layouts\View\Provider\ViewProviderInterface[]
     */
    private $viewProviders = [];

    public function __construct(TemplateResolverInterface $templateResolver, EventDispatcherInterface $eventDispatcher, iterable $viewProviders)
    {
        $this->templateResolver = $templateResolver;
        $this->eventDispatcher = new EventDispatcherProxy($eventDispatcher);

        foreach ($viewProviders as $key => $viewProvider) {
            if ($viewProvider instanceof ViewProviderInterface) {
                $this->viewProviders[$key] = $viewProvider;
            }
        }
    }

    public function buildView($value, string $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = []): ViewInterface
    {
        $viewProvider = $this->getViewProvider($value);

        $view = $viewProvider->provideView($value, $parameters);
        $view->setContext($context);
        $view->addParameters($parameters);
        $view->addParameter('view_context', $context);

        $this->templateResolver->resolveTemplate($view);

        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch($event, LayoutsEvents::BUILD_VIEW);
        $view->addParameters($event->getParameters());

        $event = new CollectViewParametersEvent($view);
        $this->eventDispatcher->dispatch($event, sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, $view::getIdentifier()));
        $view->addParameters($event->getParameters());

        return $view;
    }

    /**
     * Returns the view provider that supports the given value.
     *
     * @param mixed $value
     *
     * @return \Netgen\Layouts\View\Provider\ViewProviderInterface
     */
    private function getViewProvider($value): ViewProviderInterface
    {
        foreach ($this->viewProviders as $viewProvider) {
            if ($viewProvider->supports($value)) {
                return $viewProvider;
            }
        }

        throw ViewProviderException::noViewProvider(get_class($value));
    }
}
