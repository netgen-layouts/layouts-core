<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Generator;
use Netgen\Layouts\Exception\View\TemplateResolverException;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

use function is_array;
use function is_string;
use function iterator_to_array;
use function mb_substr;
use function sprintf;
use function str_starts_with;

/**
 * @final
 */
class TemplateResolver implements TemplateResolverInterface
{
    /**
     * @var mixed[]
     */
    private array $viewConfig;

    private ContainerInterface $matchers;

    /**
     * @param mixed[] $viewConfig
     */
    public function __construct(array $viewConfig, ContainerInterface $matchers)
    {
        $this->viewConfig = $viewConfig;
        $this->matchers = $matchers;
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
            if (!is_string($context) || !isset($this->viewConfig[$viewIdentifier][$context])) {
                continue;
            }

            foreach ($this->viewConfig[$viewIdentifier][$context] as $config) {
                if (!$this->matches($view, $config['match'])) {
                    continue;
                }

                $view->setTemplate($config['template']);
                $view->addParameters(
                    iterator_to_array(
                        $this->evaluateParameters($view, $config['parameters']),
                    ),
                );

                return;
            }
        }

        throw TemplateResolverException::noTemplateMatch($viewIdentifier, $viewContext ?? '');
    }

    /**
     * Matches the view to provided config with configured matchers.
     *
     * @param mixed[] $matchConfig
     */
    private function matches(ViewInterface $view, array $matchConfig): bool
    {
        foreach ($matchConfig as $identifier => $matcherConfig) {
            $matcher = $this->getMatcher($identifier);
            $matcherConfig = !is_array($matcherConfig) ? [$matcherConfig] : $matcherConfig;

            if (!$matcher->match($view, $matcherConfig)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Iterates over all provided parameters and evaluates the values with expression
     * engine if the parameter value specifies so.
     *
     * @param array<string, mixed> $parameters
     *
     * @return \Generator<string, mixed>
     */
    private function evaluateParameters(ViewInterface $view, array $parameters): Generator
    {
        foreach ($parameters as $key => $value) {
            if (is_string($value) && str_starts_with($value, '@=')) {
                $expressionLanguage = new ExpressionLanguage();
                $value = $expressionLanguage->evaluate(
                    mb_substr($value, 2),
                    [
                        'view' => $view,
                    ] + $view->getParameters(),
                );
            }

            yield $key => $value;
        }
    }

    /**
     * Returns the matcher for provided identifier from the collection.
     *
     * @throws \Netgen\Layouts\Exception\View\TemplateResolverException If the matcher does not exist or is not of correct type
     */
    private function getMatcher(string $identifier): MatcherInterface
    {
        if (!$this->matchers->has($identifier)) {
            throw TemplateResolverException::noTemplateMatcher($identifier);
        }

        $matcher = $this->matchers->get($identifier);
        if (!$matcher instanceof MatcherInterface) {
            throw TemplateResolverException::noTemplateMatcher($identifier);
        }

        return $matcher;
    }
}
