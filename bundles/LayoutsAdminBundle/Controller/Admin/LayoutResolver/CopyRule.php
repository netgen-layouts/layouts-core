<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\ViewInterface;

final class CopyRule extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Copies a rule. Rule is added to a position below the copied one, and
     * deactivated by default.
     */
    public function __invoke(Rule $rule): ViewInterface
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->getRuleGroupId()->toString(),
            ]
        );

        $updateStruct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
        $updateStruct->priority = $rule->getPriority() - 1;

        $targetGroup = $this->layoutResolverService->loadRuleGroup($rule->getRuleGroupId());

        $copiedRule = $this->layoutResolverService->transaction(
            function () use ($rule, $targetGroup, $updateStruct): Rule {
                $copiedRule = $this->layoutResolverService->updateRuleMetadata(
                    $this->layoutResolverService->copyRule($rule, $targetGroup),
                    $updateStruct
                );

                if (!$copiedRule->isEnabled()) {
                    return $copiedRule;
                }

                return $this->layoutResolverService->disableRule($copiedRule);
            }
        );

        return $this->buildView($copiedRule, ViewInterface::CONTEXT_ADMIN);
    }
}
