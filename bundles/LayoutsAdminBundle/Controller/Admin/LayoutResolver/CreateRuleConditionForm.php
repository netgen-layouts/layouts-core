<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateRuleConditionForm extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    private ConditionTypeRegistry $conditionTypeRegistry;

    public function __construct(
        LayoutResolverService $layoutResolverService,
        ConditionTypeRegistry $conditionTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Displays the rule condition create form.
     */
    public function __invoke(Rule $rule, string $type, Request $request): ViewInterface
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->getRuleGroupId()->toString(),
            ],
        );

        $conditionType = $this->conditionTypeRegistry->getConditionType($type);
        $createStruct = $this->layoutResolverService->newConditionCreateStruct($type);

        $form = $this->createForm(
            ConditionType::class,
            $createStruct,
            [
                'condition_type' => $conditionType,
                'action' => $this->generateUrl(
                    'nglayouts_admin_layout_resolver_rule_condition_form_create',
                    [
                        'ruleId' => $rule->getId()->toString(),
                        'type' => $type,
                    ],
                ),
            ],
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->addRuleCondition($rule, $createStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $rule->getId(),
                ),
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
