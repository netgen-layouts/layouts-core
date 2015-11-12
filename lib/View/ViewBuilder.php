<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Value;
use InvalidArgumentException;

class ViewBuilder implements ViewBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProvider[]
     */
    protected $viewProviders = array();

    /**
     * @var \Netgen\BlockManager\View\TemplateResolverInterface[]
     */
    protected $templateResolvers = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\Provider\ViewProvider[] $viewProviders
     * @param \Netgen\BlockManager\View\TemplateResolverInterface[] $templateResolvers
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
            if (!$viewProvider->supports($value)) {
                continue;
            }

            $view = $viewProvider->provideView($value, $parameters, $context);
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
            if (!is_a($view, $type)) {
                continue;
            }

            $view->setTemplate(
                $templateResolver->resolveTemplate($view)
            );
        }

        return $view;
    }
}
