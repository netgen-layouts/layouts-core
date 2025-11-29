<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Layouts\Exception\View\TemplateResolverException;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

use function is_array;
use function is_string;
use function mb_substr;
use function sprintf;
use function str_starts_with;

final class TemplateResolver implements TemplateResolverInterface
{
    public function __construct(
        private ConfigurationInterface $configuration,
        private ContainerInterface $matchers,
    ) {}

    public function resolveTemplate(ViewInterface $view): void
    {
        $viewConfig = $this->configuration->getParameter('view');

        $contextList = [$view->context];
        if ($view->fallbackContext !== null) {
            $contextList[] = $view->fallbackContext;
        }

        $viewIdentifier = sprintf('%s_view', $view->identifier);
        foreach ($contextList as $context) {
            if (!is_string($context) || !isset($viewConfig[$viewIdentifier][$context])) {
                continue;
            }

            foreach ($viewConfig[$viewIdentifier][$context] as $config) {
                if (!$this->matches($view, $config['match'])) {
                    continue;
                }

                $view->template = $config['template'];
                $view->addParameters([...$this->evaluateParameters($view, $config['parameters'])]);

                return;
            }
        }

        throw TemplateResolverException::noTemplateMatch($viewIdentifier, $view->context ?? '');
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
     * @return iterable<string, mixed>
     */
    private function evaluateParameters(ViewInterface $view, array $parameters): iterable
    {
        foreach ($parameters as $key => $value) {
            if (is_string($value) && str_starts_with($value, '@=')) {
                $expressionLanguage = new ExpressionLanguage();
                $value = $expressionLanguage->evaluate(
                    mb_substr($value, 2),
                    [...$view->parameters, 'view' => $view],
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
