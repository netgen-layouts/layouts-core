<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\ViewInterface;

final class EnableRule extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Enables a rule.
     */
    public function __invoke(Rule $rule): ViewInterface
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:activate',
            [
                'rule_group' => $rule->getRuleGroupId()->toString(),
            ],
        );

        $enabledRule = $this->layoutResolverService->enableRule($rule);

        return $this->buildView($enabledRule, ViewInterface::CONTEXT_ADMIN);
    }
}
