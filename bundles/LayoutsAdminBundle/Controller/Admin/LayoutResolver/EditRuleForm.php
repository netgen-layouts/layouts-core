<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Layout\Resolver\Form\RuleType;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditRuleForm extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Displays the rule edit form.
     */
    public function __invoke(Rule $rule, Request $request): ViewInterface
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->getRuleGroupId()->toString(),
            ],
        );

        $updateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $updateStruct->description = $rule->getDescription();

        $form = $this->createForm(
            RuleType::class,
            $updateStruct,
            [
                'action' => $this->generateUrl(
                    'nglayouts_admin_layout_resolver_rule_form_edit',
                    [
                        'ruleId' => $rule->getId()->toString(),
                    ],
                ),
            ],
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->updateRule($rule, $updateStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft($rule->getId()),
                ViewInterface::CONTEXT_ADMIN,
            );
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_ADMIN,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY),
        );
    }
}
