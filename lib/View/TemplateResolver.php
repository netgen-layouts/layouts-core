<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Generator;
use Netgen\Layouts\Exception\View\TemplateResolverException;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @final
 */
class TemplateResolver implements TemplateResolverInterface
{
    /**
     * @var \Netgen\Layouts\View\Matcher\MatcherInterface[]
     */
    private $matchers;

    /**
     * @var array
     */
    private $viewConfig;

    /**
     * @param \Netgen\Layouts\View\Matcher\MatcherInterface[] $matchers
     * @param array<string, array<string, mixed>> $viewConfig
     */
    public function __construct(array $matchers, array $viewConfig)
    {
        $this->matchers = array_filter(
            $matchers,
            static function (MatcherInterface $matcher): bool {
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

        $viewIdentifier = sprintf('%s_view', $view::getIdentifier());
        foreach ($contextList as $context) {
            if (!isset($this->viewConfig[$viewIdentifier][$context])) {
                continue;
            }

            foreach ($this->viewConfig[$viewIdentifier][$context] as $config) {
                if (!$this->matches($view, $config['match'])) {
                    continue;
                }

                $view->setTemplate($config['template']);
                $view->addParameters(
                    iterator_to_array(
                        $this->evaluateParameters($view, $config['parameters'])
                    )
                );

                return;
            }
        }

        throw TemplateResolverException::noTemplateMatch($viewIdentifier, $viewContext ?? '');
    }

    /**
     * Matches the view to provided config with configured matchers.
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
     */
    private function evaluateParameters(ViewInterface $view, array $parameters): Generator
    {
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

            yield $key => $value;
        }
    }
}
