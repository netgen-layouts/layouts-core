<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DefaultViewTemplatesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('netgen_block_manager.view')) {
            return;
        }

        $allRules = $container->getParameter('netgen_block_manager.view');
        $allRules = $this->updateRules($container, $allRules);

        $container->setParameter('netgen_block_manager.view', $allRules);
    }

    /**
     * Updates all view rules to add the default template match.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $allRules
     *
     * @return array
     */
    protected function updateRules(ContainerBuilder $container, $allRules)
    {
        $defaultTemplates = $container->getParameter('netgen_block_manager.default_view_templates');

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
     *
     * @param string $viewName
     * @param string $context
     * @param array $rules
     * @param string $defaultTemplate
     *
     * @return array
     */
    protected function addDefaultRule($viewName, $context, array $rules, $defaultTemplate)
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
