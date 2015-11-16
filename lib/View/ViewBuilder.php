<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\View\TemplateResolver\TemplateResolverInterface;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Netgen\BlockManager\API\Values\Value;
use InvalidArgumentException;

class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface[]
     */
    protected $viewProviders = array();

    /**
     * @var \Netgen\BlockManager\View\TemplateResolver\TemplateResolverInterface[]
     */
    protected $templateResolvers = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Provider\ViewProviderInterface[] $viewProviders
     * @param \Netgen\BlockManager\View\TemplateResolver\TemplateResolverInterface[] $templateResolvers
     */
    public function __construct(array $viewProviders = array(), array $templateResolvers = array())
    {
        $this->viewProviders = $viewProviders;
        $this->templateResolvers = $templateResolvers;
    }

    /**
     * Builds the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param array $parameters
     * @param string $context
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView(Value $value, array $parameters = array(), $context = 'view')
    {
        foreach ($this->viewProviders as $viewProvider) {
            if (!$viewProvider instanceof ViewProviderInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'View provider for %s value needs to implement ViewProviderInterface.',
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
        }

        if (!isset($view)) {
            throw new InvalidArgumentException(
                sprintf(
                    'No view providers found for %s class.',
                    get_class($value)
                )
            );
        }

        foreach ($this->templateResolvers as $type => $templateResolver) {
            if (!$templateResolver instanceof TemplateResolverInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Template resolver for %s type needs to implement TemplateResolverInterface.',
                        $type
                    )
                );
            }

            if (!$templateResolver->supports($view)) {
                continue;
            }

            $view->setTemplate(
                $templateResolver->resolveTemplate($view)
            );
        }

        return $view;
    }
}
