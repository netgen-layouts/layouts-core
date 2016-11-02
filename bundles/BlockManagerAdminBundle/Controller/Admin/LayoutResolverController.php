<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Validator\LayoutResolverValidator;
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
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Validator\LayoutResolverValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Repository $repository
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface $targetTypeRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface $conditionTypeRegistry
     * @param \Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Validator\LayoutResolverValidator $validator
     */
    public function __construct(
        Repository $repository,
        LayoutResolverService $layoutResolverService,
        LayoutService $layoutService,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry,
        LayoutResolverValidator $validator
    ) {
        $this->repository = $repository;
        $this->layoutResolverService = $layoutResolverService;
        $this->layoutService = $layoutService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
        $this->validator = $validator;
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
            )
        );
    }

    /**
     * Creates a new rule.
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function createRule()
    {
        $createdRule = $this->layoutResolverService->createRule(
            $this->layoutResolverService->newRuleCreateStruct()
        );

        $createdRule = $this->layoutResolverService->publishRule(
            $createdRule
        );

        return $this->buildView($createdRule, array(), ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Updates the rule.
     *
     * @param int|string $ruleId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided layout does not exist
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function updateRule($ruleId, Request $request)
    {
        $layoutId = $request->request->get('layout_id');
        $layoutId = $layoutId !== null ? trim($layoutId) : null;

        $comment = $request->request->get('comment');
        $comment = $comment !== null ? trim($comment) : null;

        // null means we don't update the layout
        // empty ("" or "0") means we remove the layout from the rule
        if ($layoutId !== null && !empty($layoutId)) {
            try {
                $this->layoutService->loadLayout($layoutId);
            } catch (NotFoundException $e) {
                throw new BadStateException(
                    'layout_id',
                    sprintf(
                        'Layout with ID "%s" does not exist.',
                        $layoutId
                    ),
                    $e
                );
            }
        }

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->comment = $comment;
        $ruleUpdateStruct->layoutId = $layoutId !== null ?
            (!empty($layoutId) ? $layoutId : 0) :
            null;

        $updatedRule = $this->layoutResolverService->updateRule(
            $this->layoutResolverService->loadRuleDraft($ruleId),
            $ruleUpdateStruct
        );

        return $this->buildView($updatedRule, array(), ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Updates rule priorities.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If an error occurred
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePriorities(Request $request)
    {
        $this->validator->validatePriorities($request);

        $this->repository->beginTransaction();

        try {
            // Rules are ordered by descending priority
            // in the request variable, we reverse the list here
            // as it is way easier to increment priorities from 0
            // then decrement them (especially when we need to
            // make sure to skip rules which do not exist)
            $ruleIds = array_reverse(
                array_unique(
                    $request->request->get('rule_ids')
                )
            );

            $ruleUpdateStruct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
            $ruleUpdateStruct->priority = 0;

            foreach (array_values($ruleIds) as $ruleId) {
                try {
                    $rule = $this->layoutResolverService->loadRule($ruleId);
                } catch (NotFoundException $e) {
                    continue;
                }

                $this->layoutResolverService->updateRuleMetadata(
                    $rule,
                    $ruleUpdateStruct
                );

                $ruleUpdateStruct->priority += 10;
            }

            $this->repository->commitTransaction();

            return new Response(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            $this->repository->rollbackTransaction();

            throw new BadStateException('rule', $e->getMessage());
        }
    }

    /**
     * Enables a rule.
     *
     * @param int|string $ruleId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function enableRule($ruleId, Request $request)
    {
        $returnRule = $this->layoutResolverService->enableRule(
            $this->layoutResolverService->loadRule($ruleId)
        );

        if ($request->query->get('draft') === 'true') {
            $returnRule = $this->layoutResolverService->loadRuleDraft($ruleId);
        }

        return $this->buildView($returnRule, array(), ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Disables a rule.
     *
     * @param int|string $ruleId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function disableRule($ruleId, Request $request)
    {
        $returnRule = $this->layoutResolverService->disableRule(
            $this->layoutResolverService->loadRule($ruleId)
        );

        if ($request->query->get('draft') === 'true') {
            $returnRule = $this->layoutResolverService->loadRuleDraft($ruleId);
        }

        return $this->buildView($returnRule, array(), ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Creates a new rule draft from published state.
     *
     * @param int|string $ruleId
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If an error occurred
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function createRuleDraft($ruleId)
    {
        $ruleDraft = null;
        $rule = $this->layoutResolverService->loadRule($ruleId);

        try {
            $ruleDraft = $this->layoutResolverService->loadRuleDraft($rule->getId());
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $this->repository->beginTransaction();

        try {
            if ($ruleDraft instanceof Rule) {
                $this->layoutResolverService->discardDraft($ruleDraft);
            }

            $createdDraft = $this->layoutResolverService->createDraft($rule);

            $this->repository->commitTransaction();

            return $this->buildView($createdDraft, array(), ViewInterface::CONTEXT_ADMIN);
        } catch (Exception $e) {
            $this->repository->rollbackTransaction();

            throw new BadStateException('rule', $e->getMessage());
        }
    }

    /**
     * Discards a rule draft.
     *
     * @param int|string $ruleId
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function discardRuleDraft($ruleId)
    {
        $this->layoutResolverService->discardDraft(
            $this->layoutResolverService->loadRuleDraft($ruleId)
        );

        $publishedRule = $this->layoutResolverService->loadRule($ruleId);

        return $this->buildView($publishedRule, array(), ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Publishes a rule draft.
     *
     * @param int|string $ruleId
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function publishRuleDraft($ruleId)
    {
        $publishedRule = $this->layoutResolverService->publishRule(
            $this->layoutResolverService->loadRuleDraft($ruleId)
        );

        return $this->buildView($publishedRule, array(), ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Deletes a rule.
     *
     * @param int|string $ruleId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteRule($ruleId)
    {
        $this->layoutResolverService->deleteRule(
            $this->layoutResolverService->loadRule($ruleId)
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Displays the target create form.
     *
     * @param int|string $ruleId
     * @param string $type
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function targetCreateForm($ruleId, $type, Request $request)
    {
        $rule = $this->layoutResolverService->loadRuleDraft($ruleId);

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

        if (!$form->isSubmitted()) {
            return $this->buildView($form, array(), ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->addTarget($rule, $createStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $rule->getId()
                ),
                array(),
                ViewInterface::CONTEXT_ADMIN
            );
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_ADMIN,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays the target edit form.
     *
     * @param int|string $targetId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If target type does not exist
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function targetEditForm($targetId, Request $request)
    {
        $target = $this->layoutResolverService->loadTargetDraft($targetId);

        $targetType = $target->getTargetType();

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

        if (!$form->isSubmitted()) {
            return $this->buildView($form, array(), ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->updateTarget($target, $updateStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $target->getRuleId()
                ),
                array(),
                ViewInterface::CONTEXT_ADMIN
            );
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_ADMIN,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Deletes a rule target.
     *
     * @param int|string $targetId
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function deleteTarget($targetId)
    {
        $target = $this->layoutResolverService->loadTargetDraft($targetId);

        $this->layoutResolverService->deleteTarget($target);

        return $this->buildView(
            $this->layoutResolverService->loadRuleDraft(
                $target->getRuleId()
            ),
            array(),
            ViewInterface::CONTEXT_ADMIN
        );
    }

    /**
     * Displays the condition create form.
     *
     * @param int|string $ruleId
     * @param string $type
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function conditionCreateForm($ruleId, $type, Request $request)
    {
        $rule = $this->layoutResolverService->loadRuleDraft($ruleId);

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

        if (!$form->isSubmitted()) {
            return $this->buildView($form, array(), ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->addCondition($rule, $createStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $rule->getId()
                ),
                array(),
                ViewInterface::CONTEXT_ADMIN
            );
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_ADMIN,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays the condition edit form.
     *
     * @param int|string $conditionId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If condition type does not exist
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function conditionEditForm($conditionId, Request $request)
    {
        $condition = $this->layoutResolverService->loadConditionDraft($conditionId);

        $conditionType = $condition->getConditionType();

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

        if (!$form->isSubmitted()) {
            return $this->buildView($form, array(), ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->updateCondition($condition, $updateStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $condition->getRuleId()
                ),
                array(),
                ViewInterface::CONTEXT_ADMIN
            );
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_ADMIN,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Deletes a rule condition.
     *
     * @param int|string $conditionId
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function deleteCondition($conditionId)
    {
        $condition = $this->layoutResolverService->loadConditionDraft($conditionId);

        $this->layoutResolverService->deleteCondition($condition);

        return $this->buildView(
            $this->layoutResolverService->loadRuleDraft(
                $condition->getRuleId()
            ),
            array(),
            ViewInterface::CONTEXT_ADMIN
        );
    }
}
