<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LayoutResolverController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface $targetTypeRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface $conditionTypeRegistry
     */
    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Displays the index page of layout resolver admin interface.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render(
            'NetgenBlockManagerAdminBundle:admin/layout_resolver:index.html.twig',
            array(
                'rules' => $this->layoutResolverService->loadRules(),
                'target_types' => $this->targetTypeRegistry->getTargetTypes(),
                'condition_types' => $this->conditionTypeRegistry->getConditionTypes(),
            )
        );
    }

    /**
     * Displays the condition create form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param string $identifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function conditionCreateForm(RuleDraft $rule, $identifier, Request $request)
    {
        $conditionType = $this->conditionTypeRegistry->getConditionType($identifier);
        $createStruct = $this->layoutResolverService->newConditionCreateStruct($identifier);

        $form = $this->createForm(
            ConditionType::class,
            $createStruct,
            array(
                'conditionType' => $conditionType,
                'action' => $this->generateUrl(
                    'netgen_block_manager_admin_layout_resolver_condition_form_create',
                    array(
                        'ruleId' => $rule->getId(),
                        'identifier' => $identifier,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->buildView($form);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->addCondition($rule, $createStruct);

            return new Response(null, Response::HTTP_CREATED);
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_VIEW,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays the condition edit form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft $condition
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If condition type does not exist.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function conditionEditForm(ConditionDraft $condition, Request $request)
    {
        $conditionType = $this->conditionTypeRegistry->getConditionType(
            $condition->getIdentifier()
        );

        $updateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $updateStruct->value = $condition->getValue();

        $form = $this->createForm(
            ConditionType::class,
            $updateStruct,
            array(
                'conditionType' => $conditionType,
                'action' => $this->generateUrl(
                    'netgen_block_manager_admin_layout_resolver_condition_form_edit',
                    array(
                        'conditionId' => $condition->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->buildView($form);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->updateCondition($condition, $updateStruct);

            return $this->buildView($form);
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_VIEW,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays the target create form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param string $identifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function targetCreateForm(RuleDraft $rule, $identifier, Request $request)
    {
        $targetType = $this->targetTypeRegistry->getTargetType($identifier);
        $createStruct = $this->layoutResolverService->newTargetCreateStruct($identifier);

        $form = $this->createForm(
            TargetType::class,
            $createStruct,
            array(
                'targetType' => $targetType,
                'action' => $this->generateUrl(
                    'netgen_block_manager_admin_layout_resolver_target_form_create',
                    array(
                        'ruleId' => $rule->getId(),
                        'identifier' => $identifier,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->buildView($form);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->addTarget($rule, $createStruct);

            return new Response(null, Response::HTTP_CREATED);
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_VIEW,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays the target edit form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft $target
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If target type does not exist.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function targetEditForm(TargetDraft $target, Request $request)
    {
        $targetType = $this->targetTypeRegistry->getTargetType(
            $target->getIdentifier()
        );

        $updateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $updateStruct->value = $target->getValue();

        $form = $this->createForm(
            TargetType::class,
            $updateStruct,
            array(
                'targetType' => $targetType,
                'action' => $this->generateUrl(
                    'netgen_block_manager_admin_layout_resolver_target_form_edit',
                    array(
                        'targetId' => $target->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->buildView($form);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->updateTarget($target, $updateStruct);

            return $this->buildView($form);
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_VIEW,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
