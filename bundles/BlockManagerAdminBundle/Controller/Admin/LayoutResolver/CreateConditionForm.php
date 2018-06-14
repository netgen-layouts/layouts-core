<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateConditionForm extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    private $conditionTypeRegistry;

    public function __construct(
        LayoutResolverService $layoutResolverService,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Displays the condition create form.
     */
    public function __invoke(Rule $rule, string $type, Request $request): ViewInterface
    {
        $conditionType = $this->conditionTypeRegistry->getConditionType($type);
        $createStruct = $this->layoutResolverService->newConditionCreateStruct($type);

        $form = $this->createForm(
            ConditionType::class,
            $createStruct,
            [
                'condition_type' => $conditionType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_condition_form_create',
                    [
                        'ruleId' => $rule->getId(),
                        'type' => $type,
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->addCondition($rule, $createStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $rule->getId()
                ),
                ViewInterface::CONTEXT_ADMIN
            );
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_ADMIN,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
