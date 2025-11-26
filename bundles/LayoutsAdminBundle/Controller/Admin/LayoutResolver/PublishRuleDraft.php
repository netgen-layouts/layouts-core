<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\ViewInterface;

final class PublishRuleDraft extends AbstractController
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    /**
     * Publishes a rule draft.
     */
    public function __invoke(Rule $rule): ViewInterface
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->ruleGroupId->toString(),
            ],
        );

        $publishedRule = $this->layoutResolverService->publishRule($rule);

        return $this->buildView($publishedRule, ViewInterface::CONTEXT_ADMIN);
    }
}
