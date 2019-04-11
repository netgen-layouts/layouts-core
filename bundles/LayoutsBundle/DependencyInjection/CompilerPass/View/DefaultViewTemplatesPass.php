<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DefaultViewTemplatesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('netgen_layouts.view')) {
            return;
        }

        $allRules = $container->getParameter('netgen_layouts.view');
        $allRules = $this->updateRules($container, $allRules);

        $container->setParameter('netgen_layouts.view', $allRules);
    }

    /**
     * Updates all view rules to add the default template match.
     */
    protected function updateRules(ContainerBuilder $container, ?array $allRules): array
    {
        $allRules = is_array($allRules) ? $allRules : [];

        $defaultTemplates = $container->getParameter('netgen_layouts.default_view_templates');

        foreach ($defaultTemplates as $viewName => $viewTemplates) {
            foreach ($viewTemplates as $context => $template) {
                $rules = [];

                if (isset($allRules[$viewName][$context]) && is_array($allRules[$viewName][$context])) {
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
     */
    protected function addDefaultRule(string $viewName, string $context, array $rules, string $defaultTemplate): array
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
