<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\ViewInterface;

final class DisableRule extends AbstractController
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    /**
     * Disables a rule.
     */
    public function __invoke(Rule $rule): ViewInterface
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:activate',
            [
                'rule_group' => $rule->ruleGroupId->toString(),
            ],
        );

        $disabledRule = $this->layoutResolverService->disableRule($rule);

        return $this->buildView($disabledRule, ViewInterface::CONTEXT_ADMIN);
    }
}
