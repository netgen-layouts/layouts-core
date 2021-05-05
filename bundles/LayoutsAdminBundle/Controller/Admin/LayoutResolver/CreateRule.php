<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\View\ViewInterface;

final class CreateRule extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Creates a new rule in provided parent group.
     */
    public function __invoke(RuleGroup $ruleGroup): ViewInterface
    {
        $this->denyAccessUnlessGranted('nglayouts:mapping:edit', $ruleGroup);

        $createdRule = $this->layoutResolverService->transaction(
            function () use ($ruleGroup): Rule {
                $createdRule = $this->layoutResolverService->createRule(
                    $this->layoutResolverService->newRuleCreateStruct(),
                    $ruleGroup,
                );

                return $this->layoutResolverService->publishRule($createdRule);
            },
        );

        return $this->buildView($createdRule, ViewInterface::CONTEXT_ADMIN);
    }
}
