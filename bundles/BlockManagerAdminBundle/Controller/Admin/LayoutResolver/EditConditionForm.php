<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditConditionForm extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Displays the condition edit form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function __invoke(Condition $condition, Request $request)
    {
        $conditionType = $condition->getConditionType();

        $updateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $updateStruct->value = $condition->getValue();

        $form = $this->createForm(
            ConditionType::class,
            $updateStruct,
            [
                'condition_type' => $conditionType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_condition_form_edit',
                    [
                        'conditionId' => $condition->getId(),
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->updateCondition($condition, $updateStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $condition->getRuleId()
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
