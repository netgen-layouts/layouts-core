<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function is_array;

final class DefaultViewTemplatesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('netgen_layouts.view')) {
            return;
        }

        /** @var mixed[] $allRules */
        $allRules = $container->getParameter('netgen_layouts.view');
        $allRules = $this->updateRules($container, $allRules);

        $container->setParameter('netgen_layouts.view', $allRules);
    }

    /**
     * Updates all view rules to add the default template match.
     *
     * @param mixed[]|null $allRules
     *
     * @return mixed[]
     */
    private function updateRules(ContainerBuilder $container, ?array $allRules): array
    {
        $allRules = is_array($allRules) ? $allRules : [];

        /** @var array<string, mixed[]> $defaultTemplates */
        $defaultTemplates = $container->getParameter('netgen_layouts.default_view_templates');

        foreach ($defaultTemplates as $viewName => $viewTemplates) {
            foreach ($viewTemplates as $context => $template) {
                $rules = [];

                if (is_array($allRules[$viewName][$context] ?? null)) {
                    $rules = $allRules[$viewName][$context];
                }

                $rules = $this->addDefaultRule($viewName, $context, $rules, $template);

                $allRules[$viewName][$context] = $rules;
            }
        }

        return $allRules;
    }

    /**
     * Adds the default view template as a fallback to specified view rules.
     *
     * @param mixed[] $rules
     *
     * @return mixed[]
     */
    private function addDefaultRule(string $viewName, string $context, array $rules, string $defaultTemplate): array
    {
        $rules += [
            "___{$viewName}_{$context}_default___" => [
                'template' => $defaultTemplate,
                'match' => [],
                'parameters' => [],
            ],
        ];

        return $rules;
    }
}
