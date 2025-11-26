<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UnlinkLayout extends AbstractController
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

    /**
     * Unlinks the layout from the rule.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule does not have a linked layout
     */
    public function __invoke(Rule $rule, Request $request): ViewInterface|Response
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->ruleGroupId->toString(),
            ],
        );

        if (!$rule->getLayout() instanceof Layout) {
            throw new BadStateException('rule', 'Rule does not have a linked layout.');
        }

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->render(
                '@NetgenLayoutsAdmin/admin/layout_resolver/form/unlink_layout.html.twig',
                [
                    'submitted' => false,
                    'error' => false,
                    'rule' => $rule,
                ],
            );
        }

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = false;

        $updatedRule = $this->layoutResolverService->updateRule(
            $rule,
            $ruleUpdateStruct,
        );

        return $this->buildView($updatedRule, ViewInterface::CONTEXT_ADMIN);
    }
}
