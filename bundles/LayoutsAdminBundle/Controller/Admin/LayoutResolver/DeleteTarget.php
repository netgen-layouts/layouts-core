<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\ViewInterface;

final class DeleteTarget extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Deletes a rule target.
     */
    public function __invoke(Target $target): ViewInterface
    {
        $rule = $this->layoutResolverService->loadRule($target->getRuleId());

        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->getRuleGroupId()->toString(),
            ],
        );

        $this->layoutResolverService->deleteTarget($target);

        return $this->buildView(
            $this->layoutResolverService->loadRuleDraft(
                $target->getRuleId(),
            ),
            ViewInterface::CONTEXT_ADMIN,
        );
    }
}
