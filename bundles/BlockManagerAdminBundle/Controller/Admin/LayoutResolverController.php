<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class LayoutResolverController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Repository
     */
    protected $repository;

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
     * @param \Netgen\BlockManager\API\Repository $repository
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface $targetTypeRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface $conditionTypeRegistry
     */
    public function __construct(
        Repository $repository,
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        $this->repository = $repository;
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
     * Creates a new rule.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createRule()
    {
        $createdRule = $this->layoutResolverService->createRule(
            $this->layoutResolverService->newRuleCreateStruct()
        );

        $createdRule = $this->layoutResolverService->publishRule(
            $createdRule
        );

        return $this->renderRule($createdRule);
    }

    /**
     * Enables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enableRule(Rule $rule)
    {
        $this->layoutResolverService->enableRule($rule);

        return $this->renderRule(
            $this->layoutResolverService->loadRule($rule->getId())
        );
    }

    /**
     * Disables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function disableRule(Rule $rule)
    {
        $this->layoutResolverService->disableRule($rule);

        return $this->renderRule(
            $this->layoutResolverService->loadRule($rule->getId())
        );
    }

    /**
     * Creates a new rule draft from published state.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If an error occurred
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createRuleDraft(Rule $rule)
    {
        $ruleDraft = null;

        try {
            $ruleDraft = $this->layoutResolverService->loadRuleDraft($rule->getId());
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $this->repository->beginTransaction();

        try {
            if ($ruleDraft instanceof RuleDraft) {
                $this->layoutResolverService->discardDraft($ruleDraft);
            }

            $createdDraft = $this->layoutResolverService->createDraft($rule);

            $this->repository->commitTransaction();

            return $this->renderRule($createdDraft);
        } catch (Exception $e) {
            $this->repository->rollbackTransaction();

            throw new BadStateException('rule', $e->getMessage());
        }
    }

    /**
     * Discards a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function discardRuleDraft(RuleDraft $rule)
    {
        $this->layoutResolverService->discardDraft($rule);

        $publishedRule = $this->layoutResolverService->loadRule(
            $rule->getId()
        );

        return $this->renderRule($publishedRule);
    }

    /**
     * Publishes a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function publishRuleDraft(RuleDraft $rule)
    {
        $publishedRule = $this->layoutResolverService->publishRule($rule);

        return $this->renderRule($publishedRule);
    }

    /**
     * Deletes a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteRule(Rule $rule)
    {
        $this->layoutResolverService->deleteRule($rule);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Displays the target create form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param string $type
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function targetCreateForm(RuleDraft $rule, $type, Request $request)
    {
        $targetType = $this->targetTypeRegistry->getTargetType($type);
        $createStruct = $this->layoutResolverService->newTargetCreateStruct($type);

        $form = $this->createForm(
            TargetType::class,
            $createStruct,
            array(
                'targetType' => $targetType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_target_form_create',
                    array(
                        'ruleId' => $rule->getId(),
                        'type' => $type,
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

            return $this->renderRule(
                $this->layoutResolverService->loadRuleDraft(
                    $rule->getId()
                )
            );
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
            $target->getType()
        );

        $updateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $updateStruct->value = $target->getValue();

        $form = $this->createForm(
            TargetType::class,
            $updateStruct,
            array(
                'targetType' => $targetType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_target_form_edit',
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

            return $this->renderRule(
                $this->layoutResolverService->loadRuleDraft(
                    $target->getRuleId()
                )
            );
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_VIEW,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Deletes a rule target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft $target
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteTarget(TargetDraft $target)
    {
        $this->layoutResolverService->deleteTarget($target);

        return $this->renderRule(
            $this->layoutResolverService->loadRuleDraft(
                $target->getRuleId()
            )
        );
    }

    /**
     * Displays the condition create form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param string $type
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function conditionCreateForm(RuleDraft $rule, $type, Request $request)
    {
        $conditionType = $this->conditionTypeRegistry->getConditionType($type);
        $createStruct = $this->layoutResolverService->newConditionCreateStruct($type);

        $form = $this->createForm(
            ConditionType::class,
            $createStruct,
            array(
                'conditionType' => $conditionType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_condition_form_create',
                    array(
                        'ruleId' => $rule->getId(),
                        'type' => $type,
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

            return $this->renderRule(
                $this->layoutResolverService->loadRuleDraft(
                    $rule->getId()
                )
            );
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
            $condition->getType()
        );

        $updateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $updateStruct->value = $condition->getValue();

        $form = $this->createForm(
            ConditionType::class,
            $updateStruct,
            array(
                'conditionType' => $conditionType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_condition_form_edit',
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

            return $this->renderRule(
                $this->layoutResolverService->loadRuleDraft(
                    $condition->getRuleId()
                )
            );
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_VIEW,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Deletes a rule condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft $condition
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCondition(ConditionDraft $condition)
    {
        $this->layoutResolverService->deleteCondition($condition);

        return $this->renderRule(
            $this->layoutResolverService->loadRuleDraft(
                $condition->getRuleId()
            )
        );
    }

    /**
     * Renders the provided rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderRule(Rule $rule)
    {
        return $this->render(
            'NetgenBlockManagerAdminBundle:admin/layout_resolver:rule.html.twig',
            array(
                'rule' => $rule,
                'target_types' => $this->targetTypeRegistry->getTargetTypes(),
                'condition_types' => $this->conditionTypeRegistry->getConditionTypes(),
            )
        );
    }
}
