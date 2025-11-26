<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Layout\Resolver\Form\TargetType;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTargetForm extends AbstractController
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
        private TargetTypeRegistry $targetTypeRegistry,
    ) {}

    /**
     * Displays the target create form.
     */
    public function __invoke(Rule $rule, string $type, Request $request): ViewInterface
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->ruleGroupId->toString(),
            ],
        );

        $targetType = $this->targetTypeRegistry->getTargetType($type);
        $createStruct = $this->layoutResolverService->newTargetCreateStruct($type);

        $form = $this->createForm(
            TargetType::class,
            $createStruct,
            [
                'target_type' => $targetType,
                'action' => $this->generateUrl(
                    'nglayouts_admin_layout_resolver_target_form_create',
                    [
                        'ruleId' => $rule->id->toString(),
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
            $this->layoutResolverService->addTarget($rule, $createStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $rule->id,
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
