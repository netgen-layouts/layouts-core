<?php

namespace Netgen\BlockManager\View;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Netgen\BlockManager\Core\Values\Value;
use RuntimeException;

class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface[]
     */
    protected $viewProviders = array();

    /**
     * @var \Netgen\BlockManager\View\TemplateResolverInterface
     */
    protected $templateResolver;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Provider\ViewProviderInterface[] $viewProviders
     * @param \Netgen\BlockManager\View\TemplateResolverInterface $templateResolver
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(array $viewProviders = array(), TemplateResolverInterface $templateResolver, EventDispatcherInterface $eventDispatcher)
    {
        $this->viewProviders = $viewProviders;
        $this->templateResolver = $templateResolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Builds the view.
     *
     * @param \Netgen\BlockManager\Core\Values\Value $value
     * @param string $context
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView(Value $value, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array())
    {
        foreach ($this->viewProviders as $viewProvider) {
            if (!$viewProvider instanceof ViewProviderInterface) {
                throw new RuntimeException(
                    sprintf(
                        'View provider for "%s" value object needs to implement ViewProviderInterface.',
                        get_class($value)
                    )
                );
            }

            if (!$viewProvider->supports($value)) {
                continue;
            }

            $view = $viewProvider->provideView($value);
            $view->setContext($context);
            $view->addParameters($parameters);

            $event = new CollectViewParametersEvent($view);
            $this->eventDispatcher->dispatch(ViewEvents::BUILD_VIEW, $event);
            $view->addParameters($event->getViewParameters());

            break;
        }

        if (!isset($view)) {
            throw new RuntimeException(
                sprintf(
                    'No view providers found for "%s" value object.',
                    get_class($value)
                )
            );
        }

        $view->setTemplate(
            $this->templateResolver->resolveTemplate($view)
        );

        return $view;
    }
}
