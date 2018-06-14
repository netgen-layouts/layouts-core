<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
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
    private $viewProviders = [];

    /**
     * @param \Netgen\BlockManager\View\TemplateResolverInterface $templateResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Netgen\BlockManager\View\Provider\ViewProviderInterface[] $viewProviders
     */
    public function __construct(TemplateResolverInterface $templateResolver, EventDispatcherInterface $eventDispatcher, array $viewProviders = [])
    {
        $this->templateResolver = $templateResolver;
        $this->eventDispatcher = $eventDispatcher;

        $this->viewProviders = array_filter(
            $viewProviders,
            function (ViewProviderInterface $viewProvider): bool {
                return true;
            }
        );
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
        $this->eventDispatcher->dispatch(BlockManagerEvents::BUILD_VIEW, $event);
        $view->addParameters($event->getParameters());

        return $view;
    }

    /**
     * Returns the view provider that supports the given value.
     *
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\View\Provider\ViewProviderInterface
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
