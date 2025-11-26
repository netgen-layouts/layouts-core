<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\ViewInterface;

final class DeleteTarget extends AbstractController
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    /**
     * Deletes a rule target.
     */
    public function __invoke(Target $target): ViewInterface
    {
        $rule = $this->layoutResolverService->loadRule($target->ruleId);

        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->ruleGroupId->toString(),
            ],
        );

        $this->layoutResolverService->deleteTarget($target);

        return $this->buildView(
            $this->layoutResolverService->loadRuleDraft(
                $target->ruleId,
            ),
            ViewInterface::CONTEXT_ADMIN,
        );
    }
}
