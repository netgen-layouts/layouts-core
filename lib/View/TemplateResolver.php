<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Exception\View\TemplateResolverException;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @final
 */
class TemplateResolver implements TemplateResolverInterface
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface[]
     */
    private $matchers = [];

    /**
     * @var array
     */
    private $viewConfig = [];

    /**
     * @param \Netgen\BlockManager\View\Matcher\MatcherInterface[] $matchers
     * @param array $viewConfig
     */
    public function __construct(array $matchers = [], array $viewConfig = [])
    {
        $this->matchers = array_filter(
            $matchers,
            function (MatcherInterface $matcher): bool {
                return true;
            }
        );

        $this->viewConfig = $viewConfig;
    }

    public function resolveTemplate(ViewInterface $view): void
    {
        $viewContext = $view->getContext();
        $fallbackViewContext = $view->getFallbackContext();

        $contextList = [$viewContext];
        if (is_string($fallbackViewContext)) {
            $contextList[] = $fallbackViewContext;
        }

        $viewIdentifier = $view->getIdentifier();
        foreach ($contextList as $context) {
            if (!isset($this->viewConfig[$viewIdentifier][$context])) {
                continue;
            }

            foreach ($this->viewConfig[$viewIdentifier][$context] as $config) {
                if (!$this->matches($view, $config['match'])) {
                    continue;
                }

                $view->setTemplate($config['template']);
                $view->addParameters($this->evaluateParameters($view, $config['parameters']));

                return;
            }
        }

        throw TemplateResolverException::noTemplateMatch($view->getIdentifier(), $viewContext);
    }

    /**
     * Matches the view to provided config with configured matchers.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $matchConfig
     *
     * @return bool
     */
    private function matches(ViewInterface $view, array $matchConfig): bool
    {
        foreach ($matchConfig as $matcher => $matcherConfig) {
            if (!isset($this->matchers[$matcher])) {
                throw TemplateResolverException::noTemplateMatcher($matcher);
            }

            $matcherConfig = !is_array($matcherConfig) ? [$matcherConfig] : $matcherConfig;
            if (!$this->matchers[$matcher]->match($view, $matcherConfig)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Iterates over all provided parameters and evaluates the values with expression
     * engine if the parameter value specifies so.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $parameters
     *
     * @return array
     */
    private function evaluateParameters(ViewInterface $view, array $parameters): array
    {
        $evaluatedParameters = [];

        foreach ($parameters as $key => $value) {
            if (is_string($value) && mb_strpos($value, '@=') === 0) {
                $expressionLanguage = new ExpressionLanguage();
                $value = $expressionLanguage->evaluate(
                    mb_substr($value, 2),
                    [
                        'view' => $view,
                    ] + $view->getParameters()
                );
            }

            $evaluatedParameters[$key] = $value;
        }

        return $evaluatedParameters;
    }
}
