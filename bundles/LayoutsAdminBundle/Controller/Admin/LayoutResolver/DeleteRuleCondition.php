<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\View\ViewInterface;

final class DeleteRuleCondition extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Deletes a rule condition.
     */
    public function __invoke(RuleCondition $condition): ViewInterface
    {
        $rule = $this->layoutResolverService->loadRule($condition->getRuleId());

        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->getRuleGroupId()->toString(),
            ],
        );

        $this->layoutResolverService->deleteCondition($condition);

        return $this->buildView(
            $this->layoutResolverService->loadRuleDraft(
                $condition->getRuleId(),
            ),
            ViewInterface::CONTEXT_ADMIN,
        );
    }
}
