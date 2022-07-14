<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Utils\BackwardsCompatibility\EventDispatcherProxy;
use Netgen\Layouts\View\Provider\ViewProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use function get_debug_type;
use function sprintf;

final class ViewBuilder implements ViewBuilderInterface
{
    private TemplateResolverInterface $templateResolver;

    private EventDispatcherProxy $eventDispatcher;

    /**
     * @var \Netgen\Layouts\View\Provider\ViewProviderInterface[]
     */
    private array $viewProviders = [];

    /**
     * @param iterable<\Netgen\Layouts\View\Provider\ViewProviderInterface> $viewProviders
     */
    public function __construct(TemplateResolverInterface $templateResolver, EventDispatcherInterface $eventDispatcher, iterable $viewProviders)
    {
        $this->templateResolver = $templateResolver;
        $this->eventDispatcher = new EventDispatcherProxy($eventDispatcher);

        foreach ($viewProviders as $viewProvider) {
            if ($viewProvider instanceof ViewProviderInterface) {
                $this->viewProviders[] = $viewProvider;
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
     */
    private function getViewProvider($value): ViewProviderInterface
    {
        foreach ($this->viewProviders as $viewProvider) {
            if ($viewProvider->supports($value)) {
                return $viewProvider;
            }
        }

        throw ViewProviderException::noViewProvider(get_debug_type($value));
    }
}
