<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteRule extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Deletes a rule.
     */
    public function __invoke(Request $request, Rule $rule): Response
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:delete',
            [
                'rule_group' => $rule->getRuleGroupId()->toString(),
            ],
        );

        if ($request->getMethod() !== Request::METHOD_DELETE) {
            return $this->render(
                '@NetgenLayoutsAdmin/admin/layout_resolver/form/delete_rule.html.twig',
                [
                    'submitted' => false,
                    'error' => false,
                    'rule' => $rule,
                ],
            );
        }

        $this->layoutResolverService->deleteRule($rule);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
